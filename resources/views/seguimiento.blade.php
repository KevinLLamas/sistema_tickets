@extends('app')
@section('content')
<script src="https://unpkg.com/slim-select@1.25.0/dist/slimselect.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/slim.css')}}">
<!-- Begin Page Content -->
<div  class="container" id="app" v-cloak>
        <ol class="breadcrumb w-100">
            <li class="breadcrumb-item"><a href="/sass">Inicio</a></li>
            <li class="breadcrumb-item">Seguimiento</li>
        </ol>
	<!-- Page Heading -->
	<h1  class="h1 text-gray-800" v-if="seguimiento.id_solicitud">#@{{ seguimiento.id_solicitud }} - @{{ seguimiento.perfil.nombre }} - @{{ seguimiento.categoria.nombre }} - @{{ seguimiento.subcategoria.nombre }}</h1>
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
	<p v-if="user.rol != 'TECNICO'" class="mt-3" v-cloak>
		<i class="far fa-clock"></i> 
		<b>@{{seguimiento.fecha_creacion}}</b> - 
		<div v-if="user.rol != 'TECNICO'" class="row">
            <div class="form-group col-6" v-if="seguimiento.perfil != ''">
                <label for="">Categoria del problema</label>
                <select class="form-control" name="categoria" id="categoria" v-model="seguimiento.categoria.id" @change="getSubcategorias('front')" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="categoria.id" v-for="categoria in Categorias">@{{categoria.nombre}}</option>
                </select>
            </div>
            <div class="form-group col-6" v-if="seguimiento.categoria != ''">
                <label for="">Subcategoria del problema</label>
                <select class="form-control" name="subcategoria" id="subcategoria" v-model="seguimiento.subcategoria.id" @change="UpdateSubcategoria()" required>
                    <option value="" selected="selected" disabled>Selecciona</option>
                    <option :value="subcategoria.id" v-for="subcategoria in Subcategorias">@{{subcategoria.nombre}}</option>
                </select>
			</div>
		</div>
		<b v-if="integrantesSeleccionados.length > 0">Atendiendo: </b>
		<div v-if="user.rol != 'TECNICO'" class="row">
			<b v-if="integrantesSeleccionados.length == 0 && user.rol=='TECNICO'">Sin usuarios asignados.</b><br>
			<select class="form-group col-6" v-model="seguimiento.departamentos_seleccionados_id" id="asignar_departamento" @change="updateDepartamento" multiple>">
				<option  :value="item.id_departamento" v-for="item in seguimiento.subcategoria_departamento">@{{item.nombre_departamento}}</option>
			</select>
			<select class="form-group col-6" v-model="integrantesSeleccionados" id="agregar_usuarios" @change="updateIntegrantes" multiple>">
				<option  :value="item.id_sgu" v-for="item in departamentoValido.integrantes">@{{item.nombre}}</option>
			</select>
			<b><label v-if="integrantesSeleccionadosCompletoSolicitud && item.id_departamento != user.id_departamento" v-for="item in integrantesSeleccionadosCompletoSolicitud">@{{item.nombre}}, </label></b>
		</div>
	</p>
	<p v-if="user.rol == 'TECNICO'">
		<i class="far fa-clock"></i> 
		<b>@{{seguimiento.fecha_creacion}}</b> - 
		<b v-if="integrantesSeleccionados.length > 0">Atendiendo: </b>
		<b v-if="integrantesSeleccionados.length == 0 && user.rol=='TECNICO'">Sin usuarios asignados.</b>
		<b><label v-if="integrantesSeleccionadosCompletoSolicitud" v-for="item in integrantesSeleccionadosCompletoSolicitud">@{{item.nombre}}, </label></b>
	</p>
	
	<hr>
	<h3 class="h3 text-gray-800">Resumen</h3>
	<div   class="card bg-white ">

		<div class="card-body">
			<p v-if="seguimiento.usuario" ><i class="far fa-edit"></i> <b>@{{seguimiento.fecha_creacion}} - Ticket creado por @{{seguimiento.usuario.nombre}}</b></p>
			<p class="card-text">@{{seguimiento.descripcion}}</p>
			<p>
				<small>
					Datos adicionales: 
					<div v-if="seguimiento.correo_atencion">
						<i class="far fa-envelope"></i>Correo contacto: <b>@{{seguimiento.correo_atencion}}</b>
					</div>
					<div v-for="item in seguimiento.dato_adicional">
						<div v-if="item.tipo_dato == 'correo_institucional'">
							<i class="far fa-envelope"></i>Correo institucional: <b>@{{item.valor}}</b>
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
						<div v-else-if="item.tipo_dato == 'n_plaza'">
							<i class="fas fa-list-ol"></i> Número de plaza: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'cct'">
							<i class="fas fa-list-ol"></i>Clave de centro de trabajo: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'alumno'">
							<i class="fas fa-list-ol"></i>Nombre del alumno: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'grado'">
							<i class="fas fa-list-ol"></i> Grado: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'grupo'">
							<i class="fas fa-list-ol"></i> grupo: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'turno'">
							<i class="fas fa-list-ol"></i> Turno: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'director'">
							<i class="fas fa-list-ol"></i> Nombre del director: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'dato incorrecto'">
							<i class="fas fa-list-ol"></i> Dato incorrecto: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'dato correcto'">
							<i class="fas fa-list-ol"></i> Dato correcto: <b>@{{item.valor}} </b>
                        </div>
                        <div v-else-if="item.tipo_dato == 'fecha de alta'">
							<i class="fas fa-list-ol"></i>Fecha de alta: <b>@{{item.valor}} </b>
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
				<p v-if="item.tipo_at == 'Atencion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Comentario agregado por @{{item.nombre}}</b></p>
				<p v-if="item.tipo_at == 'Estatus'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Estatus cambiado por @{{item.nombre}}</b></p>
				<p v-if="item.tipo_at == 'Creacion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Ticket creado por @{{item.nombre}}</b></p>
				<p v-if="item.tipo_at == 'Asignacion' && item.nombre != 'Sistema'"><i class="far fa-edit"></i> <b>@{{item.momento}} - El usuario @{{item.nombre}}</b></p>
				<p v-if="item.tipo_at == 'Asignacion' && item.nombre == 'Sistema'"><i class="far fa-edit"></i> <b>@{{item.momento}} - El @{{item.nombre}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/sass/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>			
		</div>	
		<div class="card bg-white mb-3" v-if="item.tipo_respuesta == 'Externa' && item.tipo_at != 'Estatus'">			
			<div class="card-body">
				<p v-if="item.tipo_at == 'Atencion'"><i class="far fa-edit"></i> <b>@{{item.momento}} - Usuario Contesto</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/sass/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
				</div>				
			</div>			
		</div>	
		<div class="card alert alert-warning  border-warning mb-3" v-if="item.tipo_respuesta == 'Interna'">
			<div class="card-body">
				<p><i class="far fa-edit"></i> <b>@{{item.momento}} - Nota agregada por @{{item.nombre}}</b></p>
				<p class="card-text">@{{item.detalle}}</p>
				<p v-if="item.adjuntos.length != 0" class="card-text">Documentos Adjuntos:</p>
				<div v-for="adj in item.adjuntos">
					<a :href="'/sass/get_file/solicitud-' + seguimiento.id_solicitud + '/' + adj.nombre_documento" download=""></i> @{{adj.nombre_documento}} </a>
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




	<ul v-if="seguimiento.estatus != 'Cerrada' && seguimiento.estatus != 'Cerrada (En espera de aprobación)' && seguimiento.estatus != 'Suspendida'" class="nav nav-tabs mt-5" id="myTab" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
				aria-selected="true">Responder</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
				aria-selected="false">Agregar nota interna</a>
		</li>

	</ul>
	<div v-if="seguimiento.estatus != 'Cerrada' && seguimiento.estatus != 'Cerrada (En espera de aprobación)' && seguimiento.estatus != 'Suspendida'" class="tab-content" id="myTabContent">
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
					<small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb y se permiten máximo 4 archivos.</small>
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
					<small>Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb y se permiten máximo 4 archivos.</small>
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
<script >
    slim = new SlimSelect({
                select: '#agregar_usuarios',
                placeholder: 'Asignar Ticket',
                limit: 4,
              })                    
</script>
<script>
    slim2 = new SlimSelect({
                select: '#asignar_departamento',
                placeholder: 'Asignar a Departamento',
                limit: 4,
              })                    
</script>
@endsection
