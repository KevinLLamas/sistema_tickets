@extends('app')
@section('content')


<!-- Begin Page Content -->
<div  class="container" >
    <!-- Content Row -->
    <div id="dashboard">
        <input type="hidden" value="{{Session::get('rol')}}" id="rol" name="rol">
        
    </div>
    
        <div id="reportes" class="container">
            <div class="row mt-2">
                  <div class="mx-auto mb-3">
                    <div class="border-left-primary shadow rounded bg-white">
                      <div class="card-body">
                        <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Atendiendo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numAtendiendo}}</div>
                          </div>
                          <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="mx-auto mb-3">
                    <div class="border-left-secondary shadow rounded bg-white">
                      <div class="card-body">
                        <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Sin Atender</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">@{{numSinAtender}}</div>
                          </div>
                          <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="mx-auto mb-3">
                    <div class="border-left-success shadow rounded bg-white">
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
                <div class="mx-auto mb-3">
                    <div class="border-left-info shadow rounded bg-white">
                      <div class="card-body">
                        <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cerrados</div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800">@{{porcentajeCerrados}}%</div>
                          </div>
                          <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              <div class="mx-auto mb-3">
                <div class="border-left-warning shadow rounded bg-white">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Espera</div>
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
            <div class="row">
              <!-- Tarjeta Listado de solicitudes -->
              <div class="col-xl-12 col-lg-5">
                  <div class="card shadow mb-4">
                      <!-- Card Header - Dropdown -->
                      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                          <h6 class="m-0 font-weight-bold text-primary">Reporte de Tickets Creados vs Tickets Cerrados : {{Session::get('rol')}}</h6>
                          <div class="dropdown no-arrow">
                              <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                  <div class="dropdown-header">Opciones:</div>
                                  <button class="dropdown-item" @click="generar_Grafica_ByTime();generar_Grafica_Comparacion();">Recargar</button>
                                  
                              
                              </div>
                          </div>
                      </div>
                      <!-- Card Body -->
                      <div class="card-body">
                          <div class=" mt-1 mb-2  container-fluid border-bottom">
                              <div class="form-row">
                                  <div class="form-group col-lg-12">
                                      <div class="form-group">
                                        <label for="">Periodo de tiempo</label>
                                        <select class="form-control" name="periodo" id="periodo" v-model="rangoTiempo" @change="generar_Grafica_ByTime();generar_Grafica_Comparacion();">
                                          
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
                                  <i  class="fas fa-circle" style="color:#E9004C"></i> - Creadas
                                  <i  class="fas fa-circle" style="color:#28a745"></i> - Cerradas
                                </span>
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
                            <h6 class="m-0 font-weight-bold text-primary">Grafica de Tickets por Usuario</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Opciones:</div>
                                    <button class="dropdown-item" @click="generar_Grafica_ByStatus();generar_Grafica_Estados();">Recargar Grafica</button>
                                </div>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body" >
                          <div class="form-group">
                            <label for="listaUsuarios">Usuarios en mi departamento</label>
                            <select class="form-control" name="listaUsuarios" id="listaUsuarios" v-if="listaUsuarios.length > 0" v-model="usuarioSeleccionado" @change="generar_Grafica_ByStatus();generar_Grafica_Estados();">
                              <option value="" disabled selected>Selecciona a un usuario</option>
                              <option  v-for="u in listaUsuarios" :value="u.id_sgu" >@{{u.nombre}}</span></option>
                              
                            </select>
                            
                          </div>
                          <div class="chart-pie pt-4 pb-3">
                            <canvas id="solicitudesUsuarioChart"></canvas>
                            <label v-if="usuarioSeleccionado=='' || Estatus.length == 0">Nada que mostrar</label>
                          </div>
                          
                          <div class="mt-5 text-center small">
                            
                            <span class="mr-2" v-if="Estatus.length > 0" v-for="(e,index) in Estatus">
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
<script src="{{asset('assets/vue/dashboard.js')}}"></script>
<script src="{{asset('assets/vue/reportes.js')}}"></script>

@endsection
