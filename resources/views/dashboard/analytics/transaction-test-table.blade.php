@php
    
    function labelType($value, $transactionTestId, $testId, $tabIndex) {
        $results = \App\Result::where('test_id', $testId)->get();
        $options = '<option value=""></option>';
        foreach($results as $result) {
            $selected = $value->result_label == $result->id ? 'selected' : '';
            $options .= '<option value="'.$result->id.'" "'.$selected.'">'.$result->result.'</option>';
        }
        $input = '
            <select tabindex="'.$tabIndex.'" id="select-result-label-'.$transactionTestId.'" data-transaction-test-id="'.$transactionTestId.'" data-transaction-id="'.$value->transaction_id.'" data-control="select2" data-placeholder="Select label" class="select select-result-label form-select form-select-sm form-select-solid my-0 me-4">
                '.$options.'
            </select>
        ';

        return $input;
    }

    function numberType($value, $transactionTestId, $testId, $tabIndex) {
        $checkMasterRange = \App\Range::where('test_id', $testId)->exists();
        if (!$checkMasterRange) {
            return 'PLEASE SET RESULT RANGE';
        }
        $input = '
            <input type="text" class="form-control form-control-sm result-number" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$value->id.'" tabindex="'.$tabIndex.'" value="'.$value->result_number.'">
        ';
        return $input;
    }

    function descriptionType($value, $testId, $tabIndex) {
        // $range = \App\Range::where('test_id', $testId)->first();
        $result = $value->result_text;
        $input = '
            <textarea class="result-description form-control" tabindex="'.$tabIndex.'" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$value->id.'">'.$result.'</textarea>
        ';

        return $input;
    }

    function normalRef($patient, $value, $tabIndex) {
        // nilai normal text description nggak ada
        if ($value->range_type == 'description') {
            return '-';
        }

        $bornDate = $patient->birthdate;
        $ageInDays = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(\Illuminate\Support\Carbon::now());
        $range = \App\Range::where('test_id', $value->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

        if ($range) {
            if ($patient->gender == 'M') {
                // return $range->min_male_ref . '-' . $range->max_male_ref;
                return $range->normal_male;
            }
            // return $range->min_female_ref . '-' . $range->max_female_ref;
            return $range->normal_female;
        }

        return '-';
    }

    function labelInfo($result_status) {
        switch ($result_status) {
            case 1: // normal
                return '<span class="badge badge-circle badge-success">N</span>';
            case 2: // low
                return '<span class="badge badge-circle badge-warning">L</span>';
            case 3: // high
                return '<span class="badge badge-circle badge-warning">H</span>';
            case 4: // critical
                return '<span class="badge badge-circle badge-danger">C</span>';
        }
    }

    function verifyCheckbox($value) {
        $checked = $value->verify ? 'checked' : '';
        return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                <input data-transaction-test-id="'.$value->id.'" data-transaction-id="'.$value->transaction_id.'" class="form-check-input verify-checkbox" type="checkbox" value="" '.$checked.'/>
                </div>';
    }

    function validateCheckbox($value) {
        $checked = $value->validate ? 'checked' : '';
        $disabled = $value->verify ? '' : 'disabled';
        return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input '.$disabled.' data-transaction-test-id="'.$value->id.'" data-transaction-id="'.$value->transaction_id.'" class="form-check-input validate-checkbox" type="checkbox" value="" '.$checked.'/>
                </div>';
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
            $key++; // this is for tab index
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
                @elseif($value->test->range_type == 'description')
                    {!! descriptionType($value, $value->test_id, $key) !!}
                @endif
            </td>
            <td style="border-right: 1px solid grey">{!! normalRef($transaction->patient, $value, $key) !!}</td>
            <td style="border-right: 1px solid grey" id="label-info-{{$value->id}}">{!! labelInfo($value->result_status) !!}</td>
            <td style="border-right: 1px solid grey">{!! verifyCheckbox($value) !!}</td>
            <td>{!! validateCheckbox($value) !!}</td>
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
                // refreshPatientDatatables(transactionId);
                DatatableAnalytics.refreshTable();
                let label = '';
                switch (res.label) {
                    case 1: // normal
                        label = '<span class="badge badge-sm badge-circle badge-success">N</span>';
                        break;
                    case 2: // low
                        label = '<span class="badge badge-sm badge-circle badge-warning">L</span>';
                        break;
                    case 3: // high
                        label = '<span class="badge badge-sm badge-circle badge-warning">H</span>';
                        break;
                    case 4: // critical
                        label = '<span class="badge badge-sm badge-circle badge-danger">C</span>';
                        break;
                }
                $("#label-info-"+transactionTestId).html(label);
            },
            error: function(request, status, error) {
                toastr.error(request.responseJSON.message);
                component.focus();
            }
        });
    });

    $(".result-description").on('blur', function(e) {
        const value = $(this).val();
        const transactionTestId = $(this).data('transaction-test-id');
        const transactionId = $(this).data('transaction-id');
        const component = $(this);
        $.ajax({
            url: baseUrl('analytics/update-result-description/'+transactionTestId),
            type: 'put',
            data: { result : value },
            success: function(res) {
                toastr.success("Success update result description");
            },
            error: function(request, status, error) {
                toastr.error(request.responseJSON.message);
                component.focus();
            }
        });
    });


    $(".verify-checkbox").on('change', function(e) {
        const transactionTestId = $(this).data('transaction-test-id');
        const transactionId = $(this).data('transaction-id');
        const value = e.target.checked ? 1 : 0;
        const msg = value ? 'verify' : 'unverify';
        $.ajax({
            url: baseUrl('analytics/verify-test/'+transactionTestId),
            type: 'put',
            data: { value: value },
            success: function(res) {
                toastr.success("Success "+msg+" test result");
                onSelectTransaction(transactionId);
            },
            error: function(request, status, error) {
                toastr.error(request.responseJSON.message);
                onSelectTransaction(transactionId);
            }
        });
    });

    $(".validate-checkbox").on('change', function(e) {
        const transactionTestId = $(this).data('transaction-test-id');
        const value = e.target.checked ? 1 : 0;
        const msg = value ? 'validate' : 'unvalidate';
        $.ajax({
            url: baseUrl('analytics/validate-test/'+transactionTestId),
            type: 'put',
            data: { value: value },
            success: function(res) {
                toastr.success("Success "+msg+" test result");
            }
        })
    });

    // if ( ! $.fn.DataTable.isDataTable( '.transaction-test-table' ) ) {
    //     DatatableTest.init();
    // } else {
    //     DatatableTest.destroy();
    //     DatatableTest.init();
    // }
</script>