<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;

class PreAnalyticController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.pre_analytics.index', $data);
    }

    public function datatable($startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::where('created_time', '>=', $from)->where('created_time', '<=', $to);
            
            return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
        }

        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)->where('created_time', '<=', $to);

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function datatableTransactionTest($transactionId)
    {
        $model = \App\TransactionTest::where('transaction_id', $transactionId);
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }

    public function datatableTransactionSpecimen($transactionId)
    {
        $transactionTests = \App\TransactionTest::selectRaw('test_id')->where('transaction_id', $transactionId)->get()->toArray();
        $testIds = [];
        foreach($transactionTests as $testId) {
            $testIds[] = $testId['test_id'];
        }
        
        $model = \App\Test::selectRaw('transaction_tests.transaction_id as transaction_id, GROUP_CONCAT(tests.id SEPARATOR ",") as test_ids, IFNULL(GROUP_CONCAT(transaction_tests.draw SEPARATOR ","),0) as draw, specimen_id, SUM(volume) as volume, unit')
        ->leftJoin('transaction_tests','tests.id','=','transaction_tests.test_id')
        ->where('transaction_tests.transaction_id', $transactionId)
        ->whereIn('tests.id', $testIds)->groupBy('specimen_id','unit','transaction_id');
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
            $requestData['transaction_id_label'] = $this->getTransactionIdLabel($request);
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

    public function analyzerTest($testId)
    {
        $interfacings = \App\Interfacing::where('test_id', $testId)->get();
        $options = '<option value=""></option>';
        foreach($interfacings as $interfacing) {
            $options .= '<option value="'.$interfacing->analyzer_id.'">'.$interfacing->analyzer->name.'</option>';
        }

        return $options;
    }

    public function updateAnalyzer($transactionTestId, Request $request)
    {
        try {
            \App\TransactionTest::where('id',$transactionTestId)->update(['analyzer_id' => $request->analyzer_id]);
            return response()->json(['message' => 'Success update analyzer']);
        } catch (\Exception $e) {
            return respoinse()->json(['message' => $e->getMessage(), 404]);
        }
        
    }

    public function updateDraw(Request $request)
    {
        try {
            $test_ids = explode(',', $request->test_ids);
            \App\TransactionTest::where('transaction_id', $request->transaction_id)->whereIn('test_id', $test_ids)
            ->update([
                'draw' => DB::raw('1 - draw'),
                'draw_time' => Carbon::now()
            ]);
            return response()->json(['message' => 'Success update draw status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 404]);
        }
    }

    public function drawAll($value, Request $request)
    {
        try {
            \App\TransactionTest::where('transaction_id', $request->transaction_id)
            ->update([
                'draw' => $value,
                'draw_time' => ($value) ? Carbon::now() : null
            ]);
            return response()->json(['message' => 'Success update draw status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 404]);
        }
    }

    public function isAllDrawn($transactionId)
    {
        try {
            $exists = \App\TransactionTest::where('transaction_id', $transactionId)->where('draw','0')->exists();
            return response()->json(['message' => $transactionId, 'all_drawn' => !$exists]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
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
                        \App\TransactionTest::create($inputData);
                        break;
                    case 'package':
                        $inputData['package_id'] = $test->id;
                        $this->createTransactionTestsFromPackage($inputData);
                        break;
                    default:
                }
            }
        }
        
    }

    private function createTransactionTestsFromPackage($inputData)
    {

        $tests = \App\PackageTest::where('package_id', $inputData['package_id'])->get();

        foreach($tests as $test) {
            $inputData['test_id'] = $test->test_id;
            \App\TransactionTest::create($inputData);
        }
    }

    private function getTransactionIdLabel($request)
    {
        $prefix = '';
        switch($request->type){
            case 'rawat_jalan':
                $prefix = 'RWJ';
                break;
            case 'rawat_inap':
                $prefix = 'RWI';
                break;
            case 'igd':
                $prefix = 'IGD';
                break;
            case 'rujukan':
                $prefix = 'RJK';
                break;
            default:
                $prefix = 'TRX';
        }

        $year = date('Y');
        $countExistingData = \App\Transaction::where('transaction_id_label', 'like', $prefix.$year.'%')->count();
        $countExistingData += 1;

        $trxId = str_pad($countExistingData, 7, '0', STR_PAD_LEFT);
        return $prefix.$year.$trxId;
    }
}
