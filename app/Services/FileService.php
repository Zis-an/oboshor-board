<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FileService
{
    function upload(Request $request, $name = 'files', $isMultiple = false)
    {

        $uploads = [];

        if ($request->hasFile($name)) {

            $files = $request->file($name);

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/'), $name);
                $uploads[] = '/uploads/' . $name;
            }

        }

        return $uploads;

    }
}
