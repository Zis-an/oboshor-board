<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\LeavePlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LeavePlanController extends Controller
{
    function index()
    {
        if (\request()->ajax()) {

            $query = LeavePlan::with('user');

            return DataTables::of($query)
                ->addColumn('remaining', function ($row) {
                    return $row->balance - $row->taken;
                })
                ->addColumn('employee_name', function ($row) {
                    return $row->user->name ?? '';
                })
                ->addColumn('action', function ($row) {
                    return view('leave-management.plan.action-buttons', compact('row'));
                })
                ->make(true);
        }

        return view('leave-management.plan.index');
    }

    function create()
    {

        $financialYears = FinancialYear::getForDropdown();

        $employees = User::join('designations', 'users.designation_id', '=', 'designations.id')
            ->select('users.name', 'users.id', 'designations.name as designation_name')
            ->get();

        return view('leave-management.plan.create', compact('employees', 'financialYears'));
    }

    function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required',
            'employees' => 'required',
        ]);

        $plans = $request->input('plans');

        DB::beginTransaction();

        try {

            foreach ($plans as $plan) {
                LeavePlan::create([
                    'year_id' => $request->input('year_id'),
                    'user_id' => $plan['employee_id'],
                    'balance' => $plan['balance'],
                ]);
            }

            DB::commit();

            return redirect()->route('leave-plans.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    function edit($id)
    {
        $plan = LeavePlan::with('employee')->find($id);
        $financialYears = FinancialYear::getForDropdown();
        return view('leave-management.plan.edit', compact('plan', 'financialYears'));
    }

    function update(Request $request, $id){
        $request->validate([
            'year_id' => 'required',
            'balance' => 'required',
        ]);

        $plan = LeavePlan::find($id);

        $plan->update([
            'year_id' => $request->input('year_id'),
            'balance' => $request->input('balance'),
        ]);

        return redirect()->route('leave-plans.index');
    }

    function destroy($id){
        $plan = LeavePlan::findOrFail($id);
        $plan->delete();

        return response()->json(['status' => 'success', 'message' => 'Item deleted Successfully']);
    }

    function addMore(Request $request)
    {
        $index = $request->input('index', 0);
        $id = $request->input('id');
        $employee = User::join('designations', 'users.designation_id', '=', 'designations.id')
            ->select('users.name', 'users.id', 'designations.name as designation_name')
            ->find($id);

        return view('leave-management.plan.item-row', compact('index', 'employee'));
    }

    function leaveReport(){

    }

}
