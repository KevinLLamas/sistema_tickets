@extends('app')
@section('content')
<div id="app" >
    <div class="col-md-12 mt-3">
        <ol class="breadcrumb w-100">
            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
            <li class="breadcrumb-item">Seguimiento Externo</li>
        </ol>
    </div>
    <form v-if="!banVerif"  class="container mb-5" id="alumno" v-on:submit.prevent="verificar()">
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
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="{{$id}}" name="id_orig" id="id_orig">
    </form>

    <div class="container" v-if="banVerif">
        <!-- Page Heading -->
	<h1 class="h1 text-gray-800">@{{ seguimiento.descripcion }}</h1>
	<div v-if="seguimiento.estatus">
		<!--select class="selectpicker my-2" data-style="btn-primary" v-model="seguimiento.estatus" @change="cambiarEstatus">
			<option value="" disabled>Seleccione una opción</option>
			<option option="Sin atender">Sin atender</option>
			<option option="Atendiendo">Atendiendo</option>
			<option option="Suspendida">Suspendida</option>
			<option option="Cerrada">Cerrada</option>
		</select-->
		<select  class="form-control col-md-5" v-model="seguimiento.estatus" @change="cambiarEstatus()" disabled="true">
			<option value="" disabled>Seleccione una opción</option>
			<option option="Sin atender">Sin atender</option>
			<option option="Atendiendo">Atendiendo</option>
			<option option="Suspendida">Suspendida</option>
			<option option="Cerrada">Cerrada</option>
		</select>
	</div>
	<p><i class="far fa-clock"></i> 10-01-20 10:53 - Atendiendo: Juan López García
		<!--select class="selectpicker" data-live-search="true">
			<option data-tokens="ketchup mustard">Juan López García</option>
			<option data-tokens="mustard">Armando González Gutierrez</option>
			<option data-tokens="frosting">Luis Márquez Hernández</option>
		</select--></p>
	{{--<p class="alert alert-info"><small>Categoría: Correo institucional - Subcategoría: @{{seguimiento.subcategoria.nombre}}
			electrónico <i class="fas fa-reply-all"></i> Respuestas: 2</small></p>--}}
	<hr>

	<h3 class="h3 text-gray-800">Resumen</h3>
	<div v-if="seguimiento.usuario"  class="card bg-white ">

		<div class="card-body">
			<p><i class="far fa-edit"></i> <b>@{{seguimiento.fecha_creacion}} - Ticket creado por @{{seguimiento.usuario.correo}}</b></p>
			<p class="card-text">@{{seguimiento.descripcion}}</p>
			<p>
				<small>
					Datos adicionales: 
					<div v-for="item in seguimiento.dato_adicional">
						<div v-if="item.tipo_dato == 'correo_institucional'">
							<i class="fas fa-at"></i><h2>Correo institucional: @{{item.valor}} </h2>
						</div>
						<div v-else-if="item.tipo_dato == 'curp'">
							<i class="far fa-id-badge"></i> CURP: <b>@{{item.valor}} </b>
						</div>
						<div v-else-if="item.tipo_dato == 'telefono'">
							<i class="fas fa-mobile-alt"></i> Celular: <b>@{{item.valor}} </b>
						</div>
						<div v-else-if="item.tipo_dato == 'matricula'">
							<i class="fas fa-mobile-alt"></i> Matricula: <b>@{{item.valor}} </b>
						</div>
					</div>
				</small>
			</p>
		</div>
	</div>
	<hr>

					<!--
					-->
	<div v-for="item in seguimiento.atencion">
		<div class="card bg-white mb-3" v-if="item.tipo_respuesta == 'Todos' && item.tipo_at != 'Estatus'">			
			<div class="card-body">
				<p v-if="item.tipo_at == 'Atencion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Comentario agregado por @{{seguimiento.usuario.correo}}</b></p>
				<p v-if="item.tipo_at == 'Estatus'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Estatus cambiado por @{{seguimiento.usuario.correo}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>			
		</div>		
		<!--div class="card alert alert-warning  border-warning mb-3" v-if="item.tipo_respuesta == 'Interna'">
			<div class="card-body">
				<p><i class="far fa-edit"></i> <b>@{{item.momento}} - Comentario agregado por @{{seguimiento.usuario.correo}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>		
		</div-->
	</div>


	<div v-if="false" class="form-group row">
		<label for="prioridad" class="col-1 col-form-label">Prioridad</label>
		<div class="col-3">
			<select class="selectpicker my-2" data-style="btn-primary">
				<option option="Sin atender">Sin atender</option>
				<option option="Atendiendo">Atendiendo</option>
				<option option="Suspendida">Suspendida</option>
				<option option="Cerrada">Cerrada</option>
			</select>
		</div>
		<label for="grupo" class="col-1 col-form-label">Grupo</label>
		<div class="col-3">
			<select class="selectpicker my-2" data-style="btn-primary">
				<option option="Sin atender">Sin atender</option>
				<option option="Atendiendo">Atendiendo</option>
				<option option="Suspendida">Suspendida</option>
				<option option="Cerrada">Cerrada</option>
			</select>
		</div>
		<label for="departamento" class="col-1 col-form-label">Departamento</label>
		<div class="col-3">
			<select class="selectpicker my-2" data-style="btn-primary">
				<option option="Sin atender">Sin atender</option>
				<option option="Atendiendo">Atendiendo</option>
				<option option="Suspendida">Suspendida</option>
				<option option="Cerrada">Cerrada</option>
			</select>
		</div>
	</div>




	<ul v-if="seguimiento.estatus != 'Cerrada' && seguimiento.estatus != 'Suspendida'" class="nav nav-tabs mt-5" id="myTab" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
				aria-selected="true">Responder</a>
		</li>

	</ul>
	<div v-if="seguimiento.estatus != 'Cerrada' && seguimiento.estatus != 'Suspendida'" class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
			<div class="form-group row mt-2">

				<div class="col">
					<textarea name="problema" cols="40" rows="5" class="form-control" v-model="nueva_atencion.detalle" placeholder="Agregar una respuesta..."></textarea>
				</div>
			</div>
			<div class="form-group col-md-6 mt-2">	
				<div class="custom-file" >
					<input type="file" class="custom-file-input" id="customFileLang" v-on:change="fileChangeFormato"  multiple>
					<label class="custom-file-label" id="label_formato" for="customFileLang" data-browse="Seleccionar" >Seleccionar Archivos</label>
					<small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb</small>
				</div>
			</div>
			<div class="form-group row">
				<div class="col">
					<div class="dropdown">
						<button class="btn btn-primary" type="button" id="dropdownMenuButton"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-on:click="agregarAtencion('Todos', '')" >
							Responder
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
        
    </div>
    <!-- /.container-fluid -->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="{{asset('assets/vue/seguimiento.js')}}"></script>
@endsection