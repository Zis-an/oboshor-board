@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4>Roles</h4>
                <a href="{{route('roles.create')}}" class="btn btn-primary">
                    Add Role
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered w-100">
                        <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Total Permissions</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>
                                    {{ $role->name }}
                                </td>
                                <td>{{$role->name === 'Super Admin' ? 'All' : $role->permissions_count}}</td>
                                @if($role->name !== 'Super Admin')
                                    <td>
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <button type="button" class="btn btn-danger">Delete</button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
