@php
    
    function labelType($value, $transactionTestId, $testId, $tabIndex) {
        $results = \App\Result::where('test_id', $testId)->get();
        $options = '<option value=""></option>';
        foreach($results as $result) {
            $selected = $value->result_label == $result->id ? 'selected' : '';
            $options .= '<option value="'.$result->id.'" "'.$selected.'">'.$result->result.'</option>';
        }
        $input = '
            <select tabindex="100" id="select-result-label-'.$transactionTestId.'" data-transaction-test-id="'.$transactionTestId.'" data-transaction-id="'.$value->transaction_id.'" data-control="select2" data-placeholder="Select label" class="select select-result-label form-select form-select-sm form-select-solid my-0 me-4">
                '.$options.'
            </select>
        ';
        // $input = `<b>Rendy</b>`;
        // $("#select-result-label-"+item.tt_id).select2({allowClear:true});
        return $input;
    }

    function numberType($value, $transactionTestId, $testId, $tabIndex) {
        $checkMasterRange = \App\Range::where('test_id', $testId)->exists();
        if (!$checkMasterRange) {
            return 'PLEASE SET RESULT RANGE';
        }
        $input = '
            <input type="text" id="rendy" class="form-control form-control-sm result-number" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$transactionTestId.'" tabindex="'.$tabIndex.'" value="'.$value->result_number.'">
        ';
        return $input;
    }

    function descriptionType($value, $testId, $tabIndex) {
        $range = \App\Range::where('test_id', $testId)->first();
    }

    function normalRef($gender, $testId, $tabIndex) {
        $range = \App\Range::where('test_id', $testId)->first();

        if ($range) {
            if ($gender == 'M') {
                return $range->min_male_ref . '-' . $range->max_male_ref;
            }
            return $range->min_female_ref . '-' . $range->max_female_ref;
        }
        return '-';
    }

    function labelInfo($value, $gender, $tabIndex) {
        return $value->result_status;
    }

    function verifyCheckbox() {

    }

    function validateCheckbox() {

    }

@endphp
@if(isset($table))
    @php
        $currentGroupName = ''
    @endphp

    @foreach($table as $key => $value)
        @php
            $testId = $value->id;
            $groupName = $value->test->group->name;
            if ($currentGroupName != $groupName) {
                $currentGroupoName = $groupName;
            }
            $key++;
        @endphp
        @if($currentGroupName != $groupName)
        <tr>
            <td colspan="6"><h5>{{$groupName}}</h5></td>
        </tr>
        @php
            $currentGroupName = $groupName;
        @endphp
        @endif
        <tr>
            
            <td style="border-right: 1px solid grey">{{$value->test->name}}</td>
            <td style="border-right: 1px solid grey">
                @if ($value->test->range_type == 'label')
                    {!! labelType($value, $value->id, $value->test_id, $key) !!}
                @elseif($value->test->range_type == 'number')
                    {!! numberType($value, $value->id, $value->test_id, $key) !!}
                @endif
            </td>
            <td style="border-right: 1px solid grey">{!! normalRef($transaction->patient->gender, $value->test_id, $key) !!}</td>
            <td style="border-right: 1px solid grey">{!! labelInfo($value, $transaction->patient->gender, $key) !!}</td>
            <td style="border-right: 1px solid grey">{!! verifyCheckbox() !!}</td>
            <td>-</td>
        </tr>
   @endforeach
@endif

<script>
    $(".result-number").on('blur', function(e) {
        const value = $(this).val();
        const transactionTestId = $(this).data('transaction-test-id');
        const transactionId = $(this).data('transaction-id');
        const component = $(this);
        $.ajax({
            url: baseUrl('analytics/update-result-number/'+transactionTestId),
            type: 'put',
            data: { result : value },
            success: function(res) {
                toastr.success("Success update result number");
                refreshPatientDatatables(transactionId);
            },
            error: function(request, status, error) {
                toastr.error(request.responseJSON.message);
                component.focus();
            }
        });
    });

    $(".select-result-label").on('change', function(e) {
        // alert("HI");
    });


    // if ( ! $.fn.DataTable.isDataTable( '.transaction-test-table' ) ) {
    //     DatatableTest.init();
    // } else {
    //     DatatableTest.destroy();
    //     DatatableTest.init();
    // }
</script>