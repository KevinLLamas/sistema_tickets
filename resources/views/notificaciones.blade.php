@extends('app')
@section('content')


<!-- Begin Page Content -->



<div id="notificaciones_listado" class="container-fluid">
    <div class="row">
        <!-- Tarjeta Listado de solicitudes -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tus Notificaciones</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <button class="dropdown-item" @click="getNotificaciones()">Recargar</button>
                            <button class="dropdown-item" v-show="!ocultarLista" @click="ocultarLista = true">Ocultar</button>
                            <button class="dropdown-item" v-show="ocultarLista" @click="ocultarLista = false">Mostrar</button>
                        
                        </div>
                    </div>
                </div>
                <div class="card-body" v-show="!ocultarLista">
                    <div class=" mt-2  container-fluid border-bottom">
                        <h1>Listado de Notificaciones</h1>
                        <div class="form-row">
                            <div class="form-group col-lg-3">
                                <div class="form-group">
                                    <label for="">Busqueda por ID</label>
                                    <input type="text"
                                        class="form-control" name="busquedaid4" id="busquedaid4" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getNotificaciones">
                                    <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                                </div>
                            </div>
                            <div class="form-group col-md-5">
                                <div class="form-group">
                                    <label for="">Busqueda por Descripcion</label>
                                    <input type="text"
                                        class="form-control" name="busqueda" id="busqueda" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getNotificaciones">
                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                </div>
                            </div>
                        </div>
                        <div v-for="(notificacion, index) in notificaciones" v-if="index < 10">
                            <a :class="'mb-2 card container '+ tipo(notificacion.atencion.tipo_at)+' '+esLeida(notificacion.status)"  v-on:click="verSolicitud(notificacion.id,notificacion.id_solicitud)">
                                <div class="mt-1" v-if="notificacion.atencion.tipo_at == 'Atencion'">
                                <div class="icon-circle bg-primary">
                                    <i class="far fa-comment-dots text-white"></i>
                                </div>
                                </div>
                                <div class=" mt-1" v-if="notificacion.atencion.tipo_at == 'Asignacion'">
                                <div class="icon-circle bg-success">
                                    <i class="fas fa-briefcase text-white"></i>
                                </div>
                                </div>
                                <div class=" mt-1" v-if="notificacion.atencion.tipo_at == 'Estatus'">
                                <div class="icon-circle bg-info">
                                    <i class="fas fa-exchange-alt text-white"></i>
                                </div>
                                </div>
                                <div >
                                <div class="font-weight-bold">Ticket #@{{notificacion.id_solicitud}}</div>
                                <div class="small text-gray-500">@{{notificacion.creador.correo}}</div>
                                <span class="small text-gray-500" v-if="notificacion.atencion.tipo_at == 'Atencion'"> dice:</span>
                                <span class="font-weight-bold">@{{notificacion.atencion.detalle}}</span>
                                <div class="small text-gray-500">@{{notificacion.atencion.momento}}</div>
                                
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</div>
<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<!-- Scripts VUE -->
<script src="{{asset('assets/vue/listado_notificaciones.js')}}"></script>
@endsection
