<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Theme style -->
    
    <link rel="stylesheet" href="{{  url('assets/adminlte/dist/css/adminlte.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{  url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{  url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
       <title>Login</title>
</head>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Zoom Scholar</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                 @if (session('error'))
                       
                         
                            <span class=" d-flex block capitalize text-danger"> {{ session('error') }}</span>

                       
                    @endif

                    <div class="input-group mb-3">
                        <input id="email" placeholder=" Email "
                            class=" form-control @error('email') is-invalid @enderror" type="email" name="email"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>
   

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback " role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="password" class="form-control" type="password" name="password"
                            placeholder="Password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <!-- Remember me toggle -->
                        <div class="row">
                            <!-- <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" name="remember" id="remember"
                                        class="w-10 h-4 transition bg-gray-200 border-none rounded-full shadow-inner outline-none appearance-none toggle checked:bg-pink disabled:bg-gray-200 focus:outline-none"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <label for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div> -->
                            <!-- /.col -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
</body>
<!-- /.login-box -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- jQuery -->
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
</body>
</html>
