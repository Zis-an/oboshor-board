@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4>Update Payroll</h4>
            <a href="{{route('payrolls.edit', $payroll->id)}}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <div class="card">
        {!! Form::open(['url' => route('payrolls.update', $payroll->id), 'method' => 'PUT']) !!}
        @csrf
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Salary</th>
                    <th>Work Days</th>
                    <th>Salary Type</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payroll->items as $index=>$item)
                    <tr>
                        <input type="hidden" value="{{$item->id}}" name="{{'items['. $index. '][id]'}}">
                        <td>{{$item->employee->name}}</td>
                        <td>
                            <input type="number" value="{{$item->salary}}" name="{{'items['. $index. '][salary]'}}"
                                   class="payroll-salary form-control">
                        </td>
                        <td>
                            <input type="number" value="{{$item->work_days}}"
                                   name="{{'items['. $index. '][work_days]'}}" class="work-days-input form-control"
                                   @if($item->salary_type === 'monthly')
                                       readonly
                                @endif
                            >
                        </td>
                        <td>
                            <input name="{{'items['. $index . '][salary_type]'}}" value="{{$item->salary_type}}"
                                   class="form-control payroll-salary-type"
                                   disabled>
                        </td>
                        <td>
                            <input type="number" name="{{'items['. $index . '][amount]'}}"
                                   readonly
                                   class="payroll-amount form-control" value="{{$item->amount}}">

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Save</button>
            </div>

        </div>
        {!! Form::close() !!}
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.work-days-input').change(function () {
                let workDays = this.value;
                let salary = $(this).closest('tr').find('.payroll-salary').val();
                let amount = salary * workDays;
                console.log({amount})
                $(this).closest('tr').find('.payroll-amount').val(amount)
            });

            $('.payroll-salary').change(function () {

                let salary = this.value;

                let workDays = $(this).closest('tr').find('.work-days-input').val();

                let salaryType = $(this).closest('tr').find('.payroll-salary-type').val();

                if (salaryType === 'monthly') {
                    $(this).closest('tr').find('.payroll-amount').val(salary)
                } else {
                    let amount = salary * workDays;
                    $(this).closest('tr').find('.payroll-amount').val(amount)
                }

            })
        })
    </script>
@endpush
