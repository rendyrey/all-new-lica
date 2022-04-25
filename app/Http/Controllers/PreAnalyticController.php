<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;

class PreAnalyticController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.pre_analytics.index', $data);
    }

    public function datatable()
    {
        $model = \App\Transaction::query();
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function datatableTest($roomClass)
    {
        $model = \App\TestPreAnalyticsView::where('class', $roomClass)
            ->orWhere('class', '0')
            ->orWhereNull('class');
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function datatableSelectTest(Request $request, $roomClass, $withoutIds)
    {
        $model = \App\TestPreAnalyticsView::where(function ($q) use ($roomClass) {
            $q->where('class', $roomClass)->orWhere('class', '0')->orWhereNull('class');
        })->whereNotIn('unique_id', explode(',', $withoutIds));

        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();
            $requestData['created_time'] = date('Y-m-d H:i:s');
            
            if (!$request->patient_id) {
                // create new patient if user choose the add new patient
                $new_patient = \App\Patient::create($request->all());
                $requestData['patient_id'] = $new_patient->id;
            }

            $requestData['memo'] = $request->diagnosis;
            $transaction = \App\Transaction::create($requestData);
            $transactionId = $transaction->id;

            $this->createTransactionTests($transactionId, $requestData);

            DB::commit();
            return response()->json(['message' => $requestData]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function createTransactionTests($transactionId, $requestData)
    {
        $testUniqueIds = explode(',', $requestData['selected_test_ids']);
        
        $tests = \App\TestPreAnalyticsView::whereIn('unique_id', $testUniqueIds);

        if ($tests->count() > 0) {
            $inputData = $requestData;
            
            foreach($tests->get() as $test) {
                $inputData['transaction_id'] = $transactionId;
                $inputData['price_id'] = $test->price_id;
                $inputData['group_id'] = $test->group_id;
                $inputData['type'] = $test->type;
                $inputData['test_id'] = NULL;
                $inputData['package_id'] = NULL;
                switch($test->type) {
                    case 'single':
                        $inputData['test_id'] = $test->id;
                        break;
                    case 'package':
                        $inputData['package_id'] = $test->id;
                        break;
                    default:
                }

                \App\TransactionTest::create($inputData);
            }
        }
        
    }
}
