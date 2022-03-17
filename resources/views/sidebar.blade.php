<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    {{--<li class="nav-item">
      <a class="nav-link" href="index.html">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="pages/widgets/widgets.html">
        <i class="mdi mdi-tune menu-icon"></i>
        <span class="menu-title">Widgets</span>
      </a>
    </li>--}}
    <li class="nav-item nav-category">Catalogos</li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic2" aria-expanded="false" aria-controls="ui-basic2">
        <i class="icon-bell me-1"></i>
        <span class="menu-title">Solicitudes</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic2">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="alta_ticket">Alta</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Edición</a></li>
          <li class="nav-item"> <a class="nav-link" href="listado">Listado</a></li>
        </ul>
      </div>
    </li>
    @if(Session::get('rol')=='Administrador')
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-advanced" aria-expanded="false" aria-controls="ui-advanced">
        <i class="icon-user me-1"></i>
        <span class="menu-title">Usuarios</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-advanced">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="/">Alta</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Baja</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Edición</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Listado</a></li>
        </ul>
      </div>
    </li>
    @endif
    @if(Session::get('rol')=='Administrador')
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-departamento" aria-expanded="false" aria-controls="ui-departamento">
        <i class="icon-list me-1"></i>
        <span class="menu-title">Departamentos</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-departamento">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="/">Alta</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Baja</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Edición</a></li>
          <li class="nav-item"> <a class="nav-link" href="/">Listado</a></li>
        </ul>
      </div>
    </li>
    @endif
    @if(Session::get('rol')== 'Administrador'|| Session::get('rol')=='Directivo')
    <li class="nav-item nav-category">Funciones</li>
    <li class="nav-item">
      <a class="nav-link" href="/">
        <i class="icon-speedometer me-1"></i>
        <span class="menu-title">Reportes</span>
      </a>
    </li>
    @endif
    {{--<li class="nav-item">
      <a class="nav-link" href="../../pages/apps/calendar.html">
        <i class="menu-icon mdi mdi-calendar"></i>
        <span class="menu-title">Calendar</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../../pages/apps/todo.html">
        <i class="menu-icon mdi mdi-format-list-bulleted"></i>
        <span class="menu-title">Todo List</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../../pages/apps/gallery.html">
        <i class="menu-icon mdi mdi-file-image-outline"></i>
        <span class="menu-title">Gallery</span>
      </a>
    </li>
    <li class="nav-item nav-category">help</li>
    <li class="nav-item">
      <a class="nav-link" href="https://bootstrapdash.com/demo/star-admin2-pro/docs/documentation.html">
        <i class="menu-icon mdi mdi-file-document"></i>
        <span class="menu-title">Documentation</span>
      </a>
    </li>--}}
  </ul>
</nav>