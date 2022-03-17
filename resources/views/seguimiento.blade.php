@extends('app')
@section('content')
<script src="https://unpkg.com/slim-select@1.25.0/dist/slimselect.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/css/time-line.css')}}">
<!-- Begin Page Content -->
<div  class="container" id="app" >
	<div class="row mb-3">
		<div class="card rounded col-md-8">
			<div class="card-body">
				<h3  class="text-secondary" v-if="seguimiento.id_solicitud">@{{ seguimiento.id_solicitud }}  - @{{ seguimiento.subcategoria.nombre }}</h3> 
				{{--<div class="d-flex text-start">
					<img class="img-sm rounded-10" :src="'/assets/'+s.usuario.path_foto" alt="profile" v-if="s.usuario.path_foto">
					<img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!s.usuario.path_foto">
					<div class="wrapper ">
						<p class="ms-1 mb-1 fw-bold" v-if="s.usuario">@{{s.usuario.nombre}}</p>
						<small class="text-muted ms-1" v-if="s.usuario">@{{s.usuario.rol}}</small>
					  </div>
				  </div>
				<b class="text-secondary">@{{seguimiento.fecha_creacion}}</b> --}} 
				<div v-if="seguimiento.estatus">
					<select  class="form-control col-md-5" v-model="seguimiento.estatus" @change="cambiarEstatus()" style="color:black;">
						<option value="" disabled>Seleccione una opción</option>
						<option option="Sin atender">Sin atender</option>
						<option option="Atendiendo">Atendiendo</option>
						<option option="Suspendida">Suspendida</option>
						<option option="Cerrada (En espera de aprobación)">Cerrada (En espera de aprobación)</option>
						<option v-if="seguimiento.estatus === 'Cerrada'" option="Cerrada">Cerrada</option>
					</select>
				</div>
				<p  v-if="user.rol != 'TECNICO'" class="mt-3" v-cloak>
					
					<div class="row">
						<div class="form-group col-6" v-if="seguimiento.perfil != ''">
							<label for="" class="text-secondary">Categoria del problema</label>
							<select class="form-control" name="categoria" id="categoria" v-model="seguimiento.categoria.id" @change="getSubcategorias('front')" required style="color:black;">
								<option value="" selected="selected" disabled>Selecciona</option>
								<option :value="categoria.id" v-for="categoria in Categorias">@{{categoria.nombre}}</option>
							</select>
						</div>
						<div class="form-group col-6" v-if="seguimiento.categoria != ''">
							<label for="" class="text-secondary">Subcategoria del problema</label>
							<select class="form-control" name="subcategoria" id="subcategoria" v-model="seguimiento.subcategoria.id" @change="UpdateSubcategoria()" required style="color:black;">
								<option value="" selected="selected" disabled>Selecciona</option>
								<option :value="subcategoria.id" v-for="subcategoria in Subcategorias">@{{subcategoria.nombre}}</option>
							</select>
						</div>
					</div>
					<b v-if="integrantesSeleccionados.length > 0">Atendiendo: </b>
					{{--<div class="row">
						<!--b v-if="integrantesSeleccionados.length == 0 && user.rol=='TECNICO'">Sin usuarios asignados.</b><br-->
						<select class="form-group col-md-3" v-model="seguimiento.departamentos_seleccionados_id" id="asignar_departamento" @change="updateDepartamento" multiple>">
							<option  :value="item.id_departamento" v-for="item in seguimiento.subcategoria_departamento">@{{item.nombre_departamento}}</option>
						</select>
						<select class="form-group col-md-6" v-model="integrantesSeleccionados" id="agregar_usuarios" @change="updateIntegrantes" multiple>">
							<option  :value="item.id_sgu" v-for="item in departamentoValido.integrantes">@{{item.nombre}}</option>
						</select>
					</div>--}}
				</p>
			</div>
		</div>
		<div class=" col-md-4" >
			<div class="card rounded col-md-11 float-end">
				<div class="card-header">
					<p class="text-secondary">Decripción </p>
				</div>
				<div class="card-body row">
					<div class="col-md-2">
						<img class="img-sm rounded-10" :src="'/assets/'+seguimiento.usuario.path_foto" alt="profile" v-if="seguimiento.usuario.path_foto">
						<img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!seguimiento.usuario.path_foto">
					</div>
					<div class="col-md-10">
						<p class="ms-1 mb-1 fw-bold" v-if="seguimiento.usuario">@{{seguimiento.usuario.nombre}} (Creador)</p>
						<small class="text-muted ms-1" v-if="seguimiento.usuario">@{{seguimiento.usuario.rol}}</small>
					</div>
					<div class="col-md-12 mt-4">
						<p class="fw-bold">Descripción:</p>
						<small>@{{seguimiento.descripcion}}</small>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row d-flex justify-content-center ">
		<div class="main-card mb-3 card rounded col-md-8">
			<div class="card-body">
				<h5 class="card-title">Historial</h5>
				<div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
					<div v-for="item in seguimiento.atencion">
						<div v-if="item.tipo_at == 'Creacion'" class="vertical-timeline-item vertical-timeline-element" >
							<div> 
								<span class="vertical-timeline-element-icon bounce-in badge bg-primary rounded-pill"><i class="mdi mdi-adjust"></i></span>
								<div class="vertical-timeline-element-content bounce-in">
									<h4 class="timeline-title">
										<img class="img-sm2 rounded-10" :src="'/assets/'+item.path_foto" alt="profile" v-if="item.path_foto">
										<img class="img-sm2 rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!item.path_foto">
										@{{item.nombre}}
									</h4>
									<p class="ms-3"> @{{item.detalle}}</p>
									<span class="vertical-timeline-element-date">@{{item.momento.split(' ')[1].slice(0, -3) }}</span>
								</div>
							</div>
						</div>
						<div v-if="item.tipo_at == 'Atencion'" class="vertical-timeline-item vertical-timeline-element" >
							<div> 
								<span class="vertical-timeline-element-icon bounce-in badge bg-info rounded-pill"><i class="mdi mdi-adjust"></i></span>
								<div class="vertical-timeline-element-content bounce-in">
									
									<h4 class="timeline-title">
										<img class="img-sm2 rounded-10" :src="'/assets/'+item.path_foto" alt="profile" v-if="item.path_foto">
										<img class="img-sm2 rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!item.path_foto">
										@{{item.nombre}}
									</h4>
									<p class="ms-3"> @{{item.detalle}}</p>
									<span class="vertical-timeline-element-date">@{{item.momento.split(' ')[1].slice(0, -3) }}</span>
								</div>
							</div>
						</div>
						<div v-if="item.tipo_at == 'Estatus'" class="vertical-timeline-item vertical-timeline-element" >
							<div> 
								<span class="vertical-timeline-element-icon bounce-in badge bg-info rounded-pill"><i class="mdi mdi-adjust"></i></span>
								<div class="vertical-timeline-element-content bounce-in">
									<h4 class="timeline-title">
										<img class="img-sm2 rounded-10" :src="'/assets/'+item.path_foto" alt="profile" v-if="item.path_foto">
										<img class="img-sm2 rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!item.path_foto">
										@{{item.nombre}}
									</h4>
									<p class="ms-3"> @{{item.detalle}}</p>
									<span class="vertical-timeline-element-date">@{{item.momento.split(' ')[1].slice(0, -3) }}</span>
								</div>
							</div>
						</div>
						<div v-if="item.tipo_at == 'Asignacion'" class="vertical-timeline-item vertical-timeline-element" >
							<div> 
								<span class="vertical-timeline-element-icon bounce-in badge bg-info rounded-pill"><i class="mdi mdi-adjust"></i></span>
								<div class="vertical-timeline-element-content bounce-in">
									<h4 class="timeline-title">
										<img class="img-sm2 rounded-10" :src="'/assets/'+item.path_foto" alt="profile" v-if="item.path_foto">
										<img class="img-sm2 rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!item.path_foto">
										@{{item.nombre}}
									</h4>
									<p class="ms-3"> @{{item.detalle}}</p>
									<span class="vertical-timeline-element-date">@{{item.momento.split(' ')[1].slice(0, -3) }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4 mb-3" >
			<div class="card rounded col-md-11 float-end mb-2" v-if="seguimiento.path_evidencia">
				<div class="card-header">
					<p class="text-secondary">Imagen de evidencia</p>
				  </div>
				<img class="imagen-chica rounded-bottom" :src="'/assets/'+seguimiento.path_evidencia" alt="profile" >
			</div>
			<div class="card rounded col-md-11 float-end mb-2">
				<div class="card-header">
					<p class="text-secondary">Ubicación </p>
					
				  </div>
				<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>

				<div id="defaultMapId" class="rounded-bottom" ></div>
				<!-- Make sure you put this AFTER Leaflet's CSS -->
			</div>
		</div>
	</div>
	<div class="row">
		<div v-if="seguimiento.estatus != 'Cerrada'  && seguimiento.estatus != 'Suspendida'" class="card rounded" id="myTabContent">
			<div class="card-body" id="home" role="tabpanel" aria-labelledby="home-tab">
				<div class="form-group row ">

					<div class="col">
						<textarea name="problema" cols="40" rows="5" class="form-control" v-model="nueva_atencion.detalle" placeholder="Agregar una respuesta..."></textarea>
					</div>
				</div>
				<div class="form-group col-md-12 mt-2">
					<label>Selecciona un archivo de evidencia</label>
					<input type="file" class="file-upload-default" v-on:change="fileChangeFormato">
					<div class="input-group col-xs-12 input-group-md">
						<input type="text" class="form-control file-upload-info" disabled placeholder="Seleccionar">
						<span class="input-group-append">
						<button class="file-upload-browse btn btn-primary btn-sm ms-1" type="button">Seleccionar</button>
						</span>
					</div>
					<small class="text-muted">Extensiones permitidas (pdf , png, jpg, jpeg, xls), El tamaño máximo por archivo es de 3 Mb y se permiten máximo 4 archivos.</small>
				</div>
				<div class="form-group float-end">
					<div class="col">
						<button class="btn btn-primary" type="button"  >
							Agregar comentario
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.container-fluid -->


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
<script src="{{asset('assets/vue/seguimiento.js')}}"></script>
<script type="" src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
			<script type="application/javascript">
				var mymap = L.map('defaultMapId').setView([20.728143692017, -103.38552093506], 13);
						var marker = L.marker([20.728143692017, -103.38552093506]).addTo(mymap);
				
						var url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
					L.tileLayer(url, {
					maxZoom: 18,
					attribution: '',
					id: 'mapbox/streets-v11',
					tileSize: 512,
					zoomOffset: -1
				}).addTo(mymap);
			</script>
{{--<script >
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
</script>--}}
@endsection
