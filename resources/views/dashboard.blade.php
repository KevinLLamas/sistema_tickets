@extends('app')
@section('content')

<!-- Begin Page Content -->
<div  class="container-fluid" >
    <!-- Content Row -->

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Listado de tickets</h4>
            <p class="card-description">Horizontal bootstrap tab</p>
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" v-if="rol == 'SUPER'">
                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home-1" role="tab" aria-controls="home-1" aria-selected="true">Solicitudes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile-1" role="tab" aria-controls="profile-1" aria-selected="false">Solicitudes asignadas</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact-1" role="tab" aria-controls="contact-1" aria-selected="false">Mis solicitudes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="departamento-tab" data-bs-toggle="tab" href="#departamento-1" role="tab" aria-controls="departamento-1" aria-selected="false">Solicitudes departamento</a>
              </li>
            </ul>


            <div class="tab-content">
              <div class="tab-pane fade show active" id="home-1" role="tabpanel" aria-labelledby="home-tab" >
                {{-- Solicitudes --}}

                
            <div class="row py-2">
                
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="paginado1">Paginado</label>
                        <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getSolicitudesAdmin">
                            <option value='10'>10</option>
                            <option value='50'>50</option>
                            <option value='100'>100</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="">Medio de Reporte</label>
                        <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getSolicitudesAdmin">
                            <option value="" >Todos</option>
                            <option value='Sistema'>Sistema</option>
                            <option value='Chatbot'>Chatbot</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="estadoReporte1">Estado</label>
                        <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getSolicitudesAdmin">
                            <option value="">Todos</option>
                            <option value='Sin atender'>Sin atender</option>
                            <option value='Atendiendo'>Atendiendo</option>
                            <option value='Suspendida'>Suspendida</option>
                            <option value='Cancelada'>Cancelada</option>
                            <option value='Cerrada'>Cerrada</option>
                            <option value='Cerrada (En espera de aprobacion)'>Cerrada(En espera de aprobación)</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="busquedaid1">Busqueda por ID</label>
                        <input type="text"
                            class="form-control" name="busquedaid1" id="busquedaid1" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAdmin">
                        <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="busqueda1">Busqueda por Descripcion</label>
                        <input type="text"
                            class="form-control" name="busqueda1" id="busqueda1" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAdmin">
                        <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                    </div>
                </div>
                
            </div>

                <div class="table-responsive" id="solicitudes">
                    <div class="alert alert-warning text-center" role="alert" v-if="Solicitudes.length===0">
                        <strong>Sin resultados</strong>
                    </div>
                    <table class="table" v-if="Solicitudes.length>0">
                        <thead>
                            <tr>
                                <th><button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesAdmin">ID@{{orden=='ASC' ? '↓' : '↑'}}</button></th>
                                <th>Asignado</th>
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
                                <td><a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                            </tr>
                        
                        </tbody>
                    </table>

                    <div class="text-center font-weight-bold" v-if="Solicitudes.length>0">
                        Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
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
                    
                    
                </div>


              </div>
              <div class="tab-pane fade" id="profile-1" role="tabpanel" aria-labelledby="profile-tab">
                {{-- Solicitudes asignadas --}}

                

                <div id="mis_solicitudes_asignadas">
                    <div class="row">
                        <!-- Tarjeta Listado de solicitudes asignadas-->
                        <div class="col-xl-12 col-lg-11">
                            <div class="mb-4">
                                <!-- Card Header - Dropdown -->
                                {{-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" @click="getSolicitudesAsignadas()">Recargar</button>
                                            <button class="dropdown-item" v-show="!ocultarTabla" @click="ocultarTabla = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarTabla" @click="ocultarTabla = false">Mostrar</button>
                                        
                                        </div>
                                    </div>
                                </div> --}}
                                <!-- Card Body -->
                                <div class="" v-show="!ocultarTabla">
                                    <div class="mt-4 mb-2 border-bottom">
                                        
                                        <div class="row">
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                <label for="">Paginado</label>
                                                <select class="form-control" name="" id="" v-model="numFiltro" @change="getSolicitudesAsignadas">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="" id="" v-model="medioReporte" @change="getSolicitudesAsignadas">
                                                    <option value="" >Todos</option>
                                                    <option value='Sistema'>Sistema</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                <label for="">Estado</label>
                                                <select class="form-control" name="" id="" v-model="estadoReporte" @change="getSolicitudesAsignadas">
                                                    <option value="">Todos</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                    <option value='Cerrada (En espera de aprobacion)'>Cerrada(En espera de aprobación)</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid2" id="busquedaid2" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAsignadas">
                                                    <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda2" id="busqueda2" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAsignadas">
                                                    <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <div class="alert alert-warning text-center" role="alert" v-if="MisSolicitudes.length===0">
                                                <strong>Sin resultados</strong>
                                            </div>
                                            <table class="table" v-if="MisSolicitudes.length>0">
                                                <thead>
                                                    <tr>
                                                        <th><button type="button" name="ordenID2" id="ordenID2" class="btn btn-sm btn-outline-primary" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesAsignadas">ID@{{orden=='ASC' ? '↓' : '↑'}}</button></th>
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
                                                        <td><a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                                                    </tr>
                                                
                                                </tbody>
                                            </table>
                                            
                                            
                                        </div>
                                        <div class="text-center font-weight-bold" v-if="MisSolicitudes.length>0">
                                            Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
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
                    {{-- <div class="row">
                        <!-- Grafica Dona numero De Solicitudes por tipo -->
                        <div class="col-xl-12 col-lg-11" id="graficas">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Tickets Asignados</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Opciones:</div>
                                            <button class="dropdown-item" @click="generarGraficaAsignadas()">Recargar Grafica</button>
                                            <button class="dropdown-item" v-show="!ocultarGrafica" @click="ocultarGrafica = true">Ocultar</button>
                                            <button class="dropdown-item" v-show="ocultarGrafica" @click="ocultarGrafica = false">Mostrar</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body" v-show="!ocultarGrafica">
                                    <div class="chart-pie pt-4 pb-3">
                                        <canvas id="SolicitudesAsignadasChart"></canvas>
                                    </div>
                                    <div class="mt-5 text-center small">
                                        <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
                                            <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i>@{{e.estatus}}-@{{e.total}}
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                
                
            
              </div>

              <div class="tab-pane fade" id="contact-1" role="tabpanel" aria-labelledby="contact-tab">
                {{-- Mis solicitudes --}}
              
                            
            <div id="mis_solicitudes" class="container-fluid">
                <div class="row">
                    <!-- Tarjeta Listado de solicitudes Creadas por mi -->
                    <div class="col-lg-12">
                        <div class=" mb-4">
                            <!-- Card Header - Dropdown -->
                            {{-- <div class="py-3 d-flex flex-row align-items-center justify-content-between">
                                
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <button class="dropdown-item" @click="getMisSolicitudes()">Recargar</button>
                                        <button class="dropdown-item" v-show="!ocultarTabla" @click="ocultarTabla = true">Ocultar</button>
                                        <button class="dropdown-item" v-show="ocultarTabla" @click="ocultarTabla = false">Mostrar</button>
                                    
                                    </div>
                                </div>
                            </div> --}}
                            <!-- Card Body -->
                            <div class="" v-show="!ocultarTabla">
                                <div class="mt-4 mb-2 border-bottom">
                                    
                                    <div class="row">
                                        <div class="col-lg-1">
                                            <label for="">Paginado</label>
                                            <select class="form-control" name="" id="" v-model="numFiltro" @change="getMisSolicitudes">
                                                <option value='10'>10</option>
                                                <option value='50'>50</option>
                                                <option value='100'>100</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="">Medio de Reporte</label>
                                            <select class="form-control" name="" id="" v-model="medioReporte" @change="getMisSolicitudes">
                                                <option value="">Todos</option>
                                                <option value='Sistema'>Sistema</option>
                                                <option value='Chatbot'>Chatbot</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="">Estado</label>
                                            <select class="form-control" name="" id="" v-model="estadoReporte" @change="getMisSolicitudes">
                                                <option value="">Todos</option>
                                                <option value='Sin atender'>Sin atender</option>
                                                <option value='Atendiendo'>Atendiendo</option>
                                                <option value='Suspendida'>Suspendida</option>
                                                <option value='Cancelada'>Cancelada</option>
                                                <option value='Cerrada'>Cerrada</option>
                                                <option value='Cerrada (En espera de aprobacion)'>Cerrada(En espera de aprobación)</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="">Busqueda por ID</label>
                                                <input type="text"
                                                    class="form-control" name="busquedaid4" id="busquedaid4" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getMisSolicitudes">
                                                <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Busqueda por Descripcion</label>
                                                <input type="text"
                                                    class="form-control" name="busqueda4" id="busqueda4" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getMisSolicitudes">
                                                <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div class="alert alert-warning text-center" role="alert" v-if="MisSolicitudes.length===0">
                                            <strong>Sin resultados</strong>
                                        </div>
                                        <table class="table" v-if="MisSolicitudes.length>0">
                                            <thead>
                                                <tr>
                                                    <th><button type="button" name="ordenID4" id="ordenID4" class="btn btn-sm btn-outline-primary" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getMisSolicitudes">ID@{{orden=='ASC' ? '↓' : '↑'}}</button></th>
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
                                                    <td><a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        
                                        
                                    </div>
                                    <div class="text-center font-weight-bold" v-if="MisSolicitudes.length>0">
                                        Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
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
                {{-- <div class="row">
                    <!-- Grafica Dona numero De Solicitudes por tipo -->
                    <div class="col-xl-12 col-lg-11" id="graficas">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Grafica de Mis Tickets</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <button class="dropdown-item" @click="generarGraficaMisSolicitudes()">Recargar Grafica</button>
                                        <button class="dropdown-item" v-show="!ocultarGrafica" @click="ocultarGrafica = true">Ocultar</button>
                                        <button class="dropdown-item" v-show="ocultarGrafica" @click="ocultarGrafica = false">Mostrar</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body" v-show="!ocultarGrafica">
                                <div class="chart-pie pt-4 pb-3">
                                    <canvas id="MisSolicitudesChart"></canvas>
                                </div>
                                <div class="mt-5 text-center small">
                                    <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
                                        <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            
        </div>

              </div>{{--cierra contact tab--}}
              
            </div>
          </div>
        </div>




      </div>



    <div id="dashboard">
        <input type="hidden" value="{{Session::get('rol')}}" id="rol" name="rol">
        <ul class="nav nav-tabs" id="myTab" role="tablist" >
            
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="v-pills-profile"
                    aria-selected="false">Tickets Asignados</a>
            </li>
            <li class="nav-item" v-if="rol == 'SUPER'">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                    aria-selected="true">Tickets</a>
            </li>
            <li class="nav-item" v-if="rol == 'ADMIN' || rol == 'JEFE' || rol == 'SUPER'">
                <a class="nav-link" id="profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="profile2"
                    aria-selected="false">Tickets Departamento</a>
            </li>
            <li class="nav-item" >
                <a class="nav-link" id="profile3-tab" data-toggle="tab" href="#profile3" role="tab" aria-controls="profile3"
                    aria-selected="false">Mis Tickets</a>
            </li>

        </ul>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            
            <div id="">
                <div class="row">
                    <!-- Tarjeta Listado de solicitudes general -->
                    <div class="col-xl-12 col-lg-11">
                       
                            <!-- Card Header - Dropdown -->
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                
                                <div class="dropdown no-arrow">
                                  
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
                                <div class="mt-4 mb-2 border-bottom">
                                    <h1>Listado de Tickets</h1>
                                    <div class="form-row">
                                        <div class="form-group col-lg-1">
                                            <div class="form-group">
                                                <label for="paginado1">Paginado</label>
                                                <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getSolicitudesAdmin">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <div class="form-group">
                                                <label for="">Medio de Reporte</label>
                                                <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getSolicitudesAdmin">
                                                    <option value="" >Todos</option>
                                                    <option value='Sistema'>Sistema</option>
                                                    <option value='Chatbot'>Chatbot</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <div class="form-group">
                                                <label for="estadoReporte1">Estado</label>
                                                <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getSolicitudesAdmin">
                                                    <option value="">Todos</option>
                                                    <option value='Sin atender'>Sin atender</option>
                                                    <option value='Atendiendo'>Atendiendo</option>
                                                    <option value='Suspendida'>Suspendida</option>
                                                    <option value='Cancelada'>Cancelada</option>
                                                    <option value='Cerrada'>Cerrada</option>
                                                    <option value='Cerrada (En espera de aprobacion)'>Cerrada(En espera de aprobación)</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-lg-2">
                                            <div class="form-group">
                                                <label for="busquedaid1">Busqueda por ID</label>
                                                <input type="text"
                                                    class="form-control" name="busquedaid1" id="busquedaid1" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAdmin">
                                                <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <div class="form-group">
                                                <label for="busqueda1">Busqueda por Descripcion</label>
                                                <input type="text"
                                                    class="form-control" name="busqueda1" id="busqueda1" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAdmin">
                                                <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div class="alert alert-warning text-center" role="alert" v-if="Solicitudes.length===0">
                                            <strong>Sin resultados</strong>
                                        </div>
                                        <table class="table" v-if="Solicitudes.length>0">
                                            <thead>
                                                <tr>
                                                    <th><button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesAdmin">ID@{{orden=='ASC' ? '↓' : '↑'}}</button></th>
                                                    <th>Asignado</th>
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
                                                    <td><a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        
                                        
                                    </div>
                                    <div class="text-center font-weight-bold" v-if="Solicitudes.length>0">
                                        Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
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
                <div class="row">
                    <!-- Grafica Dona numero De Solicitudes por tipo -->
                    <div class="col-xl-12 col-lg-11" id="graficas">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Grafica de Tickets</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <button class="dropdown-item" @click="generarGraficaAdmin()">Recargar Grafica</button>
                                        <button class="dropdown-item" v-show="!ocultarGrafica" @click="ocultarGrafica = true">Ocultar</button>
                                        <button class="dropdown-item" v-show="ocultarGrafica" @click="ocultarGrafica = false">Mostrar</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body" v-show="!ocultarGrafica">
                                <div class="chart-pie pt-4 pb-3">
                                    <canvas id="SolicitudesAdminChart"></canvas>
                                </div>
                                <div class="mt-5 text-center small">
                                    <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
                                        <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i>@{{e.estatus}}-@{{e.total}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                
            </div>
            
            
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            

        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
            
            <div id="solicitudes_departamento">
                <div class="row">
                    <!-- Tarjeta Listado de solicitudes por Deparmento -->
                    <div class="col-xl-12 col-lg-11">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Tickets Departamento</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <button class="dropdown-item" @click="getSolicitudesDepartamento()">Recargar</button>
                                        <button class="dropdown-item" v-show="!ocultarListaSolicitudes" @click="ocultarListaSolicitudes = true">Ocultar</button>
                                        <button class="dropdown-item" v-show="ocultarListaSolicitudes" @click="ocultarListaSolicitudes = false">Mostrar</button>
                                    
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body" v-show="!ocultarListaSolicitudes">
                                <div class="mt-4 mb-2 border-bottom">
                                    <h1>Tickets Departamento</h1>
                                    <div class="form-row">
                                        <div class="form-group col-lg-1">
                                            <label for="">Paginado</label>
                                            <select class="form-control" name="" id="" v-model="numFiltro" @change="getSolicitudesDepartamento">
                                                <option value='10'>10</option>
                                                <option value='50'>50</option>
                                                <option value='100'>100</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label for="">Medio de Reporte</label>
                                            <select class="form-control" name="" id="" v-model="medioReporte" @change="getSolicitudesDepartamento">
                                                <option value="">Todos</option>
                                                <option value='Sistema'>Sistema</option>
                                                <option value='Chatbot'>Chatbot</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="">Estado</label>
                                            <select class="form-control" name="" id="" v-model="estadoReporte" @change="getSolicitudesDepartamento">
                                                <option value="">Todos</option>
                                                <option value='Sin atender'>Sin atender</option>
                                                <option value='Atendiendo'>Atendiendo</option>
                                                <option value='Suspendida'>Suspendida</option>
                                                <option value='Cancelada'>Cancelada</option>
                                                <option value='Cerrada'>Cerrada</option>
                                                <option value='Cerrada (En espera de aprobacion)'>Cerrada(En espera de aprobación)</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <div class="form-group">
                                                <label for="">Busqueda por ID</label>
                                                <input type="text"
                                                    class="form-control" name="busquedaid3" id="busquedaid3" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesDepartamento">
                                                <small id="helpId" class="form-text text-muted">Escribe el ID ticket</small>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <div class="form-group">
                                                <label for="">Busqueda por Descripcion</label>
                                                <input type="text"
                                                    class="form-control" name="busqueda3" id="busqueda3" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesDepartamento">
                                                <small id="helpId" class="form-text text-muted">Escribe el dato a buscar</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-4" v-if="Solicitudes.length>0">
                                            <div class="form-group">
                                                <label for="">Asignar personal en el listado</label>
                                                <button type="button" class="btn btn-primary btn-md btn-block" v-on:click="asignacion_multiple=!asignacion_multiple">@{{asignacion_multiple ? 'Desactivar Asignacion' : 'Activar Asignacion'}}</button>
                                            </div>
                                        </div>
                                        <div v-if="asignacion_multiple" class="form-group col-lg-8">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="">Asignar</label>
                                                    <select class="form-control" name="personas_dep" id="personas_dep" v-model="usuarioSeleccionado">
                                                    <option value="" disabled selected>Selecciona a un usuario de su departamento</option>
                                                    <option  v-for="u in listaUsuarios" :value="u.id_sgu" ><span>@{{u.nombre}}</span></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="button" name="" id="" class="btn btn-primary btn-md btn-block" @click="asignarSolicitudes()">Asignar</button>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                    </div>
                                    <div class="table-responsive">
                                        <div class="alert alert-warning text-center" role="alert" v-if="Solicitudes.length===0">
                                            <strong>Sin resultados</strong>
                                        </div>
                                        <table class="table" v-if="Solicitudes.length>0">
                                            <thead>
                                                <tr>
                                                    <th v-if="asignacion_multiple">Asignar</th>
                                                    <th><button type="button" name="ordenID3" id="ordenID3" class="btn btn-sm btn-outline-primary" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesDepartamento">ID@{{orden=='ASC' ? '↓' : '↑'}}</button></th>
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
                                                    <td v-if="asignacion_multiple" style="text-align:center;"><div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-bind:value="s.id_solicitud" v-model="tickets_seleccionados">
                                                    </div></td>
                                                    <td>@{{s.id_solicitud}}</td>
                                                    <td>
                                                        <label v-for="u in s.usuario_many">@{{u.correo}}</label>
                                                        <label v-if="s.usuario_many.length == 0">Sin asignar</label>
                                                    </td>
                                                    <td>@{{s.descripcion}}</td>
                                                    <td>@{{s.fecha_creacion}}</td>
                                                    <td>@{{s.estatus}}</td>
                                                    <td>@{{s.medio_reporte}}</td>
                                                    <td><a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud">Responder</a></td>
                                                </tr>
                                            
                                            </tbody>
                                        </table>
                                        
                                        
                                    </div>
                                    <div class="text-center font-weight-bold" v-if="Solicitudes.length>0">
                                        Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
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
                    <div class="col-xl-12 col-lg-11" id="graficas">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Grafica de Tickets Departamento</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <button class="dropdown-item" @click="generarGraficaDepartamento()">Recargar Grafica</button>
                                        <button class="dropdown-item" v-show="!ocultarGrafica" @click="ocultarGrafica = true">Ocultar</button>
                                        <button class="dropdown-item" v-show="ocultarGrafica" @click="ocultarGrafica = false">Mostrar</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body" v-show="!ocultarGrafica">
                                <div class="chart-pie pt-4 pb-3">
                                    <canvas id="SolicitudesDepartamentoChart"></canvas>
                                </div>
                                <div class="mt-5 text-center small">
                                    <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
                                        <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i>@{{e.estatus}}-@{{e.total}}
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
            
        </div>
        <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile3-tab">

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
<script src="{{asset('assets/vue/dashboard.js')}}"></script>
<script src="{{asset('assets/vue/solicitudes_asignadas.js')}}"></script>
<script src="{{asset('assets/vue/solicitudes_departamento.js')}}"></script>
<script src="{{asset('assets/vue/mis_solicitudes.js')}}"></script>
@if(Session::get('rol')=='SUPER')
    <script src="{{asset('assets/vue/solicitudes.js')}}"></script>
@endif
@endsection
