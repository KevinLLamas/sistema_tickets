<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Star Admin2 </title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/feather/feather.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/typicons/typicons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/simple-line-icons/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" />
  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
  @if(env('APP_DEBUG')=='true')
    <script src="{{ asset('assets/js/vue.js')}}"></script>
  @else
    <script src="{{ asset('assets/js/vue.prod.js')}}"></script>
  @endif
  <script src="{{ asset('assets/js/axios.js')}}"></script>
  <script src="{{ asset('assets/js/toastr.js')}}"></script> 
  {{--  Version 2.10 sweetalert --}}
  <script src="{{asset('assets/js/sweetalert.js')}}"></script>
</head>

<body class="sidebar-dark">
  <div class="container-scroller" id="login">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
        <div class="row flex-grow">
          <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="auth-form-transparent text-left p-3">
              <div class="brand-logo">
                <img src="{{asset('assets/images/logo.svg')}}" alt="logo">
              </div>
              <h4>Bienvenido</h4>
              <h6 class="fw-light">Un gusto tenerte de regreso!</h6>
              <form class="pt-3" v-on:submit.prevent="Ingresar()">
                <div class="form-group">
                  <label for="exampleInputEmail">Usuario</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="ti-user text-primary"></i>
                      </span>
                    </div>
                    <input type="text" v-model="user" class="form-control form-control-lg border-left-0" id="exampleInputEmail" placeholder="Username" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword">Contraseña</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="ti-lock text-primary"></i>
                      </span>
                    </div>
                    <input type="password" v-model="pass" class="form-control form-control-lg border-left-0" id="exampleInputPassword" placeholder="Password" required>                        
                  </div>
                </div>
                @if(Session::has('message')) 
                    <small class="text-danger">{{Session::get('message')}}</small>
                @endif
                <div class="my-3 col-md-12">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn col-md-12" type="submit">LOGIN</button>
                </div>
                <div class="text-center mt-4 fw-light">
                  ¿No tienes cueta? <a href="register-2.html" class="text-primary">Crear una</a>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6 login-half-bg d-flex flex-row">
            <!--p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2021  All rights reserved.</p-->
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ asset('assets/vue/login.js')}}"></script>
  <script src="{{asset('assets/vendor/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{asset('assets/js/off-canvas.js')}}"></script>
    <script src="{{asset('assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('assets/js/cookie.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
    <script src="{{asset('assets/js/settings.js')}}"></script>
    <script src="{{asset('assets/js/todolist.js')}}"></script>
  <!-- endinject -->
</body>

</html>
