<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <style>[v-cloak] {
    display: none;
  }
  </style>
  <title>Sistema de tickets</title>
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
</head>



  
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

  <!-- Page Wrapper -->
<div class="container-scroller">
  @include('navbar') {{-- Menu superior --}}
    {{--@include('sidebar'){{-- Sidebar /Menu lateral --}}
        
            @yield('content') {{-- Contenido de la pagina --}}
           

</div>
<!-- End of Content Wrapper -->
        {{-- @include('footer') --}}
        
        <script src="{{asset('assets/vendor/js/vendor.bundle.base.js')}}"></script>
        <script src="{{asset('assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
        <!-- endinject -->
        <!-- Plugin js for this page -->
        <!-- End plugin js for this page -->
        <!-- inject:js -->
        <script src="{{asset('assets/js/off-canvas.js')}}"></script>
        <script src="{{asset('assets/js/hoverable-collapse.js')}}"></script>
        <script src="{{asset('assets/js/template.js')}}"></script>
        <script src="{{asset('assets/js/settings.js')}}"></script>
        <script src="{{asset('assets/js/todolist.js')}}"></script>
</body>
</html>