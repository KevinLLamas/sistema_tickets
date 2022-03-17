@extends('app')
@section('content')
@if(Session::get('rol')== 'Administrador')
<div class="col-lg-8 d-flex flex-column">
    <div class="row flex-grow">
    <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
        <div class="card card-rounded rounded shadow">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
            <div>
                <h4 class="card-title card-title-dash">Performance Line Chart</h4>
                <h5 class="card-subtitle card-subtitle-dash">Lorem Ipsum is simply dummy text of the printing</h5>
            </div>
            <div id="performance-line-legend"></div>
            </div>
            <div class="chartjs-wrapper mt-5">
            <canvas id="performaneLine"></canvas>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="col-4 grid-margin stretch-card">
    <div class="card card-rounded rounded shadow">
        <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title card-title-dash">Type By Amount</h4>
            </div>
            <canvas class="my-auto" id="doughnutChart" height="100"></canvas>
            <div id="doughnut-chart-legend" class="mt-1 text-center"></div>
            </div>
        </div>
        </div>
    </div>
</div>
@endif
<!-- Content Row -->
<div class="col-md-12 grid-margin stretch-card">
    <div class="card rounded shadow">
        <div class="card-body">
            <h4 class="card-title">Listado de solicitudes</h4>
            <!--p class="card-description">Horizontal bootstrap tab</p-->
            <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active fw-bold" id="contact-tab" data-bs-toggle="tab" href="#contact-1" role="tab" aria-controls="contact-1" aria-selected="false">Solicitudes creadas</a>
            </li>
            @if(Session::get('rol')=='Administrador' || Session::get('rol')=='Directivo')
            <li class="nav-item" v-if="rol == 'Administrador'">
                <a class="nav-link fw-bold" id="home-tab" data-bs-toggle="tab" href="#home-1" role="tab" aria-controls="home-1" aria-selected="true">Solicitudes</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link fw-bold" id="profile-tab" data-bs-toggle="tab" href="#profile-1" role="tab" aria-controls="profile-1" aria-selected="false">Solicitudes asignadas</a>
            </li>
            @if(Session::get('rol')=='Administrador' || Session::get('rol')=='Jefe')
            <li class="nav-item">
                <a class="nav-link fw-bold" id="departamento-tab" data-bs-toggle="tab" href="#departamento-1" role="tab" aria-controls="departamento-1" aria-selected="false">Solicitudes departamento</a>
            </li>
            @endif
            </ul>
            <div class="tab-content" >
                <div class="tab-pane fade" id="home-1" role="tabpanel" aria-labelledby="home-tab" >
                    {{-- Solicitudes --}}
                    <div id="solicitudes">
                        
                        <div class="row" >
                            {{--<div class="col-lg-1">
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
                            </div>--}}
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="busquedaid1">Busqueda por ID</label>
                                    <input type="text"
                                        class="form-control" name="busquedaid1" id="busquedaid1" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAdmin">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="busqueda1">Busqueda por Descripcion</label>
                                    <input type="text"
                                        class="form-control" name="busqueda1" id="busqueda1" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAdmin">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" >
                            <div class="alert alert-warning text-center" role="alert" v-if="Solicitudes.length===0">
                                <strong>Sin resultados</strong>
                            </div>
                            <table class="table text-center" v-if="Solicitudes.length>0">
                                <thead>
                                    <tr>
                                        <th>
                                            Creador
                                        </th>
                                        <th>
                                            <button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary borderless" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesAdmin"><b>Id</b>@{{orden=='ASC' ? '↓' : '↑'}}</button>
                                        </th>
                                        <th>
                                            Subcategoría
                                        </th>
                                        <th>
                                            Fecha
                                        </th>
                                        <th>
                                            <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getSolicitudesAdmin" style="color:black;">
                                                <option value="">Estados</option>
                                                <option value='Sin atender'>Sin atender</option>
                                                <option value='Atendiendo'>Atendiendo</option>
                                                <option value='Suspendida'>Suspendida</option>
                                                <option value='Cancelada'>Cancelada</option>
                                                <option value='Cerrada'>Cerrada</option>
                                            </select>
                                        </th>
                                        <th> 
                                            <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getSolicitudesAdmin"  style="color:black;">
                                                <option value="" >Medio</option>
                                                <option value='Sistema'>Sistema</option>
                                                <option value='Chatbot'>Chatbot</option>
                                            </select>
                                        </th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="s in Solicitudes" >
                                        <td >
                                            <div class="d-flex text-start">
                                                <img class="img-sm rounded-10" :src="'/assets/'+s.usuario.path_foto" alt="profile" v-if="s.usuario.path_foto">
                                                <img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile" v-if="!s.usuario.path_foto">
                                                <div class="wrapper ">
                                                    <p class="ms-1 mb-1 fw-bold" v-if="s.usuario">@{{s.usuario.nombre}}</p>
                                                    <small class="text-muted ms-1" v-if="s.usuario">@{{s.usuario.rol}}</small>
                                                  </div>
                                              </div>
                                            {{--<img class="img-xs rounded-circle" src="{{asset('/assets/chatbot_images/profile/'.Session::get('path_foto'))}}" alt="Profile image"> </a>--}}
                                        </td>
                                        <td class="text-secondary">#@{{s.id_solicitud}}</td>
                                        <td class="text-secondary">
                                            <div v-if="s.subcategoria != null">
                                                @{{s.subcategoria.nombre}}
                                            </div>
                                            <div v-if="s.subcategoria == null">
                                                Desconocida
                                            </div>
                                        </td>
                                        <td class="text-secondary">@{{(s.fecha_creacion.split(' ')[0])}} </td>
                                        <td class="text-secondary">
                                            <div class="progress progress-md" v-if="s.estatus == 'Sin atender'">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-md" v-if="s.estatus == 'Atendiendo'">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-md" v-if="s.estatus == 'Cerrada'">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-md" v-if="s.estatus == 'Cancelada'">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-secondary">
                                                @{{s.estatus}}</td>
                                            </div>
                                        <td class="text-secondary">
                                            <i class="fas fa-robot color-grey" v-if="s.medio_reporte == 'Chatbot'"></i>
                                            <i class="fas fa-laptop color-grey" v-if="s.medio_reporte == 'Sistema'"></i>
                                            @{{s.medio_reporte}}</td>
                                        <td>
                                            {{--<a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud"><i class="fa fa-eye color-white"></i></a>--}}
                                            <a type="button"  name="ordenID1" id="ordenID1" :href="'/seguimiento/'+s.id_solicitud" class="btn btn-sm btn-outline-primary eye borderless" ><i class="fa fa-eye "style=""></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="text-center font-weight-bold" v-if="Solicitudes.length>0">
                                Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
                            </div>
                            <div class="form-group col-md-1 me-1 float-end">
                                <label for="paginado1">Paginado</label>
                                <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getSolicitudesAdmin">
                                    <option value='10'>10</option>
                                    <option value='50'>50</option>
                                    <option value='100'>100</option>
                                </select>
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
                </div>
                <div class="tab-pane fade" id="profile-1" role="tabpanel" aria-labelledby="profile-tab">
                    {{-- Solicitudes asignadas --}}
                    <div id="mis_solicitudes_asignadas">
                        <div class="row">
                            <!-- Tarjeta Listado de solicitudes asignadas-->
                            <div class="col-xl-12 col-lg-11">
                                <div class="mb-4">
                                    <div class="" v-show="!ocultarTabla">
                                        <div class="mb-2 ">
                                            <div class="row">
                                                
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="">Busqueda por ID</label>
                                                        <input type="text"
                                                            class="form-control" name="busquedaid2" id="busquedaid2" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesAsignadas">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="">Busqueda por Descripcion</label>
                                                        <input type="text"
                                                            class="form-control" name="busqueda2" id="busqueda2" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesAsignadas">
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
                                                            <th>
                                                                Creador
                                                            </th>
                                                            <th>
                                                                <button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary borderless" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesAsignadas"><b>Id</b>@{{orden=='ASC' ? '↓' : '↑'}}</button>
                                                            </th>
                                                            <th>
                                                                Fecha
                                                            </th>
                                                            <th>
                                                                <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getSolicitudesAsignadas" style="color:black;">
                                                                    <option value="">Estados</option>
                                                                    <option value='Sin atender'>Sin atender</option>
                                                                    <option value='Atendiendo'>Atendiendo</option>
                                                                    <option value='Suspendida'>Suspendida</option>
                                                                    <option value='Cancelada'>Cancelada</option>
                                                                    <option value='Cerrada'>Cerrada</option>
                                                                </select>
                                                            </th>
                                                            <th> 
                                                                <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getSolicitudesAsignadas"  style="color:black;">
                                                                    <option value="" >Medio</option>
                                                                    <option value='Sistema'>Sistema</option>
                                                                    <option value='Chatbot'>Chatbot</option>
                                                                </select>
                                                            </th>
                                                            <th>Ver</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody >
                                                        <tr v-for="s in MisSolicitudes">
                                                            <td >
                                                                <div class="d-flex text-start">
                                                                    {{--<img class="img-sm rounded-10" :src="'/assets/'+s.usuario.path_foto" alt="profile" v-if="s.usuario.path_foto">--}}
                                                                    <img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile" >
                                                                    <div class="wrapper ">
                                                                        <p class="ms-1 mb-1 fw-bold" >Nombre</p>
                                                                        <small class="text-muted ms-1" >Rol</small>
                                                                      </div>
                                                                  </div>
                                                            </td>
                                                            <td class="text-secondary">#@{{s.id_solicitud}}</td>
                                                            <td class="text-secondary">
                                                                <div v-if="s.subcategoria != null">
                                                                    @{{s.subcategoria.nombre}}
                                                                </div>
                                                                <div v-if="s.subcategoria == null">
                                                                    Desconocida
                                                                </div>
                                                            </td>
                                                            <td class="text-secondary">@{{(s.fecha_creacion.split(' ')[0])}} </td>
                                                            <td class="text-secondary">
                                                                <div class="progress progress-md" v-if="s.estatus == 'Sin atender'">
                                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Atendiendo'">
                                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Cerrada'">
                                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Cancelada'">
                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="text-secondary">
                                                                    @{{s.estatus}}</td>
                                                                </div>
                                                            <td class="text-secondary">
                                                                <i class="fas fa-robot color-grey" v-if="s.medio_reporte == 'Chatbot'"></i>
                                                                <i class="fas fa-laptop color-grey" v-if="s.medio_reporte == 'Sistema'"></i>
                                                                @{{s.medio_reporte}}</td>
                                                            <td>
                                                                {{--<a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud"><i class="fa fa-eye color-white"></i></a>--}}
                                                                <a type="button"  name="ordenID1" id="ordenID1" :href="'/seguimiento/'+s.id_solicitud" class="btn btn-sm btn-outline-primary eye borderless" ><i class="fa fa-eye "style=""></i></a>
                                                            </td>
                                                        </tr>
                                                    
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-center font-weight-bold" v-if="MisSolicitudes.length>0">
                                                Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
                                            </div>
                                            <div class="form-group col-md-1 me-1 float-end">
                                                <label for="paginado1">Paginado</label>
                                                <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getSolicitudesAsignadas">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
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
                    </div>
                </div>
                <div class="tab-pane fade  show active" id="contact-1" role="tabpanel" aria-labelledby="contact-tab">
                    {{-- Mis solicitudes --}}
                    <div  class="container-fluid">
                        <div class="row"  id="mis_solicitudes">
                            <div class="col-lg-12">
                                <div class=" mb-4">
                                    <div class="" v-show="!ocultarTabla">
                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="">Busqueda por ID</label>
                                                        <input type="text"
                                                            class="form-control" name="busquedaid4" id="busquedaid4" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getMisSolicitudes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="">Busqueda por Descripcion</label>
                                                        <input type="text"
                                                            class="form-control" name="busqueda4" id="busqueda4" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getMisSolicitudes">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <div class="alert alert-warning text-center" role="alert" v-if="MisSoli.length===0">
                                                    <strong>Sin resultados</strong>
                                                </div>
                                                <table class="table" v-if="MisSoli.length>0">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                Creador
                                                            </th>
                                                            <th>
                                                                <button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary borderless" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getMisSolicitudes"><b>Id</b>@{{orden=='ASC' ? '↓' : '↑'}}</button>
                                                            </th>
                                                            <th>
                                                                Subcategoría
                                                            </th>
                                                            <th>
                                                                Fecha
                                                            </th>
                                                            <th>
                                                                <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getMisSolicitudes" style="color:black;">
                                                                    <option value="">Estados</option>
                                                                    <option value='Sin atender'>Sin atender</option>
                                                                    <option value='Atendiendo'>Atendiendo</option>
                                                                    <option value='Suspendida'>Suspendida</option>
                                                                    <option value='Cancelada'>Cancelada</option>
                                                                    <option value='Cerrada'>Cerrada</option>
                                                                </select>
                                                            </th>
                                                            <th> 
                                                                <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getMisSolicitudes"  style="color:black;">
                                                                    <option value="" >Medio</option>
                                                                    <option value='Sistema'>Sistema</option>
                                                                    <option value='Chatbot'>Chatbot</option>
                                                                </select>
                                                            </th>
                                                            <th>Ver</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody >
                                                        <tr v-for="s in MisSoli">
                                                            <td >
                                                                <div class="d-flex text-start">
                                                                    @if(Session::has('path_foto'))
                                                                        <img class="img-sm rounded-10" src="/assets/{{Session::get('path_foto')}}" alt="profile">
                                                                    @else
                                                                        <img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile">
                                                                    @endif
                                                                    <div class="wrapper ">
                                                                        <p class="ms-1 mb-1 fw-bold" >{{Session::get('nombre')}}</p>
                                                                        <small class="text-muted ms-1">{{Session::get('rol')}}</small>
                                                                      </div>
                                                                  </div>
                                                            </td>
                                                            <td class="text-secondary">#@{{s.id_solicitud}}</td>
                                                            <td class="text-secondary">
                                                                <div v-if="s.subcategoria != null">
                                                                    @{{s.subcategoria.nombre}}
                                                                </div>
                                                                <div v-if="s.subcategoria == null">
                                                                    Desconocida
                                                                </div>
                                                            </td>
                                                            <td class="text-secondary">@{{(s.fecha_creacion.split(' ')[0])}} </td>
                                                            <td class="text-secondary">
                                                                <div class="progress progress-md" v-if="s.estatus == 'Sin atender'">
                                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Atendiendo'">
                                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Cerrada'">
                                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="progress progress-md" v-if="s.estatus == 'Cancelada'">
                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="text-secondary">
                                                                    @{{s.estatus}}</td>
                                                                </div>
                                                            <td class="text-secondary">
                                                                <i class="fas fa-robot color-grey" v-if="s.medio_reporte == 'Chatbot'"></i>
                                                                <i class="fas fa-laptop color-grey" v-if="s.medio_reporte == 'Sistema'"></i>
                                                                @{{s.medio_reporte}}</td>
                                                            <td>
                                                                {{--<a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud"><i class="fa fa-eye color-white"></i></a>--}}
                                                                <a type="button"  name="ordenID1" id="ordenID1" :href="'/seguimiento/'+s.id_solicitud" class="btn btn-sm btn-outline-primary eye borderless" ><i class="fa fa-eye "style=""></i></a>
                                                            </td>
                                                        </tr>
                                                    
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-center font-weight-bold" v-if="MisSoli.length>0">
                                                Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
                                            </div>
                                            <div class="form-group col-md-1 me-1 float-end">
                                                <label for="paginado1">Paginado</label>
                                                <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getMisSolicitudes">
                                                    <option value='10'>10</option>
                                                    <option value='50'>50</option>
                                                    <option value='100'>100</option>
                                                </select>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="departamento-1" role="tabpanel" aria-labelledby="departamento-tab">
                    <div class="row" id="solicitudes_departamento">
                        <!-- Tarjeta Listado de solicitudes por Deparmento -->
                        <div class="col-xl-12 col-lg-12">
                            <div class="mb-4">
                                <!-- Card Body -->
                                <div class="" v-show="!ocultarListaSolicitudes">
                                    <div class="mb-2">
                                        <div class="row">
                                            
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="">Busqueda por ID</label>
                                                    <input type="text"
                                                        class="form-control" name="busquedaid3" id="busquedaid3" aria-describedby="helpId" placeholder="ID" v-model="busquedaid" @input="getSolicitudesDepartamento">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Busqueda por Descripcion</label>
                                                    <input type="text"
                                                        class="form-control" name="busqueda3" id="busqueda3" aria-describedby="helpId" placeholder="Escribe aqui la busqueda" v-model="busqueda" @input="getSolicitudesDepartamento">
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
                                                        <th>
                                                            Creador
                                                        </th>
                                                        <th>
                                                            <button type="button" name="ordenID1" id="ordenID1" class="btn btn-sm btn-outline-primary borderless" v-on:click="orden=='ASC' ? orden='DESC' : orden='ASC'" @click="getSolicitudesDepartamento"><b>Id</b>@{{orden=='ASC' ? '↓' : '↑'}}</button>
                                                        </th>
                                                        <th>
                                                            Subcategoría
                                                        </th>
                                                        <th>
                                                            Fecha
                                                        </th>
                                                        <th>
                                                            <select class="form-control" name="estadoReporte1" id="estadoReporte1" v-model="estadoReporte" @change="getSolicitudesDepartamento" style="color:black;">
                                                                <option value="">Estados</option>
                                                                <option value='Sin atender'>Sin atender</option>
                                                                <option value='Atendiendo'>Atendiendo</option>
                                                                <option value='Suspendida'>Suspendida</option>
                                                                <option value='Cancelada'>Cancelada</option>
                                                                <option value='Cerrada'>Cerrada</option>
                                                            </select>
                                                        </th>
                                                        <th> 
                                                            <select class="form-control" name="medioReporte1" id="medioReporte1" v-model="medioReporte" @change="getSolicitudesDepartamento"  style="color:black;">
                                                                <option value="" >Medio</option>
                                                                <option value='Sistema'>Sistema</option>
                                                                <option value='Chatbot'>Chatbot</option>
                                                            </select>
                                                        </th>
                                                        <th>Ver</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="s in Solicitudes">
                                                        <td >
                                                            <div class="d-flex text-start">
                                                                {{--<img class="img-sm rounded-10" :src="'/assets/'+s.usuario.path_foto" alt="profile" v-if="s.usuario.path_foto">--}}
                                                                <img class="img-sm rounded-10" src="/assets/images/user.jpg" alt="profile" >
                                                                <div class="wrapper ">
                                                                    <p class="ms-1 mb-1 fw-bold" >Nombre</p>
                                                                    <small class="text-muted ms-1" >Rol</small>
                                                                  </div>
                                                              </div>
                                                        </td>
                                                        <td class="text-secondary">#@{{s.id_solicitud}}</td>
                                                        <td class="text-secondary">
                                                            <div v-if="s.subcategoria != null">
                                                                @{{s.subcategoria.nombre}}
                                                            </div>
                                                            <div v-if="s.subcategoria == null">
                                                                Desconocida
                                                            </div>
                                                        </td>
                                                        <td class="text-secondary">@{{(s.fecha_creacion.split(' ')[0])}} </td>
                                                        <td class="text-secondary">
                                                            <div class="progress progress-md" v-if="s.estatus == 'Sin atender'">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="progress progress-md" v-if="s.estatus == 'Atendiendo'">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="progress progress-md" v-if="s.estatus == 'Cerrada'">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="progress progress-md" v-if="s.estatus == 'Cancelada'">
                                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="text-secondary">
                                                                @{{s.estatus}}</td>
                                                            </div>
                                                        <td class="text-secondary">
                                                            <i class="fas fa-robot color-grey" v-if="s.medio_reporte == 'Chatbot'"></i>
                                                            <i class="fas fa-laptop color-grey" v-if="s.medio_reporte == 'Sistema'"></i>
                                                            @{{s.medio_reporte}}</td>
                                                        <td>
                                                            {{--<a type="button" class="btn btn-sm btn-primary" :href="'/seguimiento/'+s.id_solicitud"><i class="fa fa-eye color-white"></i></a>--}}
                                                            <a type="button"  name="ordenID1" id="ordenID1" :href="'/seguimiento/'+s.id_solicitud" class="btn btn-sm btn-outline-primary eye borderless" ><i class="fa fa-eye "style=""></i></a>
                                                        </td>
                                                    </tr>
                                                
                                                </tbody>
                                            </table>
                                            
                                            
                                        </div>
                                        <div class="text-center font-weight-bold" v-if="Solicitudes.length>0">
                                            Mostrando @{{(pagination.per_page < pagination.total) ? (pagination.per_page) : (pagination.total)}} de @{{pagination.total}}
                                        </div>
                                        <!--Paginación-->
                                        <div class="form-group col-md-1 me-1 float-end">
                                            <label for="paginado1">Paginado</label>
                                            <select class="form-control" name="paginado1" id="paginado1" v-model="numFiltro" @change="getSolicitudesDepartamento">
                                                <option value='10'>10</option>
                                                <option value='50'>50</option>
                                                <option value='100'>100</option>
                                            </select>
                                        </div>
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
                </div>
            </div>{{--cierra contact tab--}}  
        </div>
    </div> 
</div>
<!-- /.container-fluid -->


<!-- Chart.js -->
<script src="{{asset('assets/vendor/chart.js/Chart.min.js')}}"></script>
<!-- Scripts VUE -->
<script src="{{asset('assets/vue/solicitudes_asignadas.js')}}"></script>
<script src="{{asset('assets/vue/solicitudes_departamento.js')}}"></script>
<script src="{{asset('assets/vue/mis_solicitudes.js')}}"></script>
@if(Session::get('rol')=='Administrador' || Session::get('rol')=='Directivo')
    <script src="{{asset('assets/vue/solicitudes.js')}}"></script>
@endif
@endsection
