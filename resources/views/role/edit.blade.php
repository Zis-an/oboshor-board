@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Update Role</h4>
        </div>
    </section>

    <section class="content">
        <div class="card">

            {!! Form::open(['url' => route('roles.update', $role->id, $role->id), 'method' => 'PUT']) !!}
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        {!! Form::label('name', 'Role Name*', ['class' => 'control-label']) !!}
                        {!! Form::text('name', $role->name, ['class' => 'form-control']) !!}
                    </div>

                    <div class="col-12 my-2">
                        <h5>Permissions</h5>
                    </div>

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Expense</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.view', in_array('expense.view',$permissions)) !!}
                                        View Expense
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.edit', in_array('expense.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.create', in_array('expense.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.delete', in_array('expense.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Purchase</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $permissions)) !!}
                                        View
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.edit', in_array('purchase.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.create', in_array('purchase.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.delete', in_array('purchase.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Approval -->

                    <!--Approval -->

                    @if(!empty(session()->get('setting')->approval_level))

                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Approval</div>
                                </div>
                                <div class="card-body">
                                    @for($i=1; $i<= session()->get('setting')->approval_level; $i++)
                                        <div>
                                            <label class="form-check-label">
                                                {!! Form::checkbox('permissions[]', "approval.level-$i", in_array("approval.level-$i", $permissions)) !!}
                                                Approval Level {{$i}}
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                    @endif

                    <!-- bank -->

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Bank</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.view', in_array('bank.view', $permissions)) !!}
                                        View Bank
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.edit', in_array('bank.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.create', in_array('bank.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.delete', in_array('bank.delete',$permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accounting -->

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Accounting</h5>
                            </div>
                            <div class="card-body">
                                <div class="my-1">
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.withdraw', in_array('accounting.withdraw', $permissions)) !!}
                                        Withdraw Money
                                    </label>
                                </div>
                                <div class="my-1">
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.deposit', in_array('accounting.deposit', $permissions)) !!}
                                        Deposit Money
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.transfer', in_array('accounting.transfer', $permissions)) !!}
                                        Transfer Money
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.service-charge', in_array('accounting.service-charge',$permissions)) !!}
                                        Add Service charge
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.account-book', in_array('accounting.account-book', $permissions)) !!}
                                        View Account Book
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Employee Management</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.view', in_array('employee.view', $permissions)) !!}
                                        View Employee List
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.edit', in_array('employee.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.create', in_array('employee.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.delete', in_array('employee.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Contacts</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.view', in_array('contact.view', $permissions)) !!}
                                        View Contacts
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.edit', in_array('contact.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.create', in_array('contact.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.delete', in_array('contact.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Incomes</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.view', in_array('income.view', $permissions)) !!}
                                        View Incomes
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.edit', in_array('income.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.create', in_array('income.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.delete', in_array('income.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Lots</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.view', in_array('lot.view', $permissions)) !!}
                                        View Lots
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.edit', in_array('lot.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.create', in_array('lot.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.delete', in_array('lot.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Payroll</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.view', in_array('payroll.view', $permissions)) !!}
                                        View Payroll
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.view', in_array('payroll.edit', $permissions)) !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.create', in_array('payroll.create', $permissions)) !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.delete', in_array('payroll.delete', $permissions)) !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>

    </section>

@endsection
