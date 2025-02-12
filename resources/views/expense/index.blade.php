@extends('layouts.app')

@section('main')

    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-sm-4">
                    {{Form::label('financial_year_id', 'Account')}}
                    {!! Form::select('financial_year_id', $financialYears, '', ['class' => 'form-control select2-search', 'placeholder' => 'Financial Year']) !!}
                </div>

                <div class="col-sm-4">
                    {{Form::label('head_id', 'Head')}}
                    {!! Form::select('head_id', $heads, '', ['class' => 'form-control select2-search', 'placeholder' => 'Main Head']) !!}
                </div>

                <div class="col-sm-4">
                    {{Form::label('sub_head_id', 'Sub Head')}}
                    {!! Form::select('sub_head_id', [], '', ['class' => 'form-control select2-search', 'placeholder' => '-Select-']) !!}
                </div>
            </div>
        </div>
    </div>

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4>Expense Lists</h4>
                <a href="{{route('expenses.create')}}" class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                    Add</a>
            </div>
        </div>
    </section>
    <section class="card">
        <div class="card-body">


            <table class="table" id="budget-table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Head</th>
                    <th>Sub Head</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            let table = $('#budget-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: window.location.pathname,
                    data: function (d) {
                        d.financial_year_id = $('#financial_year_id').val();
                        d.head_id = $('#head_id').val();
                        d.sub_head_id = $('#sub_head_id').val();
                    }
                },
                columnDefs: [
                    {
                        targets: '_all',
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'date'},
                    {data: 'heads'},
                    {data: 'sub_heads'},
                    {data: 'amount'},
                    {data: 'status'},
                    {data: 'actions'}
                ],
            });

            $(document).on('click', '.delete-budget-btn', function () {

                let url = $(this).data('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            dataType: 'json',
                            success: function (res) {
                                console.log("deleted", res);
                                toastr.success("Item deleted");
                                table.ajax.reload();
                            },
                            error: function (er) {
                                console.log(er)
                            }
                        });

                    }
                })

            })

            $('#financial_year_id, #head_id, #sub_head_id').on('change', function () {
                table.ajax.reload();
            })

            $('#head_id').on('change', function () {
                let head_id = this.value;
                $.ajax({
                    url: `/get-head-items?head_id=${head_id}`,
                    success: function (data) {
                        let options = "<option value=''>--Select One--</option>";
                        data.forEach(function (item) {
                            options += `<option value='${item.id}'>${item.name}</option>`;
                        })
                        $('#sub_head_id').html(options);
                    }
                })
            })

        })
    </script>
@endpush
