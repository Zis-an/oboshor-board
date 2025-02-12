<?php

namespace App\Http\Controllers;

use App\Models\ApprovalTimeline;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ApprovalTimelineController extends Controller
{
    function index()
    {
        $user = auth()->user();

        $accessLevel = [];

        $setting = session()->get('setting');

        for($i=1; $i<=$setting->approval_level; $i++){
            if ($user->hasPermissionTo('approval.level-'. $i)) {
                $accessLevel[] = $i;
            }
        }

        //get all requests that are not performed by me
        $approvalRequests = ApprovalTimeline::where('is_active', true)
            ->with('performedBy')
            ->whereIn('current_level', $accessLevel)
            ->where('performed_by', '!=', $user->id)
            ->get();

        return view('approval.index', compact(('approvalRequests')));
    }

    function show($id)
    {
        $approval = ApprovalTimeline::findOrFail($id);

        return view('approval.show', compact(('approval')));
    }

    function store(Request $request)
    {

        $user = auth()->user();

        $id = $request->input('id');

        $setting = session()->get('setting');

        $approvalRequest = ApprovalTimeline::find($id);
        //disable this current time line
        $approvalRequest->is_active = false;

        //$approvalRequest->save();

        $status = $request->input('status');

        if ($status == 'approved') {
            $level = $approvalRequest->current_level + 1;

            //now check if required level fulfilled

            if ($level >= $setting->required_level) {
                $model = app($approvalRequest->model)->find($approvalRequest->model_id);
                if ($model->status != 'final') {
                    $model->status = 'final';
                    $model->save();
                }
            }


        } else {
            $level = 0;
        }

        $cloned = $approvalRequest->replicate();

        $cloned->$level = $level;
        $cloned->status = $status;

        toastr()->success($status);

        return redirect()->route('approvals.index');

    }

    function getDetail($id){
        $approval = ApprovalTimeline::findOrFail($id);

        if($approval->type == 'income'){
            return redirect()->route('incomes.show', $approval->model_id);
        }

        if($approval->type == 'purchase'){
            return redirect()->route('purchase.show', $approval->model_id);
        }

        return redirect()->route('approvals.index');

    }

}
