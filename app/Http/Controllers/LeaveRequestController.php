<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\LeavePlan;
use App\Models\LeaveRequest;
use App\Models\Setting;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    function index()
    {
        if (\request()->ajax()) {
            $query = LeaveRequest::with('user')
                ->orderByDesc('created_at');

            return datatables()->of($query)
                ->addColumn('employee_name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('action', function ($row) {
                    return view('leave-management.leave-request.action-buttons', compact('row'));
                })
                ->make(true);
        }

        return view('leave-management.leave-request.index');
    }

    function create()
    {
        $employees = Employee::join('designations', 'employees.designation_id', '=', 'designations.id')
            ->select('employees.name', 'employees.id', 'designations.name as designation_name')
            ->pluck('name', 'id');

        return view('leave-management.leave-request.create', compact('employees'));
    }

    function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'reason' => 'required',
            'file' => 'nullable',
        ]);

        $user = auth()->user();

        $dateRange = explode(' - ', $request->input('date'));
        $startDate = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        $days = $startDate->diffInDays($endDate) + 1;
        $setting = Setting::first();
        $activeFinancialYear = FinancialYear::find($setting->active_financial_year_id);

        $plan = LeavePlan::where('year_id', $activeFinancialYear->id)
            ->where('user_id', $user->id)
            ->first();

        if (empty($plan)) {
            return redirect()->back()->with('error', 'No leave plan found for this employee');
        }

        $files = (new FileService())->upload($request, 'file');

        LeaveRequest::create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $request->input('reason'),
            'user_id' => $user->id,
            'days' => $days,
            'plan_id' => $plan->id,
            'file' => !empty($files) ? $files[0] : null
        ]);

        return redirect()->route('leave-requests.index');

    }

    function show($id)
    {
        $leaveRequest = LeaveRequest::with('plan', 'user')->find($id);
        return view('leave-management.leave-request.view', compact('leaveRequest'));
    }

    function edit($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $startDate = Carbon::parse($leaveRequest->start_date)->format('d m Y');
        $endDate = Carbon::parse($leaveRequest->end_date)->format('d m Y');

        $dateRange = implode(' - ', [$startDate, $endDate]);

        return view('leave-management.leave-request.edit', compact('leaveRequest', 'dateRange'));
    }

    function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required',
            'reason' => 'required',
            'file' => 'nullable',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $user = auth()->user();

        $dateRange = explode(' - ', $request->input('date'));
        $startDate = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        $days = $startDate->diffInDays($endDate) + 1;

        $files = (new FileService())->upload($request, 'file');

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $request->input('reason'),
            'days' => $days,
        ];

        if (!empty($files)) {
            $data['file'] = $files[0];
        }

        $leaveRequest->update($data);

        return redirect()->route('leave-requests.index');
    }

    function updateStatus(Request $request, $id)
    {

        //dd($request->input('status'));

        $leaveRequest = LeaveRequest::find($id);
        $leaveRequest->status = $request->input('status');
        $leaveRequest->save();

        if ($request->input('status') == 'approved') {
            $leaveRequest->plan->taken += $leaveRequest->days;
            $leaveRequest->plan->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request status updated successfully',
        ]);
    }

}
