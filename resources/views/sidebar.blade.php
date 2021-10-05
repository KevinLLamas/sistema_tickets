 <!-- Sidebar -->
 <ul class="navbar-nav bg-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
      <div class="sidebar-brand-icon rotate-n-10">
        <i class="fas fa-question-circle"></i>
      </div>
      <div class="sidebar-brand-text mx-3">SASS</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard-->
    <li class="nav-item active">
      <a class="nav-link" href="/sass">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
    </li>
    
    <!--<li class="nav-item">
      <a class="nav-link" href="lista_solicitudes">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Lista de tickets admin</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="mis_solicitudes">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Lista de tickets trabajador</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="solicitudes_departamento">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Lista de tickets departamento</span></a>
    </li> -->
    

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
      Interfaz
    </div>
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSolicitud" aria-expanded="true" aria-controls="collapseSolicitud">
        <i class="fas fa-envelope-open-text"></i>
        <span>Tickets</span>
      </a>
      <div id="collapseSolicitud" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Opciones:</h6>
          <a class="collapse-item" href="/sass/alta_ticket">Crear Ticket</a>
          <!--a class="collapse-item" href="/sass/alta_solicitud_servicio">Solicitar un Servicio</a-->
          <a class="collapse-item" href="/sass/dashboard">Listado</a>
        </div>
      </div>
    </li>
    @if(Session::get('rol') == 'SUPER' || Session::get('rol') == 'ADMIN' )
    <li class="nav-item">
      <a class="nav-link" href="/sass/reportes">
        <i class="fas fa-fw fa-cog"></i>
        <span>Reportes</span></a>
    </li>
    @endif
    <!-- Nav Item - Pages Collapse Menu 
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSolicitud2" aria-expanded="true" aria-controls="collapseSolicitud">
        <i class="fas fa-envelope-open-text"></i>
        <span>Tickets Local</span>
      </a>
      <div id="collapseSolicitud2" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Opciones:</h6>
          <a class="collapse-item" href="/alta_ticket">Crear Ticket</a>
          <a class="collapse-item" href="/dashboard">Listado</a>
        </div>
      </div> 
    </li>-->

    <!-- Nav Item - Pages Collapse Menu -->
    {{-- <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-cog"></i>
        <span>Reportes</span>
      </a>
      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Reportes:</h6>
          <a class="collapse-item" href="/sass/reportes">Ver reportes</a>
        </div>
      </div>
    </li> --}}

    <!-- Nav Item - Utilities Collapse Menu 
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-wrench"></i>
        <span>Utilities</span>
      </a>
      <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Custom Utilities:</h6>
          <a class="collapse-item" href="utilities-color.html">Colors</a>
          <a class="collapse-item" href="utilities-border.html">Borders</a>
          <a class="collapse-item" href="utilities-animation.html">Animations</a>
          <a class="collapse-item" href="utilities-other.html">Other</a>
        </div>
      </div>
    </li>-->

    <!-- Divider 
    <hr class="sidebar-divider">-->

    <!-- Heading 
    <div class="sidebar-heading">
      Addons
    </div>-->

    <!-- Nav Item - Pages Collapse Menu 
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-folder"></i>
        <span>Pages</span>
      </a>
      <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Login Screens:</h6>
          <a class="collapse-item" href="login.html">Login</a>
          <a class="collapse-item" href="register.html">Register</a>
          <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
          <div class="collapse-divider"></div>
          <h6 class="collapse-header">Other Pages:</h6>
          <a class="collapse-item" href="404.html">404 Page</a>
          <a class="collapse-item" href="blank.html">Blank Page</a>
        </div>
      </div>
    </li>-->

    <!-- Nav Item - Charts 
    <li class="nav-item">
      <a class="nav-link" href="charts.html">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Charts</span></a>
    </li>-->

    <!-- Nav Item - Tables 
    <li class="nav-item">
      <a class="nav-link" href="tables.html">
        <i class="fas fa-fw fa-table"></i>
        <span>Tables</span></a>
    </li>-->

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

  </ul>
  <!-- End of Sidebar -->

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

     
