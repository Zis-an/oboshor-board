@extends('layouts.app')
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Bank Accounts</h4>
                @can('bank.create')
                    <button data-href="{{route('accounts.create')}}" class="btn btn-primary create-account-btn">
                        <i class="fa fa-plus"></i>
                        Create
                    </button>
                @endcan
            </div>
        </div>
    </section>

    @foreach($maturedAccounts as $account)
        <div class="alert mb-2" style="background-color: #99e7bb">
            <h4>{{$account->name}}</h4>
            <h4>Account NO: {{$account->account_no}}</h4>
            @can('bank.renew-account')
                <a class="btn btn-primary" href="{{route('accounts.get-renew', $account->id)}}">Renew Account</a>
            @endcan
        </div>
    @endforeach

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('account_type', 'Account Type', ['class' => 'control-label']) !!}
                    {!! Form::select('account_type', [''=>'All', 'std' => 'STD', 'fdr' => 'FDR'], '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-4">
                    {!! Form::label('status', 'Account Status', ['class' => 'control-label']) !!}
                    {!! Form::select('status', [''=>'All', 'active' => 'Active', 'closed' => 'Closed'], '', ['class' => 'form-control']) !!}
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="accountTypeTable">
                <thead>
                <tr>
                    <th>Actions</th>
                    <th>Account No.</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>
    <!-- Create Modal -->
    <div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="updateAccountModal" tabindex="-1" aria-hidden="true">
    </div>

    <!--- View Modal -->

    <div class="modal fade" id="viewAccountModal" tabindex="-1" aria-hidden="true">
    </div>
    <!-- Create Deposit -->
    <div class="modal fade" id="createDepositModal" tabindex="-1" aria-hidden="true">
    </div>

    <!-- Create Withdraw -->
    <div class="modal fade" id="createWithdrawModal" tabindex="-1" aria-hidden="true">
    </div>

    <!-- Create Withdraw -->
    <div class="modal fade" id="createTransferModal" tabindex="-1" aria-hidden="true">
    </div>

    <div class="modal fade" id="editOpeningBalanceModal" tabindex="-1" aria-hidden="true">
    </div>

    <div class="modal fade" id="addServiceChargeModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createAccountModal').modal('show');
            });

            //add unit

            //submit form ajax

            $(document).on('submit', '#createAccountForm, #updateAccountForm, #updateOpeningBalanceForm, #addServiceCharge, #createDepositForm, #createWithdrawForm, #createTransferForm', function (e) {
                e.preventDefault();
                submitAjaxForm(this, () => {
                    table.ajax.reload();
                    $(this).closest('.modal.fade').modal('hide');
                })
            });

            let table = $('#accountTypeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/accounts',
                    data: function(d){
                        d.type = $('#account_type').val()
                        d.status = $('#status').val()
                    },
                },

                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'actions'},
                    {data: 'account_no'},
                    {data: 'type'},
                    {data: 'balance', searchable: false},
                    {data: 'bank.name', searchable: false},
                    {data: 'branch.name', searchable: false},
                    {data: 'status', searchable: false}
                ],
            });

            $('#createAccountModal').on('show.bs.modal', function () {

                $('#dateCreateAccountForm').datetimepicker({
                    format: 'yyyy-MM-DD HH:mm:ss'
                });

                $("#createAccountForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        account_no: {
                            required: true,
                        },
                        balance: {
                            required: true,
                        },
                        bank_id: {
                            required: true,
                        },
                        branch_id: {
                            required: true,
                        },
                        account_type_id: {
                            required: true,
                        }
                    },
                    messages: {}
                });
            });

            $('#updateAccountModal').on('show.bs.modal', function () {
                $("#updateAccountForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        account_no: {
                            required: true,
                        },
                        bank_id: {
                            required: true,
                        },
                        branch_id: {
                            required: true,
                        },
                        account_type_id: {
                            required: true,
                        }
                    },
                    messages: {}
                });
            });

            //on click edit unit button

            $(document).on('click', '.edit-account-btn', function () {
                $('#updateAccountModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.create-account-btn', function () {
                $('#createAccountModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });
            <!-- account view modal -->
            $(document).on('click', '.view-account-btn', function () {
                $('#viewAccountModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-account-btn', function () {

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

            $(document).on('click', '.deposit-money-btn', function () {
                $('#createDepositModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            })

            $(document).on('click', '.add-charge-btn', function () {
                $('#addServiceChargeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            })

            $(document).on('click', '.withdraw-money-btn', function () {
                $('#createWithdrawModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            })

            $(document).on('click', '.transfer-money-btn', function () {
                $('#createTransferModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            })

            $('#createTransferModal').on('show.bs.modal', function () {
                $('#transferDate').datetimepicker({
                    format: 'yyyy-MM-DD'
                });
            })

            $('#createDepositModal').on('show.bs.modal', function () {
                $('#depositDate').datetimepicker({
                    format: 'yyyy-MM-DD'
                });
            })

            $('#createWithdrawModal').on('show.bs.modal', function () {
                $('#withdrawDate').datetimepicker({
                    format: 'yyyy-MM-DD'
                });
            })

            //update

            $(document).on('click', '.edit-opening-balance-btn', function () {
                $('#editOpeningBalanceModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            })

            //select2 for create

            $(document).on('change', '#selectBank', function () {

                let value = this.value;

                $.ajax({
                    method: 'GET',
                    url: `/get-branch-data?bank=${value}`,
                    success: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Branch</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })
                        $('#selectBranch').html(options);
                    }
                })
            });

            //view

            $(document).on('change', '.select-account-type', function () {
                let type = this.value;
                if (type === 'FDR') {
                    $(this).closest('.modal-body').find('.maturity-period').removeClass('d-none')
                } else {
                    $(this).closest('.modal-body').find('.maturity-period').addClass('d-none');
                }
            })

            $(document).on('change', '#status, #account_type', function () {

                let val = $('#status').val();

                console.log({val})

                table.ajax.reload();
            })

        })
        ;

    </script>
@endpush
