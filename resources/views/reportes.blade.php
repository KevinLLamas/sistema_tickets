@extends('app')
@section('content')


<!-- Begin Page Content -->
<div  class="container-fluid"  >
    <!-- Content Row -->
    <div id="dashboard">
        <input type="hidden" value="{{Session::get('rol')}}" id="rol" name="rol">
        
    </div>
    
    <div id="reportes">
      <input type="hidden" value="{{Session::get('rol')}}" id="rol" name="rol">
      <input type="hidden" value="{{Session::get('id_departamento')}}" id="departamento" name="departamento">
      <div class="row" v-if="departamentoSeleccionado != ''">
          <div class="col-xl-3 col-md-4 mb-4">
            <div class="card border-left-info shadow bg-white py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Atendiendo</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numAtendiendo}}</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sin Atender</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numSinAtender}}</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cerrados</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numCerradas}}</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-check fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        <div class="col-xl-3 col-md-4 mb-4">
          <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">En Espera</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numEspera}}</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-question fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row" v-if="rolUsuario!='SUPER'">
        <!-- Tarjeta Listado de solicitudes -->
        <div class="col-xl-12 col-lg-11">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Reporte de Solicitudes Creadas vs Solicitudes Cerrados en mi Departamento </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <button class="dropdown-item" @click="recargarGraficaComparacion()">Recargar</button>
                            
                        
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="mt-1 mb-2 border-bottom">
                        <div class="form-row">
                            <div class="form-group col-lg-12">
                                <div class="form-group">
                                  <label for="">Periodo de tiempo</label>
                                  <select class="form-control" name="periodo" id="periodo" v-model="rangoTiempo" @change="recargarGraficaComparacion">
                                    
                                    <option value="INTERVAL 1 DAY">Hoy</option>
                                    <option value="INTERVAL 7 DAY">7 Dias</option>
                                    <option value="INTERVAL 1 MONTH">1 Mes</option>
                                    <option value="INTERVAL 3 MONTH">3 Meses</option>
                                  </select>
                                </div>
                            </div>
                            <div class="chart-area">
                                <canvas id="ComparacionSolicitudesChart"></canvas>
                                
                            </div>
                            
                            
                            
                        </div>
                        <div class="mt-5 text-center small">
                      
                          <span class="mr-2">
                            <i  class="fas fa-circle" style="color:#E9004C"></i> - Sin Atender
                            <i  class="fas fa-circle" style="color:#28a745"></i> - Cerradas
                            <i  class="fas fa-circle" style="color:#CDCDCD"></i> - Cerradas(En Espera)
                            <i  class="fas fa-circle" style="color:#007bff"></i> - Atendiendo
                          </span>
                        </div>
                        <!---->
                    
                    </div>
                </div>
            </div>
        </div>
          
      </div>  
      <div class="row" v-if="rolUsuario=='SUPER'">
        <!-- Tarjeta Listado de solicitudes -->
        <div class="col-xl-12 col-lg-11">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Reporte de Solicitudes Creadas vs Solicitudes Cerradas por Departamento</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <button class="dropdown-item" @click="recargarGraficaComparacionDep()">Recargar</button>
                            
                        
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class=" mt-1 mb-2 border-bottom">
                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <div class="form-group">
                                  <label for="">Departamento</label>
                                  <select class="form-control" name="listaDeps" id="listaDeps" v-if="listaDepartamentos.length > 0" v-model="departamentoSeleccionado" @change="recargarTodoDepartamento()">
                                    
                                    <option  v-for="d in listaDepartamentos" :value="d.id" ><span>@{{d.nombre}}</span></option>
                                    
                                  </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="form-group">
                                  <label for="">Periodo de tiempo</label>
                                  <select class="form-control" name="periodo" id="periodo" v-model="rangoTiempo" @change="recargarGraficaComparacionDep()">
                                    
                                    <option value="INTERVAL 1 DAY">Hoy</option>
                                    <option value="INTERVAL 7 DAY">7 Dias</option>
                                    <option value="INTERVAL 1 MONTH">1 Mes</option>
                                    <option value="INTERVAL 3 MONTH">3 Meses</option>
                                  </select>
                                </div>
                            </div>
                            <div class="chart-area">
                                <canvas id="ComparacionSolicitudesChartDep"></canvas>
                                
                            </div>
                            
                            
                            
                        </div>
                        <div class="mt-5 text-center small">
                      
                          <span class="mr-2">
                            <i  class="fas fa-circle" style="color:#E9004C"></i> - Sin Atender
                            <i  class="fas fa-circle" style="color:#28a745"></i> - Cerradas
                            <i  class="fas fa-circle" style="color:#CDCDCD"></i> - Cerradas(En Espera)
                            <i  class="fas fa-circle" style="color:#007bff"></i> - Atendiendo
                          </span>
                        </div>
                        <!---->
                    
                    </div>
                </div>
            </div>
        </div>
          
      </div>
      
      <div class="row">
        <!-- Listado de personas en el departamento y sus numero de solicitudes -->
        <div class="col-xl-8 col-lg-7" id="graficas">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Listado de personas en el departamento</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <button class="dropdown-item" @click="recargarListadoUsuariosTodos()">Recargar Tabla</button>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body" >
                  <div class=" mt-4 mb-2 border-bottom">
                    
                    <table class="table table-responsive" v-if="EstatusTodos && EstatusTodos.length > 0">
                      <colgroup>
                        <col span="1" ></col>
                        <col span="1" class="table-danger"></col>
                        <col span="1" class="table-primary"></col>
                        <col span="1" class="table-success"></col>
                        <col span="1" class="table-secondary"></col>
                        <col span="1" class="table-active"></col>
                      </colgroup>
                      <thead style="color:whitesmoke">
                        <tr>
                          <th style="color: black">Nombre del Usuario</th>
                          <th class="bg-primary">Sin Atender</th>
                          <th class="bg-info">Atendiendo</th>
                          <th class="bg-success">Cerrada</th>
                          <th class="bg-secondary">Cerrada(En espera)</th>
                          <th class="bg-dark">Suspendida</th>

                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="u in EstatusTodos">
                          <td>@{{u.nombre}}</td>
                          <td>@{{u.Sin_atender!=undefined ? u.Sin_atender : "N/A"}}</td>
                          <td>@{{u.Atendiendo!=undefined ? u.Atendiendo : "N/A"}}</td>
                          <td>@{{u.Cerrada!=undefined ? u.Cerrada : "N/A"}}</td>
                          <td>@{{u.Cerrada_En_espera_de_aprobación!=undefined ? u.Cerrada_En_espera_de_aprobación : "N/A"}}</td>
                          <td>@{{u.Suspendida!=undefined ? u.Suspendida : "N/A"}}</td>
                        </tr>
                        
                      </tbody>
                    </table>
                  </div>
                  
                  
                </div>
            </div>
        </div>
        <!-- Grafica Dona numero De Solicitudes por Usuario -->
        <div class="col-xl-4 col-lg-5" id="graficas">
          <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Grafica de Solicitudes por Usuario</h6>
                  <div class="dropdown no-arrow">
                      <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                          <div class="dropdown-header">Opciones:</div>
                          <button class="dropdown-item" @click="recargarGraficaByStatus()">Recargar Grafica</button>
                      </div>
                  </div>
              </div>
              <!-- Card Body -->
              <div class="card-body" >
                <div class="form-group">
                  <label for="listaUsuarios">Usuarios en el departamento</label>
                  <select class="form-control" name="listaUsuarios" id="listaUsuarios" v-if="listaUsuarios.length > 0" v-model="usuarioSeleccionado" @change="recargarGraficaByStatus()">
                    <option value="" disabled selected>Selecciona a un usuario</option>
                    <option  v-for="u in listaUsuarios" :value="u.id_sgu" ><span>@{{u.nombre}}</span></option>
                    
                  </select>
                  
                </div>
                <div class="chart-pie pt-4 pb-3">
                  <canvas id="solicitudesUsuarioChart"></canvas>
                  <label v-if="usuarioSeleccionado=='' || Estatus.length == 0">Nada que mostrar</label>
                </div>
                
                <div class="mt-5 text-center small">
                  
                  <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
                    <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i> @{{e.estatus}}-(@{{e.total}})
                  </span>
                </div>
              </div>
          </div>
        </div>
      </div>
      
      <div class="row" >
        <!-- Grafica Dona numero De Solicitudes por Subcategoria -->
        <div class="col-xl-12 col-lg-11" id="graficas">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafica de Solicitudes por Subcategoria</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <button class="dropdown-item" @click="recargarGraficaByStatusSubc()">Recargar Grafica</button>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body" >
                  <div class="form-group">
                    <label for="listaUsuarios">Subcategorias en el departamento</label>
                    <select class="form-control" name="listaSubcategorias" id="listaSubcategorias"  v-if="listaSubcategorias.length > 0"  v-model="subcategoriaSeleccionada"  @change="recargarGraficaByStatusSubc()">
                      
                      <option v-for="(s,index) in listaSubcategorias" :value="s.id" :selected="index == 0" ><span>@{{s.nombre}}</span></option>
                      
                    </select>
                    
                  </div>
                  <div class="chart-pie pt-4 pb-3">
                    <canvas id="solicitudesSubcategoriaChart"></canvas>
                    <label v-if="usuarioSeleccionado=='' || EstatusSubc.length == 0">Nada que mostrar</label>
                  </div>
                  
                  <div class="mt-5 text-center small">
                    
                    <span class="mr-2" v-if="EstatusSubc.length > 0" v-for="(e,index) in EstatusSubc">
                      <i :id="index" class="fas fa-circle" :style="'color:'+asignarColorHex(e.estatus)" ></i> @{{e.estatus}}-@{{e.total}}
                    </span>
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
{{-- <script src="{{asset('assets/vue/dashboard.js')}}"></script> --}}
<script src="{{asset('assets/vue/reportes.js')}}"></script>

@endsection
