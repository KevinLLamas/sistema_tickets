@extends('app')
@section('content')
<div id="seguimiento_externo" >
    <div class="col-md-12 mt-3">
        <ol class="breadcrumb w-100">
            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
            <li class="breadcrumb-item">Seguimiento Externo</li>
        </ol>
    </div>
    <form class="container mb-5" id="alumno" v-on:submit.prevent="guardar()">
        <h1 class="mb-5">Seguimiento Externo</h1>
        <div class="row h-100 justify-content-center align-items-center mb-5 mt-5" id="verificar">
            <div class="col-10 col-md-10 col-lg-6">
                <label for=""  class="font-weight-bold ml-3">Para dar seguimiento a su solicitud es necesario que ingrese su código de verificación</label>
                <div class="form-row col-md mt-2 ">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="codigo" id="codigo" aria-describedby="helpId" placeholder="Código" maxlength="6" minlength="6" v-model="codigo" required>
                        <small class="text-muted"></small>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" v-on:click="verificar()">Verificar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="{{asset('assets/vue/seguimiento_externo.js')}}"></script>
@endsection