<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;

class AnalyticController extends Controller
{
    const STATUS = 1;
    /**
     * 
     */
    public function index()
    {
        $data['title'] = 'Analytics';
        return view('dashboard.analytics.index', $data);
    }

    /**
     * 
     */
    public function datatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::where('created_time', '>=', $from)
                ->where('created_time', '<=', $to)
                ->where('status', AnalyticController::STATUS)
                ->orderBy('cito','desc');
            
            return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->where('status', AnalyticController::STATUS)
            ->orderBy('cito','desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * 
     */
    public function datatableTest($transactionId)
    {
        $model = \App\TransactionTest::selectRaw('transaction_tests.*, transaction_tests.id as tt_id')
            ->leftJoin('tests','tests.id','transaction_tests.test_id')
            ->leftJoin('groups','groups.id','tests.group_id')
            ->where('transaction_id', $transactionId)
            ->orderBy('groups.id','asc')
            ->get();

        $transaction = \App\Transaction::findOrFail($transactionId);
        
        // return Datatables::of($model)
        //     ->addIndexColumn()
        //     ->escapeColumns([])
        //     ->make(true);
        $data['table'] = $model;
        $data['transaction'] = $transaction;
        $html = view('dashboard.analytics.transaction-test-table', $data)->render();
        return response()->json(['html' => $html, 'data' => $model]);
    }

    /**
     * 
     */
    public function resultLabel($testId)
    {
        $checkMasterRange = \App\Range::where('test_id', $testId)->exists();
        $test = \App\Test::where('id', $testId)->first();

        if ($test->range_type == 'label') {
            $results = \App\Result::where('test_id', $testId)->get();
            $options = '<option value=""></option>';
            foreach($results as $result) {
                $options .= '<option value="'.$result->result.'">'.$result->result.'</option>';
            }
    
            return $options;
        } else if ($test->range_type == 'number' && !$checkMasterRange) {
            return response()->json(['message' => 'PLEASE SET RESULT RANGE']);
        }
    }

    /**
     * 
     */
    public function transaction($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);
            
            return response()->json(['message' => 'SUCCESS', 'data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMesage()], 400);
        }
    }

    public function updateResultNumber($transactionTestId, Request $request)
    {
        try {
            $status = '';

            DB::beginTransaction();
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            $transactionTest->result_number = $request->result;

            $patient = $transactionTest->transaction->patient;
            $bornDate = $patient->birthdate;
            
            $ageInDays = Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(Carbon::now());

            $range = \App\Range::where('test_id', $transactionTest->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

            if (!$range) {
                throw new \Exception("The Range ref. doesn't exist");
            }

            if ($patient->gender == 'M') {
                if ($request->result >= $range->min_male_ref && $request->result <= $range->max_male_ref) {
                    $status = 'normal';
                } else if ($request->result < $range->min_crit_male || $request->result > $range->max_crit_male) {
                    $status = 'critical';
                } else if ($request->result < $range->min_male_ref || $request->result > $range->max_male_ref) {
                    $status = 'abnormal';
                }
            } else {
                if ($patient->gender == 'F') {
                    if ($request->result >= $range->min_female_ref && $request->result <= $range->max_female_ref) {
                        $status = 'normal';
                    } else if ($request->result < $range->min_crit_female || $request->result > $range->max_crit_male) {
                        $status = 'critical';
                    } else if ($request->result < $range->min_female_ref || $request->result > $range->max_female_ref) {
                        $status = 'abnormal';
                    }
                }
            }

            if ($status == 'normal') {
                $transactionTest->result_status = 1;
            } else if ($status == 'abnormal') {
                $transactionTest->result_status = 2;
            } else {
                $transactionTest->result_status = 3;
            }

            $transactionTest->save();
            DB::commit();

            return response()->json(['age' => $ageInDays]);
            // $range = \App\Range::where('test_id')
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 400);
            DB::rollback();
        }
    }

    public function updateResultLabel($transactionTestId, Request $request)
    {
        try {
            $result = \App\Result::where('id', $request->input('result'))->first();
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            $transactionTest->result_label = $request->input('result');
            $transactionTest->result_text = $result ? $result->result : '';
            $transactionTest->save();

            return response()->json(['message' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
