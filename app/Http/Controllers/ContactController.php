<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {

            $type = \request()->input('type');

            $employees = Contact::select('id', 'name', 'address', 'phone')
            ->where('type', $type);

            return DataTables::of($employees)
                ->editColumn('salary', function ($row) {
                    return "$row->salary($row->salary_type)";
                })
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-employee-btn' data-href='/contacts/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-employee-btn' data-href='/contacts/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('contact.index');
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'address' => 'string',
            'phone' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'address', 'phone', 'website', 'email'
        ]);

        $data['type'] = \request()->input('type');

        $user = auth()->user();

        try {

            Contact::create($data);

            return $this->respondWithSuccess('Contact added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $contact = Contact::findOrFail($id);

        return view('contact.partials.edit-modal', compact('contact'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'address' => 'string',
            'phone' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'address', 'phone', 'website', 'email'
        ]);


        $user = auth()->user();

        try {

            $contact = Contact::findOrFail($id);

            $contact->name = \request()->input('name');
            $contact->address = \request()->input('address');
            $contact->phone = \request()->input('phone');
            $contact->website = \request()->input('website', null);
            $contact->email = \request()->input('email', null);

            $contact->save();

            return $this->respondWithSuccess('Contact Updated');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $contact = Contact::findOrFail($id);

        try {
            $contact->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
