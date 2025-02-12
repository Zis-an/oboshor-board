<!-- Navbar -->
<!--<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    &lt;!&ndash; Left navbar links &ndash;&gt;
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    &lt;!&ndash; Right navbar links &ndash;&gt;
    <ul class="navbar-nav ml-auto">
        &lt;!&ndash; Auth dropdown &ndash;&gt;

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <li class="collapse navbar-collapse" id="navbarSupportedContent">
            &lt;!&ndash; Left Side Of Navbar &ndash;&gt;
            <ul class="navbar-nav mr-auto">

            </ul>

            &lt;!&ndash; Right Side Of Navbar &ndash;&gt;
            <ul class="navbar-nav ml-auto">
                &lt;!&ndash; Authentication Links &ndash;&gt;
                @guest
    <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>




@else
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
{{ auth()->user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
    </form>
</div>
</li>




@endguest
</ul>
</li>

</ul>
</nav>-->

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{route('home.dashboard')}}" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search"
                               aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Logout -->
        @auth
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user-circle"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <div class="dropdown-item dropdown-header p-5" style="height: 80px">
                        {{auth()->user()->name}}
                    </div>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="#"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <div>
                            {{ __('Logout') }}
                            <i class="fa-regular fa-arrow-right-from-bracket"></i>
                        </div>

                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                </div>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
        @endauth
    </ul>
</nav>
