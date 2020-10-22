@extends('app')
@section('content')


<!-- Begin Page Content -->
<div  class="container">
    <!-- Content Row -->
    <div class="row">
    </div>    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                aria-selected="true">Solicitudes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                aria-selected="false">Solicitudes Asignadas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="profile2"
                aria-selected="false">Solicitudes Departamento</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile3-tab" data-toggle="tab" href="#profile3" role="tab" aria-controls="profile3"
                aria-selected="false">Mis Solicitudes</a>
        </li>

    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="form-group row mt-3">
                <div id="solicitudes" class="container-fluid">
                    <div class="row">
                        <!-- Tarjeta Listado de solicitudes -->
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Solicitudes</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" @click="getSolicitudesAdmin()">Recargar</button>
                                            <button class="dropdown-item" v-show="!ocultarListaSolicitudes" @click="ocultarListaSolicitudes = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarListaSolicitudes" @click="ocultarListaSolicitudes = false">Mostrar</button>
                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body" v-show="!ocultarListaSolicitudes">
                                    <div class=" mt-4 mb-2  container-fluid border-bottom">
                                        <h1>Listado de Solicitudes</h1>
                                        <div class="form-row">
                                            <div class="form-group col-md-1">
                                                <label for="">Paginado</label>
                                                <select class="form-control" name="" id="" v-model="numFiltro" @change="getSolicitudesAdmin">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="" id="" v-model="medioReporte" @change="getSolicitudesAdmin">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Internet'>Internet</option>
                                                    <option value='Personal'>Personal</option>
                                                    <option value='Llamada'>Llamada</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="">Estado</label>
                                                <select class="form-control" name="" id="" v-model="estadoReporte" @change="getSolicitudesAdmin">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group col-md-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid1" id="busquedaid1" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAdmin">
                                                    <small id="helpId" class="form-text text-muted">Escribe el ID</small>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda1" id="busqueda1" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAdmin">
                                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-small">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Asignada a</th>
                                                    <th>Descripcion</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                    <th>Medio</th>
                                                    <th>Responder</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="s in Solicitudes">
                                                    <td>@{{s.id_solicitud}}</td>
                                                    <td>
                                                        <label v-for="u in s.usuario_many">@{{u.correo}}</label>
                                                        <label v-if="s.usuario_many.length == 0">Sin asignar</label>
                                                    </td>
                                                    <td>@{{s.descripcion}}</td>
                                                    <td>@{{s.fecha_creacion}}</td>
                                                    <td>@{{s.estatus}}</td>
                                                    <td>@{{s.medio_reporte}}</td>
                                                    <td><a type="button" class="btn btn-outline-primary" :href="'seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        <div class="text-center font-weight-bold">
                                            Mostrando @{{pagination.per_page}} de @{{pagination.total}}
                                        </div>
                                        <!--Paginación-->
                                        <div class="d-flex justify-content-center">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">
                                                    <li v-if="pagination.current_page > 1" class="page-item">
                                                        <a @click.prevent="siguientePagina(pagination.current_page -1)" class="page-link" href="#">Anterior</a>
                                                    </li>
                                
                                                    <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']" class="page-item">
                                                        <a class="page-link" href="#" @click.prevent="siguientePagina(page)">@{{page}}</a>
                                                    </li>
                                
                                                    <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                                                        <a  @click.prevent="siguientePagina(pagination.current_page + 1)"class="page-link" href="#">Siguiente</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <!---->
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <!-- Grafica Dona numero De Solicitudes por tipo -->
                        <div class="col-xl-12 col-lg-5" id="graficas">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Solicitudes</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-3">
                                        <canvas id="SolicitudesAdminChart"></canvas>
                                    </div>
                                    <div class="mt-5 text-center small">
                                        <span class="mr-2" v-for="(e,index) in Estatus">
                                            <i :id="index" :class="['fas fa-circle',asignarColor(e.estatus)]"></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    
                </div>
            </div>
            
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            <div class="form-group row mt-3">
                <div id="mis_solicitudes_asignadas" class="container-fluid">
                    <div class="row">
                        <!-- Tarjeta Listado de solicitudes -->
                        <div class="col-xl-12 col-lg-9">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Solicitudes Asignadas</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" v-show="!ocultarTabla" @click="ocultarTabla = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarTabla" @click="ocultarTabla = false">Mostrar</button>
                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body" v-show="!ocultarTabla">
                                    <div class=" mt-4 mb-2  container-fluid border-bottom">
                                        <h1>Solicitudes Asignadas</h1>
                                        <div class="form-row">
                                            <div class="form-group col-md-1">
                                                <label for="">Paginado</label>
                                                <select class="form-control" name="" id="" v-model="numFiltro" @change="getSolicitudesAsignadas">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="" id="" v-model="medioReporte" @change="getSolicitudesAsignadas">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Internet'>Internet</option>
                                                    <option value='Personal'>Personal</option>
                                                    <option value='Llamada'>Llamada</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="">Estado</label>
                                                <select class="form-control" name="" id="" v-model="estadoReporte" @change="getSolicitudesAsignadas">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid2" id="busquedaid2" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAsignadas">
                                                    <small id="helpId" class="form-text text-muted">Escribe el ID</small>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda2" id="busqueda2" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAsignadas">
                                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <table class="table table-small" >
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Descripcion</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                    <th>Medio</th>
                                                    <th>Responder</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                <tr v-for="s in MisSolicitudes">
                                                    <td>@{{s.id_solicitud}}</td>
                                                    
                                                    <td>@{{s.descripcion}}</td>
                                                    <td>@{{s.fecha_creacion}}</td>
                                                    <td>@{{s.estatus}}</td>
                                                    <td>@{{s.medio_reporte}}</td>
                                                    <td><a type="button" class="btn btn-outline-primary" :href="'seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        <div class="text-center font-weight-bold">
                                            Mostrando @{{pagination.per_page}} de @{{pagination.total}}
                                        </div>
                                        <!--Paginación-->
                                        <div class="d-flex justify-content-center">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">
                                                    <li v-if="pagination.current_page > 1" class="page-item">
                                                        <a @click.prevent="siguientePagina(pagination.current_page -1)" class="page-link" href="#">Anterior</a>
                                                    </li>
                                
                                                    <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']" class="page-item">
                                                        <a class="page-link" href="#" @click.prevent="siguientePagina(page)">@{{page}}</a>
                                                    </li>
                                
                                                    <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                                                        <a  @click.prevent="siguientePagina(pagination.current_page + 1)"class="page-link" href="#">Siguiente</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <!---->
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>    
                    <div class="row">
                        <!-- Grafica Dona numero De Solicitudes por tipo -->
                        <div class="col-xl-12 col-lg-5" id="graficas">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Solicitudes Asignadas</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-3">
                                        <canvas id="SolicitudesAsignadasChart"></canvas>
                                    </div>
                                    <div class="mt-5 text-center small">
                                        <span class="mr-2" v-for="(e,index) in Estatus">
                                            <i :id="index" :class="['fas fa-circle',asignarColor(e.estatus)]"></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
            <div class="form-group row mt-3">
                <div id="solicitudes_departamento" class="container-fluid">
                    <div class="row">
                        <!-- Tarjeta Listado de solicitudes -->
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Solicitudes Departamento</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" @click="getSolicitudes()">Recargar</button>
                                            <button class="dropdown-item" v-show="!ocultarListaSolicitudes" @click="ocultarListaSolicitudes = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarListaSolicitudes" @click="ocultarListaSolicitudes = false">Mostrar</button>
                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body" v-show="!ocultarListaSolicitudes">
                                    <div class=" mt-4 mb-2  container-fluid border-bottom">
                                        <h1>Listado de Solicitudes</h1>
                                        <div class="form-row">
                                            <div class="form-group col-md-1">
                                                <label for="">Paginado</label>
                                                <select class="form-control" name="" id="" v-model="numFiltro" @change="getSolicitudesDepartamento">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="" id="" v-model="medioReporte" @change="getSolicitudesDepartamento">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Internet'>Internet</option>
                                                    <option value='Personal'>Personal</option>
                                                    <option value='Llamada'>Llamada</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="">Estado</label>
                                                <select class="form-control" name="" id="" v-model="estadoReporte" @change="getSolicitudesDepartamento">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid3" id="busquedaid3" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesDepartamento">
                                                    <small id="helpId" class="form-text text-muted">Escribe el ID</small>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda3" id="busqueda3" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesDepartamento">
                                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <table class="table table-small">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Asignada a</th>
                                                    <th>Descripcion</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                    <th>Medio</th>
                                                    <th>Responder</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="s in Solicitudes">
                                                    <td>@{{s.id_solicitud}}</td>
                                                    <td>
                                                        <label v-for="u in s.usuario_many">@{{u.correo}}</label>
                                                        <label v-if="s.usuario_many.length == 0">Sin asignar</label>
                                                    </td>
                                                    <td>@{{s.descripcion}}</td>
                                                    <td>@{{s.fecha_creacion}}</td>
                                                    <td>@{{s.estatus}}</td>
                                                    <td>@{{s.medio_reporte}}</td>
                                                    <td><a type="button" class="btn btn-outline-primary" :href="'seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        <div class="text-center font-weight-bold">
                                            Mostrando @{{pagination.per_page}} de @{{pagination.total}}
                                        </div>
                                        <!--Paginación-->
                                        <div class="d-flex justify-content-center">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">
                                                    <li v-if="pagination.current_page > 1" class="page-item">
                                                        <a @click.prevent="siguientePagina(pagination.current_page -1)" class="page-link" href="#">Anterior</a>
                                                    </li>
                                
                                                    <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']" class="page-item">
                                                        <a class="page-link" href="#" @click.prevent="siguientePagina(page)">@{{page}}</a>
                                                    </li>
                                
                                                    <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                                                        <a  @click.prevent="siguientePagina(pagination.current_page + 1)"class="page-link" href="#">Siguiente</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <!---->
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>  
                    <div class="row">
                        <!-- Grafica Dona numero De Solicitudes por tipo -->
                        <div class="col-xl-12 col-lg-5" id="graficas">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Solicitudes Departamento</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-3">
                                        <canvas id="SolicitudesDepartamentoChart"></canvas>
                                    </div>
                                    <div class="mt-5 text-center small">
                                        <span class="mr-2" v-for="(e,index) in Estatus">
                                            <i :id="index" :class="['fas fa-circle',asignarColor(e.estatus)]"></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile3-tab">
            <div class="form-group row mt-3">
                <div id="mis_solicitudes" class="container-fluid">
                    <div class="row">
                        <!-- Tarjeta Listado de solicitudes -->
                        <div class="col-xl-12 col-lg-9">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Mis Solicitudes</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" v-show="!ocultarTabla" @click="ocultarTabla = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarTabla" @click="ocultarTabla = false">Mostrar</button>
                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body" v-show="!ocultarTabla">
                                    <div class=" mt-4 mb-2  container-fluid border-bottom">
                                        <h1>Mis Solicitudes</h1>
                                        <div class="form-row">
                                            <div class="form-group col-md-1">
                                                <label for="">Paginado</label>
                                                <select class="form-control" name="" id="" v-model="numFiltro" @change="getMisSolicitudes">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="" id="" v-model="medioReporte" @change="getMisSolicitudes">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Internet'>Internet</option>
                                                    <option value='Personal'>Personal</option>
                                                    <option value='Llamada'>Llamada</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="">Estado</label>
                                                <select class="form-control" name="" id="" v-model="estadoReporte" @change="getMisSolicitudes">
                                                    <option value="" disabled>Selecciona</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid4" id="busquedaid4" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getMisSolicitudes">
                                                    <small id="helpId" class="form-text text-muted">Escribe el ID</small>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda4" id="busqueda4" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getMisSolicitudes">
                                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <table class="table table-small" >
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Descripcion</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                    <th>Medio</th>
                                                    <th>Responder</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                <tr v-for="s in MisSolicitudes">
                                                    <td>@{{s.id_solicitud}}</td>
                                                    
                                                    <td>@{{s.descripcion}}</td>
                                                    <td>@{{s.fecha_creacion}}</td>
                                                    <td>@{{s.estatus}}</td>
                                                    <td>@{{s.medio_reporte}}</td>
                                                    <td><a type="button" class="btn btn-outline-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                           
                                            </tbody>
                                        </table>
                                        <div class="text-center font-weight-bold">
                                            Mostrando @{{pagination.per_page}} de @{{pagination.total}}
                                        </div>
                                        <!--Paginación-->
                                        <div class="d-flex justify-content-center">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">
                                                    <li v-if="pagination.current_page > 1" class="page-item">
                                                        <a @click.prevent="siguientePagina(pagination.current_page -1)" class="page-link" href="#">Anterior</a>
                                                    </li>
                                
                                                    <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']" class="page-item">
                                                        <a class="page-link" href="#" @click.prevent="siguientePagina(page)">@{{page}}</a>
                                                    </li>
                                
                                                    <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                                                        <a  @click.prevent="siguientePagina(pagination.current_page + 1)"class="page-link" href="#">Siguiente</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <!---->
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>    
                    <div class="row">
                        <!-- Grafica Dona numero De Solicitudes por tipo -->
                        <div class="col-xl-12 col-lg-5" id="graficas">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Mis Solicitudes</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-3">
                                        <canvas id="MisSolicitudesChart"></canvas>
                                    </div>
                                    <div class="mt-5 text-center small">
                                        <span class="mr-2" v-for="(e,index) in Estatus">
                                            <i :id="index" :class="['fas fa-circle',asignarColor(e.estatus)]"></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                    </div>
                                </div>
                            </div>
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


<!-- Chart.js -->
<script src="{{asset('assets/vendor/chart.js/Chart.min.js')}}"></script>
<!-- Scripts VUE -->
<script src="{{asset('assets/vue/solicitudes.js')}}"></script>
<script src="{{asset('assets/vue/solicitudes_asignadas.js')}}"></script>
<script src="{{asset('assets/vue/solicitudes_departamento.js')}}"></script>
<script src="{{asset('assets/vue/mis_solicitudes.js')}}"></script>

@endsection
