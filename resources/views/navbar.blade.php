<div id="notificaciones">
    <!-- Topbar Navbar -->
     <!-- Topbar -->
     <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
  
      <!-- Sidebar Toggle (Topbar) -->
      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
      </button>
  
      <!-- Topbar Search -->
      <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
          <input list="myUL" type="text" id="tickets" name="tickets"class="form-control bg-light border-0 small" placeholder="Buscar ticket..."  v-model="id_ticket" @input="buscarTiket">
          <datalist id="myUL" class="rounded" v-on:change="ir_solicitud(ticket.id_solicitud)">
            <option v-for="(ticket,index) in tickets" :value="ticket.id_solicitud" v-if="index < 4">@{{ticket.medio_reporte}} - @{{ticket.fecha_creacion}} - @{{ticket.descripcion}}</option>
          </datalist>
          <div class="input-group-append">
            <button v-if="banIr" class="btn btn-primary" type="button" v-on:click="ir_solicitud()">
              <i>Ir</i>
            </button>
          </div>
        </div>
      </form>
      <ul class="navbar-nav ml-auto" v-cloak>
  
        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
          <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-search fa-fw"></i>
          </a>
        </li>
  
        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link dropdown-toggle"  id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-danger badge-counter" v-if="cont > 9">9+</span>
            <span class="badge badge-danger badge-counter" v-if="cont > 0 && cont <= 9">@{{cont}}</span>
          </a>
          <!-- Dropdown - Alerts -->
          <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
              Centro de notificaciones.
            </h6>
            <div v-for="(notificacion,index) in notificaciones" v-if="index < 10">
              <a :class="'dropdown-item d-flex align-items-center ' + esLeida(notificacion.status)"  v-on:click="verSolicitud(notificacion.id,notificacion.id_solicitud)">
                <div class="mr-3" v-if="notificacion.atencion.tipo_at == 'Atencion' || notificacion.atencion.tipo_at == 'Atención'">
                  <div class="icon-circle bg-primary">
                    <i class="far fa-comment-dots text-white"></i>
                  </div>
                </div>
                <div class="mr-3" v-if="notificacion.atencion.tipo_at == 'Asignacion'">
                  <div class="icon-circle bg-success">
                    <i class="fas fa-briefcase text-white"></i>
                  </div>
                </div>
                <div class="mr-3" v-if="notificacion.atencion.tipo_at == 'Estatus'">
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
            <a class="dropdown-item text-center small text-gray-500" href="/notificaciones">Ver todas las notificaciones.</a>
          </div>
        </li>
  
        <div class="topbar-divider d-none d-sm-block"></div>
  
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Session::get('nombre')}}</span>
            <img class="img-profile rounded-circle" src="{{ asset('assets/images/logo_edu.png')}}">
          </a>
          <!-- Dropdown - User Information -->
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="/logout" >
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Cerrar sesión
            </a>
          </div>
        </li>
    </ul>
  
    </nav>
  </div>
  <script type="text/javascript" src="{{asset('assets/vue/notificaciones.js')}}"></script>
  <!-- End of Topbar -->
  