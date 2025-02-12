@extends('layouts.app')

@section('main')
    <div class="content-header">
        <h3>Edit Leave Plan</h3>
    </div>

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('leave-plans.update', $plan->id), 'method' => 'PUT']) !!}
            <div class="row">

                <div class="col-sm-6 mb-2">
                    {!! Form::label('year_id', 'Financial Years *') !!}
                    {!! Form::select('year_id', $financialYears, $plan->year_id, ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Employee</label>
                    <input type="hidden" name="employee_id"
                           value="{{$plan->employee_id}}"
                           class="form-control"

                    />
                    <input type="text"
                           class="form-control"
                           value="{{$plan->employee->name ?? ''}}"
                           readonly
                    >
                </div>

                <div class="col-sm-6">
                    {!! Form::label('balance', 'Number of Days') !!}
                    {!! Form::number('balance', $plan->balance, ['class' => 'form-control']) !!}
                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            $('#employee_select').select2({
                placeholder: "Select Employee",
                multiple: true,
            })

            $('#employee_select').on('select2:select', function (e) {

                let id = e.params.data.id;

                console.log({params: e.params})

                //return;

                let indexEl = $('tbody').find('tr:last .td-index');

                let index = 0;

                if (indexEl.length > 0) {
                    index = Number(indexEl.val()) + 1
                }


                $.ajax({
                    type: 'GET',
                    url: "{{route('leave-plans.add-more')}}",
                    data: {
                        index,
                        id,
                    },
                    success: function (html) {
                        $('tbody').append(html);
                    }
                })
            })

            $('#employee_select').on('select2:unselect', function (event) {
                let id = event.params.data.id

                $('tbody').find('tr .td-employee-id[value="' + id + '"]').parents('tr').remove()

            })
        })
    </script>
@endpush
