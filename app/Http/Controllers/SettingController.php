<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function index()
    {
        $setting = Setting::first();

        //financial years for dropdown

        $financialYears = FinancialYear::pluck('name', 'id');

        return view('setting.index', compact('setting', 'financialYears'));
    }

    function store(Request $request)
    {
        //create or update

        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
        }

        $setting->approval_level = $request->input('approval_level');
        $setting->required_level = $request->input('required_level');
        $setting->active_financial_year_id = $request->input('active_financial_year_id');

        $setting->save();

        toastr()->success('Success');

        return redirect()->route('settings.index');

    }

}
