<?php
$setting = session()->get('setting');
?>

@extends('layouts.app')


@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Add Role</h4>
        </div>
    </section>

    <section class="content">
        <div class="card">

            {!! Form::open(['url' => route('roles.store')]) !!}
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        {!! Form::label('name', 'Role Name*', ['class' => 'control-label']) !!}
                        {!! Form::text('name', '', ['class' => 'form-control',  'required' => true]) !!}
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
                                        {!! Form::checkbox('permissions[]', 'expense.view') !!}
                                        View Expense
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'expense.delete') !!}
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
                                        {!! Form::checkbox('permissions[]', 'purchase.view') !!}
                                        View
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'purchase.delete') !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


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
                                                {!! Form::checkbox('permissions[]', "approval.level-$i") !!}
                                                Approval Level {{$i}}
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                    @endif

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Bank</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.view') !!}
                                        View Bank
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.delete') !!}
                                        Delete
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'bank.renew-account') !!}
                                        Renew Bank Account
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
                                        {!! Form::checkbox('permissions[]', 'accounting.withdraw') !!}
                                        Withdraw Money
                                    </label>
                                </div>
                                <div class="my-1">
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.deposit') !!}
                                        Deposit Money
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.transfer') !!}
                                        Transfer Money
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.service-charge') !!}
                                        Add Service charge
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'accounting.account-book') !!}
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
                                        {!! Form::checkbox('permissions[]', 'employee.view') !!}
                                        View Employee List
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'employee.delete') !!}
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
                                        {!! Form::checkbox('permissions[]', 'contact.view') !!}
                                        View Contacts
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'contact.delete') !!}
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
                                        {!! Form::checkbox('permissions[]', 'income.view') !!}
                                        View Incomes
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'income.delete') !!}
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
                                        {!! Form::checkbox('permissions[]', 'lot.view') !!}
                                        View Lots
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'lot.delete') !!}
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
                                        {!! Form::checkbox('permissions[]', 'payroll.view') !!}
                                        View Payroll
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.create') !!}
                                        Create
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'payroll.delete') !!}
                                        Delete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Manage Settings</div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'setting.view') !!}
                                        View Payroll
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'setting.edit') !!}
                                        Edit
                                    </label>
                                </div>
                                <div>
                                    <label class="form-check-label">
                                        {!! Form::checkbox('permissions[]', 'setting.create') !!}
                                        Create
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary float-right">Save</button>
                    </div>

                </div>


            </div>
            {!! Form::close() !!}
        </div>

    </section>

@endsection
