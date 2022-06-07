<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class AnalyticController extends Controller
{
    const STATUS = 1;
    const RESULT_STATUS_NORMAL = 1;
    const RESULT_STATUS_LOW = 2;
    const RESULT_STATUS_HIGH = 3;
    const RESULT_STATUS_ABNORMAL = 4;
    const RESULT_STATUS_CRITICAL = 5;
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
            $model = \App\Transaction::selectRaw('transactions.*, transactions.id as t_id')->where('created_time', '>=', $from)
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
        $model = \App\Transaction::selectRaw('transactions.*, transactions.id as t_id')->where('created_time', '>=', $from)
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
        $model = \App\TransactionTest::selectRaw('transaction_tests.*, transaction_tests.id as tt_id, results.result as res_label')
            ->leftJoin('tests','tests.id','transaction_tests.test_id')
            ->leftJoin('groups','groups.id','tests.group_id')
            ->leftJoin('results','results.id', 'result_label')
            ->where('transaction_id', $transactionId)
            ->orderBy('groups.id','asc')
            ->orderBy('tests.sequence', 'asc')
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
            return response()->json(['message' => $e->getMessage()], 400);
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

            $status = $this->checkResultStatus($patient, $range, $request);

            switch ($status) {
                case 'normal':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                    break;
                case 'low':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_LOW;
                    break;
                case 'high':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_HIGH;
                    break;
                case 'critical':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                    break;
                default:
                    $transactionTest->result_status = 0;
            }

            $transactionTest->save();
            $this->updateIsCriticalStatus($transactionTest->transaction_id);
            DB::commit();

            return response()->json(['age' => $ageInDays, 'label' => $transactionTest->result_status]);
            // $range = \App\Range::where('test_id')
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 400);
            DB::rollback();
        }
    }

    private function checkResultStatus($patient, $range, $request)
    {
        $status = '';
        if ($patient->gender == 'M') {
            if ($request->result >= $range->min_male_ref && $request->result <= $range->max_male_ref) {
                $status = 'normal';
            } else if ($request->result < $range->min_crit_male || $request->result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($request->result < $range->min_male_ref) {
                $status = 'low';
            } else if ($request->result > $range->max_male_ref) {
                $status = 'high';
            }
        } else {
            if ($request->result >= $range->min_female_ref && $request->result <= $range->max_female_ref) {
                $status = 'normal';
            } else if ($request->result < $range->min_crit_female || $request->result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($request->result < $range->min_female_ref) {
                $status = 'abnormal';
            } else if ($request->result > $range->max_female_ref) {
                $status = 'high';
            }
        }

        return $status;
    }

    private function updateIsCriticalStatus($transactionId)
    {
        $transaction = \App\Transaction::findOrFail($transactionId);
        $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->get();

        $isCriticalExists = 0;
        foreach($transactionTest as $value) {
            if ($value->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                $isCriticalExists = 1;
                break;
            }
        }

        if ($isCriticalExists) {
            $transaction->is_critical = true;
        } else {
            $transaction->is_critical = false;
        }

        $transaction->save();
    }

    public function updateResultLabel($transactionTestId, Request $request)
    {
        try {
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            $transactionTest->result_label = $request->input('result');

            if ($request->input('result')) {
                $result = \App\Result::where('id', $request->input('result'))->first();
                // $transactionTest->result_text = $result ? $result->result : '';
                switch ($result->status) {
                    case 'normal':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                        break;
                    case 'abnormal':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                        break;
                    case 'critical':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                        break;
                }
            } else {
                $transactionTest->result_status = null;
            }
            $transactionTest->save();

            return response()->json(['message' => 'success', 'label' => $transactionTest->result_status]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateResultDescription($transactionTestId, Request $request)
    {
        try {
            // $result = \App\Result::where('id')
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            $transactionTest->result_text = $request->input('result');
            $transactionTest->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function verifyAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach($transactionTests as $value) {
                if ($value->result_number || $value->result_label || $value->result_text) {
                    $value->verify = true;
                    $value->verify_by = $user->id;
                    $value->verify_time = $now;
                    $value->save();
                }
            }

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function verifyTest(Request $request, $transactionTestId)
    {
        try {
            $user = Auth::user();

            $transactionTest = \App\TransactionTest::findOrFail($transactionTestId);
            if (!$transactionTest->result_text && !$transactionTest->result_number && !$transactionTest->result_label && $request->value == 1) {
                throw new \Exception("Unable to verify because result has not been set");
            }

            $transactionTest->verify = $request->value;
            $transactionTest->verify_by = $user->id;
            $transactionTest->verify_time = Carbon::now();

            if ($request->value == 0) {
                $transactionTest->validate = 0;
                $transactionTest->verify_by = null;
                $transactionTest->verify_time = null;
                $transactionTest->validate_by = null;
                $transactionTest->validate_time = null;
            }
            
            $transactionTest->save();

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function unverifyAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->update([
                'verify' => false,
                'verify_by' => null,
                'verify_time' => null,
                'validate' => 0,
                'validate_by' => null,
                'validate_time' => null
            ]);

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function validateAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach($transactionTests as $value){
                if ($value->verify) {
                    $value->validate = true;
                    $value->validate_by = $user->id;
                    $value->validate_time = $now;
                    $value->save();
                }
            }
            
            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function validateTest(Request $request, $transactionTestId)
    {
        try {
            $user = Auth::user();

            $transactionTest = \App\TransactionTest::findOrFail($transactionTestId);
            $transactionTest->validate = $request->value;
            if ($request->value == 0) {
                $transactionTest->validate_by = null;
                $transactionTest->validate_time = null;
            }

            $transactionTest->validate_by = $user->id;
            $transactionTest->validate_time = Carbon::now();
            
            $transactionTest->save();
            
            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function unvalidateAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)
                ->where('verify', 1)->update([
                    'validate' => false,
                    'validate_by' => null,
                    'validate_time' => null
                ]);
            
            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateTestMemo(Request $request)
    {
        try {
            $transactionTestId = $request->transaction_test_id;
            $memo = $request->memo;

            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();

            $transactionTest->memo_test = $memo;
            $transactionTest->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateMemoResult(Request $request)
    {
        try {
            $transactionId = $request->transaction_id;
            $memoResult = $request->memo_result;

            $transaction = \App\Transaction::where('id', $transactionId)->first();
            $transaction->memo_result = $memoResult;
            $transaction->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function checkCriticalTest($transactionId)
    {
        $transactionTests = \App\TransactionTest::selectRaw('transaction_tests.*, results.result as res_label')
            ->leftJoin('results','results.id','transaction_tests.result_label')->where('transaction_id', $transactionId)
            ->where('result_status', AnalyticController::RESULT_STATUS_CRITICAL)
            ->whereIn('verify', [0, null])->get();

        if (count($transactionTests) > 0) {
            return response()->json(['data' => $transactionTests, 'exists' => true]);
        } else {
            return response()->json(['data' => $transactionTests, 'exists' => false]);
        }
        
    }

    public function reportCriticalTest(Request $request)
    {
        $criticalTestIds = explode(',', $request->transaction_test_ids);
        $reportTo = $request->report_to;
        $reportBy = $request->report_by;

        $tests = \App\TransactionTest::whereIn('id', $criticalTestIds)->update([
            'report_status' => 1,
            'report_by' => $reportBy,
            'report_to' => $reportTo
        ]);
        return response()->json(['data' => $request->all()]);
    }

    public function checkActionBtnTestStatus($transactionId)
    {
        $unverAndValAll = \App\TransactionTest::where('transaction_id', $transactionId)->where('verify', 1)->exists();
        $unvalAll = \App\TransactionTest::where('transaction_id', $transactionId)->where('validate', 1)->exists();

        return response()->json(['unver_and_val_all' => $unverAndValAll, 'unval_all' => $unvalAll]);
    }

    public function goToPostAnalytics($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);

            if ($transaction->status == '2') {
                throw new \Exception("Transaction has been moved to analytic");
            }

            if ($transaction->no_lab == '' || $transaction->no_lab == null) {
                throw new \Exception("No Lab has not been set");
            }

            $allAnalzerSet = false;
            $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach($transactionTest as $test) {
                if ($test->analyzer_id == null || $test->analyzer_id == '') {
                    throw new \Exception('Analyzer hasn\'t been set for all');
                }

                if ($test->draw == null || $test->draw == '0') {
                    throw new \Exception('Draw hasn\'t set for all specimen');
                }
            }

            return response()->json(['message' => 'Valid', 'valid' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'valid' => false], 400);
        }
    }
}
