@extends('app')
@section('content')

<!-- Begin Page Content -->
<div  class="container" id="app" v-cloak>
	<!-- Page Heading -->
	<h1 class="h1 text-gray-800">#@{{ seguimiento.id_solicitud }} @{{ seguimiento.descripcion }}</h1>
	<div v-if="seguimiento.estatus">
		<select  class="form-control col-md-5" v-model="seguimiento.estatus" @change="cambiarEstatus()">
			<option value="" disabled>Seleccione una opción</option>
			<option option="Sin atender">Sin atender</option>
			<option option="Atendiendo">Atendiendo</option>
			<option option="Suspendida">Suspendida</option>
			<option option="Cerrada (En espera de aprobación)">Cerrada (En espera de aprobación)</option>
			<option v-if="seguimiento.estatus === 'Cerrada'" option="Cerrada">Cerrada</option>
		</select>
	</div>
	<p><i class="far fa-clock"></i> @{{seguimiento.fecha_creacion}} - Atendiendo:
		<select v-if="departamentoValido && user.rol === 'ADMIN'" class="selectpicker" data-live-search="true" v-model="integrantesSeleccionados" @change="updateIntegrantes" multiple>
			<option  :value="item.id" v-for="item in departamentoValido.integrantes">@{{item.correo}}</option>
		</select>
		<!--select v-if="departamentos && user.rol === 'SUPER'" class="selectpicker" data-live-search="true" v-model="seguimiento.departamento" multiple>
			<option  :value="item" v-for="item in departamentos">@{{item.nombre}}</option>
		</select-->
		<label v-if="departamentoValido && user.rol != 'ADMIN'" v-for="item in integrantesSeleccionadosCompleto">@{{item.correo}} <br> </label>
	</p>
		
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
							<i class="fas"></i><h2>Correo institucional: <b>@{{item.valor}}</b> </h2>
						</div>
						<div v-else-if="item.tipo_dato == 'curp'">
							<i class="far fa-id-badge"></i> CURP: <b>**********@{{item.valor.slice(10)}} </b>
						</div>
						<div v-else-if="item.tipo_dato == 'telefono'">
							<i class="fas fa-mobile-alt"></i> Celular: ******<b>@{{item.valor.slice(6)}} </b>
						</div>
						<div v-else-if="item.tipo_dato == 'matricula'">
							<i class="fas fa-mobile-alt"></i> Matricula: *****<b>@{{item.valor.slice(5)}} </b>
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
		<div class="card bg-white mb-3" v-if="item.tipo_respuesta == 'Todos'">			
			<div class="card-body">
				<p v-if="item.tipo_at == 'Atencion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Comentario agregado por @{{item.correo_usuario}}</b></p>
				<p v-if="item.tipo_at == 'Estatus'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Estatus cambiado por @{{item.correo_usuario}}</b></p>
				<p v-if="item.tipo_at == 'Creacion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Ticket creado por @{{item.correo_usuario}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'../get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>			
		</div>	
		<div class="card bg-white mb-3" v-if="item.tipo_respuesta == 'Externa' && item.tipo_at != 'Estatus'">			
			<div class="card-body">
				<p v-if="item.tipo_at == 'Atencion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Usuario Contesto</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>			
		</div>	
		<div class="card alert alert-warning  border-warning mb-3" v-if="item.tipo_respuesta == 'Interna'">
			<div class="card-body">
				<p><i class="far fa-edit"></i> <b>@{{item.momento}} - Nota agregada por @{{item.correo_usuario}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'../get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>		
		</div>
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
		<li class="nav-item">
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
				aria-selected="false">Agregar nota interna</a>
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
						<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
							Responder
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<a class="dropdown-item" v-on:click="agregarAtencion('Todos', '')">Responder</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Todos', 'Abrir')">Responder y abrir</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Todos', 'Suspender')">Responder y suspender</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Todos', 'Resolver')">Responder y resolver</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Todos', 'Terminar')">Responder y terminar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

			<div class="form-group row mt-2">

				<div class="col">
					<textarea  name="problema" cols="40" rows="5" class="form-control" v-model="nueva_atencion.detalle" placeholder="Agregar una nota..."></textarea>
				</div>
			</div>
			
			<div class="form-group col-md-6 mt-2">	
				<div class="custom-file" >
					<input type="file" class="custom-file-input" id="customFileLangNotes" v-on:change="fileChangeFormatoNotes"  multiple>
					<label class="custom-file-label" id="label_formato_notes" for="customFileLangNotes" data-browse="Seleccionar" >Seleccionar Archivos</label>
					<small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb</small>
				</div>
			</div>

			<div class="form-group row">
				<div class="col">
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
							Responder
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<a class="dropdown-item" v-on:click="agregarAtencion('Interna', '')">Responder</a>
							<a v-if="" class="dropdown-item" v-on:click="agregarAtencion('Interna', 'Abrir')">Responder y abrir</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Interna', 'Suspender')">Responder y suspender</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Interna', 'Resolver')">Responder y resolver</a>
							<a class="dropdown-item" v-on:click="agregarAtencion('Interna', 'Terminar')">Responder y terminar</a>
						</div>
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

<script type="text/javascript" src="{{asset('assets/vue/seguimiento.js')}}"></script>

@endsection
