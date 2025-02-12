<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('/images/logo/oboshor-logo.png')}}"/>
    <title>Login Page</title>
    <link rel="stylesheet" href="{{asset('/adminLTE/dist/css/adminlte.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/css/auth.css')}}"/>
</head>

<body class="sidebar-dark">
<div class="main-wrapper">
    <div class="row w-100 mx-0">
        <div class="col-md-8 col-xl-6 mx-auto">
            <div class="card">
                <div class="row">
                    <div class="col-md-4 pr-md-0">
                        <div class="auth-left"
                             style="background-image: url('{{ asset('images/obo_home.jpg') }}');">
                        </div>
                    </div>
                    <div class="col-md-8 pl-md-0">
                        <div class="px-4 py-5">
                            <div class="text-center">
                                <img src="{{ asset('/images/logo/govt.png')}}" style="max-width: 50px;">
                                <h4 href="#" class="d-block my-2 font-weight-bold">{{config('app.name')}}</h4>
                                <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your
                                    account.</h5>
                            </div>
                            <form method="POST" action="{{ route('do-login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="UserID">User Id</label>
                                    <input type="text" placeholder="UserId"
                                           class="form-control @error('user_id') is-invalid @enderror"
                                           name="user_id" value="{{ old('user_id') }}" required
                                           autocomplete="User Id" autofocus>
                                    @error('UserID')
                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="Password">Password</label>
                                    <input type="password" placeholder="Password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="current-password">

                                    @error('Password')
                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                    @enderror
                                </div>
                                <div class="mt-3 d-flex justify-content-center">
                                    <button type="submit"
                                            class="btn btn-primary px-4 mr-2 mb-2 mb-md-0 text-white">Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>
