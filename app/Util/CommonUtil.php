<?php

namespace App\Util;

use App\Models\ApprovalTimeline;
use App\Models\ReferenceCount;

class CommonUtil
{
    function addApproval($model, $type){

        $user = auth()->user();
        try {
            ApprovalTimeline::create([
                'performed_by' => $user->id,
                'model_id' => $model->id,
                'type' => $type,
                'model' => get_class($model),
            ]);
        }catch(\Exception $exception){
            throw new \Exception($exception);
        }
    }

    function getReferenceCount(string $type)
    {
        $ref = ReferenceCount::where('type', $type)
            ->first();

        if (empty($ref)) {
            $ref = new ReferenceCount();
            $newVal = 1;
            $ref->type = $type;
        } else {
            $newVal = $ref->count + 1;
        }

        $ref->count = $newVal;

        $ref->save();

        return $newVal;
    }

    function generateInvoiceNumber(string $type, $prefix = '', $withYearPrefix = true, $separator = '-')
    {
        $count = $this->getReferenceCount($type);

        if ($withYearPrefix) {
            $prefix = $prefix . $separator . now()->format('y');
        }

        return $prefix . $separator . str_pad($count, 4, 0, STR_PAD_LEFT);
    }

}
