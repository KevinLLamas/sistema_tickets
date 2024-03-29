@extends('app')
@section('content')


<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h1 text-gray-800">#11 Tengo problemas con mi cuenta de correo electrónico</h1>
    <select class="selectpicker my-2" data-style="btn-primary">
        <option option="Sin atender">Sin atender</option>
        <option option="Atendiendo">Atendiendo</option>
        <option option="Suspendida">Suspendida</option>
        <option option="Cerrada">Cerrada</option>
    </select>
    <p><i class="far fa-clock"></i> 10-01-20 10:53 - Atendiendo:
        <select class="selectpicker" data-live-search="true">
            <option data-tokens="ketchup mustard">Juan López García</option>
            <option data-tokens="mustard">Armando González Gutierrez</option>
            <option data-tokens="frosting">Luis Márquez Hernández</option>
        </select></p>
    <p class="alert alert-info"><small>Categoría: Correo institucional - Subcategoría: No puedo ingresar a mi correo
            electrónico <i class="fas fa-reply-all"></i> Respuestas: 2</small></p>
    <hr>

    <h3 class="h3 text-gray-800">Resumen</h3>
    <div class="card bg-white ">

        <div class="card-body">
            <p><i class="far fa-edit"></i> <b>10-01-20 10:53pm - Ticket creado por José de Jésús Jiménez</b></p>
            <p class="card-text">Tengo un problema con mi cuenta @jaliscoedu.mx y no puedo entrar. Por favor ayuda.</p>
            <p><small>Datos adicionales: <i class="fas fa-at"></i> Correo institucional:
                    <b>jose.jimenez@jaliscoedu.mx</b> <i class="far fa-id-badge"></i> CURP: <b>JIGJ930417HCCMRS05</b> <i
                        class="fas fa-mobile-alt"></i> Celular: <b>3213213663</b></small></p>
        </div>
    </div>
    <hr>

    <div class="card bg-white ">

        <div class="card-body">
            <p><i class="far fa-edit"></i> <b>10-01-20 10:53pm - Comentario agregado por José de Jésús Jiménez</b></p>
            <p class="card-text">Tengo un problema con mi cuenta @jaliscoedu.mx y no puedo entrar. Por favor ayuda.</p>

        </div>
    </div>

    <hr>

    <div class="card alert alert-warning  border-warning ">

        <div class="card-body">
            <p><i class="far fa-sticky-note"></i> <b>10-01-20 10:53pm - Nota interna agregada por Juan López García</b>
            </p>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
                content.</p>
        </div>
    </div>

    <hr>


    <div class="form-group row">
        <label for="prioridad" class="col-1 col-form-label">Prioridad</label>
        <div class="col-3">
            <select class="selectpicker my-2" data-style="btn-primary">
                <option option="Sin atender">Sin atender</option>
                <option option="Atendiendo">Atendiendo</option>
                <option option="Suspendida">Suspendida</option>
                <option option="Cerrada">Cerrada</option>
            </select>
        </div>
        <label for="grupo" class="col-1 col-form-label">Grupo</label>
        <div class="col-3">
            <select class="selectpicker my-2" data-style="btn-primary">
                <option option="Sin atender">Sin atender</option>
                <option option="Atendiendo">Atendiendo</option>
                <option option="Suspendida">Suspendida</option>
                <option option="Cerrada">Cerrada</option>
            </select>
        </div>
        <label for="departamento" class="col-1 col-form-label">Departamento</label>
        <div class="col-3">
            <select class="selectpicker my-2" data-style="btn-primary">
                <option option="Sin atender">Sin atender</option>
                <option option="Atendiendo">Atendiendo</option>
                <option option="Suspendida">Suspendida</option>
                <option option="Cerrada">Cerrada</option>
            </select>
        </div>
    </div>




    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                aria-selected="true">Responder</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                aria-selected="false">Agregar nota interna</a>
        </li>

    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="form-group row mt-5">

                <div class="col">
                    <textarea id="editor" name="problema" cols="40" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="btn btn-default">
                    <i class="fas fa-paperclip"></i> Adjuntar archivos <input type="file" hidden>
                </label>
            </div>
            <div class="form-group row">
                <div class="col">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Responder
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Responder</a>
                            <a class="dropdown-item" href="#">Responder y abrir</a>
                            <a class="dropdown-item" href="#">Responder y suspender</a>
                            <a class="dropdown-item" href="#">Responder y resolver</a>
                            <a class="dropdown-item" href="#">Responder y terminar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            <div class="form-group row mt-5">

                <div class="col">
                    <textarea id="editor" name="problema" cols="40" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="btn btn-default">
                    <i class="fas fa-paperclip"></i> Adjuntar archivos <input type="file" hidden>
                </label>
            </div>
            <div class="form-group row">
                <div class="col">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Responder
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Responder</a>
                            <a class="dropdown-item" href="#">Responder y abrir</a>
                            <a class="dropdown-item" href="#">Responder y suspender</a>
                            <a class="dropdown-item" href="#">Responder y resolver</a>
                            <a class="dropdown-item" href="#">Responder y terminar</a>
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

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div>


@endsection
