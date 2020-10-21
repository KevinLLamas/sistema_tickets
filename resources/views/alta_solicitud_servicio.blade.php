@extends('app')
@section('content')
<div id="alta_solicitud" >
    <div class="col-md-12 mt-3">
        <ol class="breadcrumb w-100">
            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
            <li class="breadcrumb-item">Solicitar Servicio</li>
        </ol>
    </div>
    <form class="container mb-5" id="alumno" v-on:submit.prevent="guardar()">
        <h1>Solicitar Servicio</h1>
        <div class="form-group">
            <label for="">Tipo de persona solicitante*</label>
            <select class="form-control" name="perfil" id="perfil" v-model="solicitud.perfil" @change="getCategorias()" required>
                <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="perfil.id" v-for="perfil in Perfiles">@{{perfil.nombre}}</option>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-6" v-if="solicitud.perfil != ''">
                <label for="">Categoria del problema</label>
                <select class="form-control" name="categoria" id="categoria" v-model="solicitud.categoria" @change="getSubcategorias()" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="categoria.id" v-for="categoria in Categorias">@{{categoria.nombre}}</option>
                </select>
            </div>
            <div class="form-group col-6" v-if="solicitud.categoria != ''">
                <label for="">Subcategoria del problema</label>
                <select class="form-control" name="subcategoria" id="subcategoria" v-model="solicitud.subcategoria" @change="getCampos()" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="subcategoria.id" v-for="subcategoria in Subcategorias">@{{subcategoria.nombre}}</option>
                </select>
            </div>
            <div class="form-group col-6" v-for="(campo, index) in Campos" >
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
            </div>
            <div class="form-group col-md-6" v-if="Campos.length > 0">
                <label for="">Correo de contacto</label>
                <input class="form-control" type="email" v-model="solicitud.correo_contacto" placeholder="Correo de contacto" required>
            </div>
            <div class="form-group col-md-6 mt-2" v-if="Campos.length > 0">
                <label for="" >Subir archivos de ayuda</label>
                <div class="custom-file" >
                    <input type="file" class="custom-file-input" id="customFileLang" v-on:change="fileChangeFormato"  multiple>
                    <label class="custom-file-label" id="label_formato" for="customFileLang" data-browse="Seleccionar" >Seleccionar Archivos</label>
                    <small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb</small>
                </div>
            </div>
            <div class="form-group col-md-12 mt-3" v-if="Campos.length > 0">
                <label for="">Descripción</label>
                <textarea class="form-control" name="" id="" rows="4" v-model="solicitud.descripcion" placeholder="Escribe aquí la descripción del problema" required></textarea>
            </div>
            <div class="form-group col-md-12" v-if="Campos.length > 0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="acepto" value="acepto" v-model="solicitud.necesita_respuesta">
                    <label class="form-check-label" for="exampleCheck1">Necesita respuesta</label>
                </div>
            </div>
        </div>
        <button type="submit" v-if="Campos.length > 0" class="btn btn-primary" id="btnGuardar">Guardar</button>
    </form>
</div>
<script src="{{asset('assets/vue/alta_solicitud.js')}}"></script>
@endsection