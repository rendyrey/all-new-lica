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
        'patient' => 'App\Patient',
        'group' => 'App\Group',
        'analyzer' => 'App\Analyzer',
        'specimen' => 'App\Specimen',
        'doctor' => 'App\Doctor',
        'insurance' => 'App\Insurance'
    ];
    
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
        try {
            $validator = $this->masters[$masterData]::validate($request);
            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            $data = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));

            $this->logActivity(
                "Create $masterData with ID $data->id",
                json_encode($data)
            );
            return response()->json(['message' => ucwords($masterData) . ' added successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
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
        try {
            $validator = $this->masters[$masterData]::validate($request);
            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            $this->masters[$masterData]::findOrFail($request->id)
                ->update($this->mapInputs($masterData, $request));

            $this->logActivity(
                "Update $masterData with ID $request->id",
                json_encode($request->except(['_method','_token']))
            );

            return response()->json(['message' => ucwords($masterData) . ' updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function delete($masterData, $id)
    {
        try {
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

    /**
     * Preparing the data for the DataTables
     *
     * @param string $masterData The model of the master
     * @return json of DataTables
     */
    public function datatable($masterData)
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
        return DataTables::of($this->masters[$masterData]::query())
        ->setTotalRecords($count)
        // ->skipTotalRecords()
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function selectOptions($masterData, $searchKey, Request $request)
    {
        try {
            $data = $this->masters[$masterData]::selectRaw('id, '.$searchKey.' as name')
                ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                ->take(150)->get();
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function mapInputs($masterData, $request)
    {
        $data = array();
        switch ($masterData) {
            case 'patient': // patient need custom mapping, so we must to add here
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['phone'] = $request->phone;
                $data['medrec'] = $request->medrec;
                $data['birthdate'] = $request->birthdate_submit;
                $data['gender'] = $request->gender;
                $data['address'] = $request->address;
                break;
            default:
                return $request->all();
        }

        return $data;
    }
}
