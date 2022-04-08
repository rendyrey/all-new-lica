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
        try {
            // if the param of masterData is not listed in $masters, thrown 404 exception
            if (!isset($this->masters[$masterData])) {
                throw new \Exception("Not Found");
            }

            $data['title'] = $this->titles[$masterData]; // the title of the table
            $data['tableId'] = $this->tableIds[$masterData]; // the table id
            $data['masterData'] = $masterData; // the master model in string
            $data['page'] = "Master ".ucwords($masterData);
        
            return view('dashboard.masters.'.$masterData, $data);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function create($masterData, Request $request)
    {
        try {
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
        return DataTables::of($this->masters[$masterData]::query())
        // ->setTotalRecords(1000)
        // ->skipTotalRecords()
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
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
            case 'test':
                break;
        }

        return $data;
    }
}
