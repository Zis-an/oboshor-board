@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4>Users</h4>
                <a href="{{route('users.create')}}" class="btn btn-primary">
                    Add User
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
                            <th>Role</th>
                            <th>Name</th>
                            <th>User Id</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span class="badge badge-primary">{{ $role->name }}</span>
                                    @endforeach
                                <td>
                                    {{ $user->name }}
                                </td>
                                <td>{{$user->user_id}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('users.edit',$user->id)}}" class="btn btn-primary">
                                        Edit
                                    </a>
                                    <a href="{{route('users.destroy',$user->id)}}" class="btn btn-danger">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
