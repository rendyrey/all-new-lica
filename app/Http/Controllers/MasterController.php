<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;
use DB;

class MasterController extends Controller
{
    protected $masters = [
        'patient' => 'App\Patient',
        'group' => 'App\Group',
        'analyzer' => 'App\Analyzer',
        'specimen' => 'App\Specimen'
    ];

    protected $titles = [
        'patient' => 'Master Patient',
        'group' => 'Master Group',
        'analyzer' => 'Master Analyzer',
        'specimen' => 'Master Specimen'
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
            
            $data['title'] = $this->titles[$masterData]; // the title of the table
            $data['masterData'] = $masterData; // the master model in string
            // dd($data);
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

            $this->masters[$masterData]::create($this->mapInputs($masterData, $request));

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

            return response()->json(['message' => ucwords($masterData) . ' updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function delete($masterData, $id)
    {
        try {
            $this->masters[$masterData]::findOrFail($id)->delete();

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
        // $count = $this->masters[$masterData]::count();
        return DataTables::of($this->masters[$masterData]::query())
        // ->setTotalRecords(10) 
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
            case 'patient':
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
