@extends('layouts.app')

@section('main')
    <div class="content-header">
        <h3>Create Leave Plan</h3>
    </div>

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('leave-plans.store')]) !!}
            <div class="row">

                <div class="col-sm-4 mb-2">
                    {!! Form::label('year_id', 'Financial Years *') !!}
                    {!! Form::select('year_id', $financialYears, '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-12 mb-2">
                    <label>Select Employee *</label>
                    <select name="employees[]" class="form-control" multiple
                            id="employee_select"
                            required
                    >
                        <option value="all">For All</option>
                        @foreach($employees as $employee)
                            <option value="{{$employee->id}}">{{$employee->name}} ({{$employee->designation_name}})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <table class="table table-bordered" id="table">
                        <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Number of Days</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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

                $('tbody').find('tr .td-employee-id[value="'+id+'"]').parents('tr').remove()

            })
        })
    </script>
@endpush
