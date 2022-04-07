<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;

class MasterController extends Controller
{
    protected $masters = [
        'test' => 'App\Test',
        'patient' => 'App\Patient'
    ];

    protected $titles = [
        'test' => 'Master Test',
        'patient' => 'Master Patient'
    ];

    protected $tableIds = [
        'test' => 'master-test-table',
        'patient' => 'master-patient-table'
    ];

    /**
     * The index function for all master pages, route: '/master/*'
     * 
     * @param string $masterData The model of the master
     * @return view
     */
    public function index($masterData)
    {
        try{
            // if the param of masterData is not listed in $masters, thrown 404 exception
            if (!isset($this->masters[$masterData])) {
                throw new \Exception("Not Found");
            }
            $data['title'] = $this->titles[$masterData]; // the title of the table
            $data['tableId'] = $this->tableIds[$masterData]; // the table id
            $data['masterData'] = $masterData; // the master model in string
        
            return view('masters.'.$masterData, $data);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function create($masterData, Request $request)
    {
        // return $request->name;
        try {
            $this->masters[$masterData]::create($request->all());

            return response()->json(['status' => true, 'message' => 'Patient added successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
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
        return $this->{$masterData}();
    }

    /**
     * DataTable query for patient model, will call on '/master/patient' route
     * 
     * @return DataTables
     */
    private function patient()
    {
        return DataTables::of(\App\Patient::query())
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    /**
     * DataTable query for test model, will call on '/master/test' route
     * 
     * @return DataTables
     */
    private function test()
    {
        return DataTables::of(\App\Test::query())
        ->addIndexColumn()
        ->make(true);
    }

    
}
