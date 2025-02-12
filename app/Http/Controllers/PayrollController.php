<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PayrollController extends ParentController
{
    function index()
    {
        if (\request()->ajax()) {
            $payrolls = Payroll::all();
            return DataTables::of($payrolls)
                ->editColumn('approved_on', function ($row) {
                    return $row->approved_at ? $row->approved_at : 'Not approved';
                })
                ->addColumn('actions', function ($row) {
                    return "
                            <a class='btn btn-primary btn-sm view-budget-btn' href='/payrolls/$row->id'>View</a>
                            <a class='btn btn-primary btn-sm edit-branch-btn' href='/payrolls/$row->id/edit'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-budget-btn' data-href='/budgets/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('payroll.index');
    }

    function create()
    {
        return view("payroll.create");
    }

    function show($id)
    {
        $payroll = Payroll::with('items.employee')
            ->findOrFail($id);

        return view('payroll.show', compact('payroll'));
    }

    function store()
    {

        \request()->validate([
            'date' => 'required',
            'name' => 'required|string',
        ]);

        $numOfDays = Carbon::parse(\request()->date)->daysInMonth;

        DB::beginTransaction();

        try {

            $payroll = Payroll::create([
                'name' => \request()->name,
                'date' => \request()->date,
                'created_by' => auth()->id(),
            ]);

            $employees = Employee::all();

            foreach ($employees as $employee) {
                EmployeePayroll::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employee->id,
                    'salary' => $employee->salary,
                    'salary_type' => $employee->salary_type,
                    'amount' => $employee->salary_type === 'monthly' ? $employee->salary : $employee->salary * $numOfDays,
                    'work_days' => $numOfDays,
                    'status' => 'due'
                ]);
            }

            DB::commit();
            toastr()->success('Payroll generated');
            return redirect()->route('payrolls.index');

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            toastr()->error($exception->getMessage());
            return redirect()->back();
        }
    }

    function edit($id)
    {
        $payroll = Payroll::with('items.employee')
            ->findOrFail($id);

        return view('payroll.edit', compact('payroll'));
    }

    function update($id)
    {

        try {

            $payroll = Payroll::findOrFail($id);

            foreach (\request()->items as $item) {
                $employeePayroll = EmployeePayroll::findOrFail($item['id']);
                $employeePayroll->salary = $item['salary'];
                $employeePayroll->work_days = $item['work_days'];
                $employeePayroll->amount = $item['amount'];
                $employeePayroll->save();
            }

            toastr()->success('Payroll updated');

            return redirect()->route('payrolls.index');

        } catch (\Exception $exception) {
            toastr()->error($exception->getMessage());
            return redirect()->back();
        }

    }
}
