@extends('app')
@section('content')


<!-- Begin Page Content -->
<div  class="container" >
    <!-- Content Row -->
    <div id="dashboard">
        <input type="hidden" value="{{Session::get('rol')}}" id="rol" name="rol">
        
    </div>
    
        <div id="reportes" class="container-fluid">
            <div class="row">
                <!-- Tarjeta Listado de solicitudes -->
                <div class="col-xl-12 col-lg-5">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Reportes </h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Opciones:</div>
                                    <button class="dropdown-item" >Recargar</button>
                                    <button class="dropdown-item" >Ocultar</button>
                                    <button class="dropdown-item" >Mostrar</button>
                                
                                </div>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class=" mt-4 mb-2  container-fluid border-bottom">
                                <h1>Reportes : {{Session::get('rol')}}</h1>
                                <div class="form-row">
                                    <div class="form-group col-lg-12">
                                        <div class="form-group">
                                          <label for="">Periodo de tiempo</label>
                                          <select class="form-control" name="periodo" id="periodo" v-model="rangoTiempo" @change="getNumSolicitudesThroughTime();generar_Grafica_Comparacion;">
                                            
                                            <option value="1" selected>Hoy</option>
                                            <option value="7">7 Dias</option>
                                            <option value="31">1 Mes</option>
                                            <option value="90">3 Meses</option>
                                          </select>
                                        </div>
                                    </div>
                                    <div class="chart-area">
                                        <canvas id="ComparacionSolicitudesChart"></canvas>
                                    </div>
                                    
                                    
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
                            <h6 class="m-0 font-weight-bold text-primary">Grafica de Tickets</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Opciones:</div>
                                    <button class="dropdown-item" >Recargar Grafica</button>
                                    <button class="dropdown-item" >Ocultar</button>
                                    <button class="dropdown-item" >Mostrar</button>
                                </div>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body" >
                            <div class="chart-pie pt-4 pb-3">
                                <canvas id="SolicitudesUsuarioChart"></canvas>
                            </div>
                            <div class="mt-5 text-center small">
                                {{--<span class="mr-2" v-for="(e,index) in Estatus">
                                    <i :id="index" :class="['fas fa-circle',asignarColor(e.estatus)]"></i>@{{e.estatus}}-@{{e.total}}
                                </span>--}}
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
