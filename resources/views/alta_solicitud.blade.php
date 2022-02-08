@extends('app')
@section('content')
<div id="alta_solicitud" >
    <div class="mt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page"><span>Crear nueva solicitud</span></li>
            </ol>
        </nav>

    </div>
    <form class="mb-5 mt-2" id="alumno" v-on:submit.prevent="guardar()">
        <h1 class="text-black fw-bold">Crear nueva solicitud</h1>
        <div class="form-group">
            <label for="">Usuario solicitante*</label>
            <select class="form-control form-control-lg" name="perfil" id="perfil" v-model="solicitud.perfil" @change="getCategorias()" required>
                <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="perfil.id" v-for="perfil in Perfiles">@{{perfil.nombre}}</option>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-6" v-if="solicitud.perfil != ''">
                <label for="">Categoría del problema</label>
                <select class="form-control form-control-lg" name="categoria" id="categoria" v-model="solicitud.categoria" @change="getSubcategorias()" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="categoria.id" v-for="categoria in Categorias">@{{categoria.nombre}}</option>
                </select>
            </div>
            <div class="form-group col-6" v-if="solicitud.categoria != ''">
                <label for="">Subcategoría del problema</label>
                <select class="form-control form-control-lg" name="subcategoria" id="subcategoria" v-model="solicitud.subcategoria" @change="mostrar_inputs()" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="subcategoria.id" v-for="subcategoria in Subcategorias">@{{subcategoria.nombre}}</option>
                </select>
            </div>
            <!--div class="form-group col-6" v-for="(campo, index) in Campos" >
                <div v-if="campo.campo_personalizado_tipo=='select'">
                    <label for="">@{{campo.etiqueta}}</label>
                    <select class="form-control" v-model="campo.respuesta" required>
                        <option :value="undefined" disabled selected="selected">Selecciona</option>
                        <option v-for="(opt, index) in campo.opciones" :value="opt" >@{{opt.etiqueta}}</option>
                    </select>
                </div> 
                <div v-else>
                    <label for="">@{{campo.etiqueta}}</label>
                    <input v-if="campo.model == 'curp'" class="form-control" :type="campo.campo_personalizado_tipo" v-on:blur="buscar(campo.respuesta)" :maxlength="campo.max_length" :minlength="campo.min_length" v-model="campo.respuesta" :placeholder="campo.etiqueta" required>
                    <input v-else   class="form-control" :type="campo.campo_personalizado_tipo" :maxlength="campo.max_length" :minlength="campo.min_length" v-model="campo.respuesta" :placeholder="campo.etiqueta" required>
                </div>
            </div-->
            <div class="form-group col-md-6" v-if="show_inputs">
                <label for="">Correo de contacto</label>
                <input class="form-control form-control-lg"  v-model="solicitud.correo_contacto"  required data-inputmask="'alias': 'email' ">
            </div>
            <div class="form-group col-md-6 mt-2" v-if="show_inputs">
                {{-- <label for="" >Subir archivos de ayuda</label>
                <div class="custom-file" >
                    <input type="file" class="custom-file-input" id="customFileLang" v-on:change="fileChangeFormato"  multiple>
                    <label class="custom-file-label" id="label_formato" for="customFileLang" data-browse="Seleccionar" >Seleccionar Archivos</label>
                    <small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb y se permiten máximo 4 archivos.</small>
                </div> --}}

                
            <div class="input-group" v-if="show_inputs">
                <input type="text" class="form-control form-control-lg file-upload-info" disabled="" placeholder="Seleccionar archivos de ayuda">
                <span class="input-group-append">
                  <button class="file-upload-browse btn btn-primary btn-lg" type="button">Seleccionar</button>
                </span>
            </div>

            </div>


            <div class="form-group col-md-12 mt-3" v-if="show_inputs">
                <label for="">Descripción</label>
                <textarea class="form-control form-control-lg" name="descripcion_solicitud" id="" rows="10" v-model="solicitud.descripcion" placeholder="Escribe aquí la descripción del problema" required></textarea>
            </div>

            
        </div>
        <button type="submit" v-if="show_inputs" class="btn btn-primary" id="btnGuardar">Guardar solicitud</button>
    </form>
</div>
<script src="{{asset('assets/vue/alta_solicitud.js')}}"></script>
@endsection