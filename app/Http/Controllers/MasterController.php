<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;
use DB;
use Illuminate\Support\Facades\Cache;

class MasterController extends Controller
{
    protected const COUNT_LIMIT_FOR_DATATABLE = 20000;
    protected $masters = [
        'test' => 'App\Test',
        'package' => 'App\Package',
        'patient' => 'App\Patient',
        'group' => 'App\Group',
        'analyzer' => 'App\Analyzer',
        'specimen' => 'App\Specimen',
        'doctor' => 'App\Doctor',
        'insurance' => 'App\Insurance',
        'price' => 'App\Price',
        'room' => 'App\Room',
        'range' => 'App\Range',
        'interfacing' => 'App\Interfacing',
        'general_code_test' => 'App\GeneralCodeTest'
    ];

    protected $masterId = null;
    
    /**
     * The index function for all master pages, route: '/master/*'
     *
     * @param string $masterData The model of the master
     * @return view
     */
    public function index($masterData)
    {
        try {
            // if the param of masterData is not listed in $masters, thrown 404 exception
            if (!isset($this->masters[$masterData])) {
                throw new \Exception("Not Found");
            }
            
            $data['masterData'] = $masterData; // the master model in string
            $data['title'] = "Master " .ucwords($masterData);

            return view('dashboard.masters.'.$masterData, $data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function create($masterData, Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->masters[$masterData]::validate($request);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            switch ($masterData) {
                case 'price':
                    $creates = $this->createPrices($request);
                    break;
                case 'test':
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                    \App\Price::create([
                        'test_id' => $createdData->id,
                        'type' => 'test',
                        'price' => 0, // set default for price that has no class
                        'class' => 0 // set default for price class that
                    ]);
                    
                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
                    break;
                case 'package':
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                     // this is for create package in particular, it will be executed if the masterData is package
                    $this->createPackageTest($createdData, $masterData, $request);
                    \App\Price::create([
                        'package_id' => $createdData->id,
                        'type' => 'package',
                        'price' => 0, // set default for price that has no class
                        'class' => 0 // set default for price class that
                    ]);

                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
                    break;
                default:
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
            }
            
           
            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", $masterData)) . ' added successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function createPackageTest($createdData, $masterData, $request)
    {
        if ($masterData != 'package') {
            return;
        }
        $data = [];
        if($request->test_ids != null) {
            $currentTime = date('Y-m-d H:i:s');
            foreach ($request->test_ids as $test_id) {
                $data[] = [
                    'test_id' => $test_id,
                    'package_id' => $createdData->id,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
            }
        }

        \App\PackageTest::insert($data);
    }

    private function createPrices($request)
    {
        $data = [];
        $currentTime = date('Y-m-d H:i:s');
        foreach ($request->class_price as $class_price) {
            $checkExistsClass = \App\Price::where('class', $class_price['class'])->where('type', $request->type);
            if ($request->type == 'test') {
                $checkExistsClass = $checkExistsClass->where('test_id', $request->test_id)->exists();
            } else {
                $checkExistsClass = $checkExistsClass->where('package_id', $request->package_id)->exists();
            }

            if ($checkExistsClass) {
                throw new \Exception("The price for that class already set!");
            }

            $data[] = [
                'class' => $class_price['class'],
                'price' => str_replace(',','',$class_price['price']),
                'test_id' => $request->type == 'test' ? $request->test_id : null,
                'type' => $request->type,
                'package_id' => $request->type == 'package' ? $request->package_id : null,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
        }

        \App\Price::insert($data);
    }

    public function edit($masterData, $id)
    {
        try {
            $data = $this->masters[$masterData]::findOrFail($id);

            return $data;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function update($masterData, Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->masters[$masterData]::validate($request);
            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            $this->masters[$masterData]::findOrFail($request->id)
                ->update($this->mapInputs($masterData, $request));

            $this->updatePackageTest($masterData, $request);

            $this->logActivity(
                "Update $masterData with ID $request->id",
                json_encode($request->except(['_method','_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", $masterData)) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function updatePackageTest($masterData, $request)
    {
        if ($masterData != 'package') {
            return;
        }

        $data = [];
        if($request->test_ids != null) {
            \App\PackageTest::where('package_id', $request->id)->delete(); // delete previous data

            $currentTime = date('Y-m-d H:i:s');
            foreach ($request->test_ids as $test_id) {
                $data[] = [
                    'test_id' => $test_id,
                    'package_id' => $request->id,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
            }
        }

        \App\PackageTest::insert($data);
    }


    public function delete($masterData, $id)
    {
        try {
            $this->validateDelete($masterData, $id);
            $data = $this->masters[$masterData]::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete $masterData with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords($masterData) . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function validateDelete($masterData, $id)
    {
        $exists = [];
        switch($masterData)
        {
            case 'test':
                $exists[] = \App\PackageTest::where('test_id', $id)->exists();
                break;
            case 'group':
                $exists[] = \App\Analyzer::where('group_id', $id)->exists();
                break;
            case 'package':
                $exists[] = \App\Price::where('package_id', $id)->exists();
                break;
            default:
                $exists[] = false;
        }
        if (in_array(true, $exists)) {
            throw new \Exception("You can't delete this data, because this data has been used");
        }
    }

    /**
     * Preparing the data for the DataTables
     *
     * @param string $masterData The model of the master
     * @param string $with The relation model of the masterData, e.g. "group,specimen" or just "group"
     * @return json of DataTables
     */
    public function datatable($masterData, $with = null)
    {
        // store count cache
        if (Cache::has($masterData.'_count')) {
            $count = Cache::get($masterData.'_count');
        } else {
            $count = $this->masters[$masterData]::count();
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put($masterData.'_count', $count, 600);
            }
        }

        // $count = $this->masters[$masterData]::count();
        $model = $this->masters[$masterData]::query();
        if ($with) {
            $withModel = explode(',', $with);
            $model = $model->with($withModel);
        }

        return DataTables::of($model)
        ->setTotalRecords($count)
        // ->skipTotalRecords()
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function rangeDatatable($testId)
    {
         if (Cache::has('range_count')) {
            $count = Cache::get('range_count');
        } else {
            $count = \App\Range::where('test_id', $testId)->count();
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put('range_count', $count, 600);
            }
        }

        $model = \App\Range::where('test_id', $testId);
        return DataTables::of($model)
        ->setTotalRecords($count)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function selectOptions($masterData, $searchKey, Request $request)
    {
        try {
            switch ($masterData) {
                case 'room':
                    $data = $this->masters[$masterData]::selectRaw("id, ".$searchKey." as name, class")
                        ->where('room', 'LIKE', '%' . $request->input('query') . '%')
                        ->take(150)->get();
                    break;
                case 'test':
                    $data = $this->masters[$masterData]::selectRaw("tests.id as id, tests.name as name, GROUP_CONCAT(class SEPARATOR ', ') as classes")
                        ->leftJoin('prices','tests.id','=','prices.test_id')
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        ->where(function($q) {
                            $q->where('prices.class', '<>', 0)->orWhereNull('prices.class');
                        })
                        ->groupBy(['tests.id','tests.name'])
                        ->take(150)->get();
                    break;
                case 'package':
                    $data = $this->masters[$masterData]::selectRaw("packages.id as id, packages.name as name, GROUP_CONCAT(class SEPARATOR ', ') as classes")
                        ->leftJoin('prices','packages.id','=','prices.test_id')
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        ->where(function($q) {
                            $q->where('prices.class', '<>', 0)->orWhereNull('prices.class');
                        })
                        ->groupBy(['packages.id','packages.name'])
                        ->take(150)->get();
                    break;
                default:
                    $data = $this->masters[$masterData]::selectRaw('id, '.$searchKey.' as name')
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        ->take(150)->get();    
            }
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function mapInputs($masterData, $request)
    {
        $data = array();
        switch ($masterData) {
            case 'test':
                if (!$request->normal_notes || (!isset($request->normal_notes)) || $request->normal_notes == null) {
                    return $request->except(['normal_notes']);
                }
                return $request->all();
            case 'room': // room need custom mapping for checkbox
                $data = $request->all();
                $data['auto_checkin'] = $request->auto_checkin == 1;
                $data['auto_draw'] = $request->auto_draw == 1;
                break;
            case 'price':
                $data = $request->all();
                $data['price'] = str_replace(',', '', $request->price);
                $data['test_id'] = $request->type == 'test' ? $request->test_id : null;
                $data['package_id'] = $request->type == 'package' ? $request->package_id : null;
                break;
            case 'package':
                $data = $request->all();
                $data['group_id'] = $request->group_id == '' ? null : $request->group_id;
                break;
            default:
                return $request->all();
        }

        return $data;
    }

    public function getTestPackage($packageIds) {
        $pIds = explode(',', $packageIds);
        $data = \App\PackageTest::whereIn('package_id', $pIds)->get();
        return response()->json($data);
    }
}
