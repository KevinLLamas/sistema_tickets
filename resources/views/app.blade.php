<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>CAST</title>
  <link rel="icon" type="image/png" href="assets/images/logo_edu.png" /> 
  <!-- Custom fonts for this template-->
  <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">
  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{ asset('assets/js/vue.js')}}"></script>
  <script src="{{ asset('assets/js/axios.js')}}"></script>
  <script src="{{ asset('assets/js/toastr.js')}}"></script> 
  {{--  Version 2.10 sweetalert --}}
  <script src="{{asset('assets/js/sweetalert.js')}}"></script>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
<div id="wrapper">
    @include('sidebar'){{-- Sidebar /Menu lateral --}}
        @include('navbar') {{-- Menu superior --}}
            @yield('content') {{-- Cotenido de la pagina --}}
           

</div>
<!-- End of Content Wrapper -->
        {{-- @include('footer') --}}
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        
        <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <!-- Core plugin JavaScript-->
        <script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
        <!-- Custom scripts for all pages-->
        <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
        <script>
            tinymce.init({
                selector: 'textarea#editor',
                menubar: false
            });
        
            $('.selectpicker').selectpicker({
                maxOptions:2
            });
        </script>
</body>
</html>