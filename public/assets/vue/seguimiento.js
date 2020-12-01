var url = window.location.pathname;
var getId = url.substring(url.lastIndexOf('/')+1);

new Vue({
    el: '#app',
    data:{
        var: '',
        seguimiento: {
            subcategoria:
            {
                nombre: '',
            },
            perfil:
            {
                nombre: '',
            },
            categoria:
            {
                nombre: '',
            },
            departamentos_seleccionados_id: [],
        },
        nueva_atencion:{
            detalle: '',
            id_solicitud: '',
            id_usuario: '',
            tipo_at: '',
            tipo_respuesta: '',
            fecha_finalizado: '',
        },
        files: {},
        id_atencion: '',
        codigo: '',
        banVerif: false,
        id: '',
        user: '',
        departamentoValido: '',
        integrantesSeleccionados: [],
        integrantesSeleccionadosCompleto: [],
        integrantesSeleccionadosSolicitud: [],
        integrantesSeleccionadosCompletoSolicitud: [],
        integrantesSeleccionadoAntesUpdate: [],
        integrantesDesasignados: [],
        integrantesAsignados: [],
        departamentos: [],        
        departamentosAntesUpdate: [],
        subcategoriaAntesUpdate: '',
        categoriaAntesUpdateId: '',
        banCambio: false,
        banCambioDpto: false,
        banCambioSubcategoria: false,
        banSlimDest: false,
        Categorias: '',
        Subcategorias: ''
    },
    created: function(){
        this.muestra();        
    },
    methods:{
        muestra: function(){                     
            if(getId>0)
            axios.get(`../getSolicitud/`+getId).then(response=>{
                this.seguimiento = response.data;    
                if(!this.banCambioDpto)
                    this.departamentosAntesUpdate = this.seguimiento.departamento;  
                if(!this.banCambioSubcategoria)
                {
                    this.subcategoriaAntesUpdate = this.seguimiento.subcategoria.nombre; 
                    this.categoriaAntesUpdateId = this.seguimiento.categoria.id;
                }         
                this.getUserData();
                this.compruebaCerradoAuto();
                this.getCategorias();
                this.getSubcategorias('back');
            }).catch(function (error) {
                console.log(error);
            });            
        },
        muestra2: function(){
            axios.get(`../getSolicitud/`+this.id).then(response=>{
                this.seguimiento = response.data;
                this.validatePermissionExterno();
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        agregarAtencion: function(tipo, accion){
            var ban = false;
            if(this.nueva_atencion.detalle)
            {
                this.nueva_atencion.id_usuario= this.user.id_sgu;
                this.nueva_atencion.tipo_respuesta = tipo;
                this.nueva_atencion.id_solicitud = this.seguimiento.id_solicitud;
                axios.post('../inserta_atencion',{
                    data: this.nueva_atencion,
                    codigo: this.codigo,
                    email: this.seguimiento.correo_atencion,
                    rol: this.user.rol,
                    estatus: this.seguimiento.estatus
                }).then(response=>{
                    //console.log(response);
                    this.id_atencion = response.data.id;
                    if(response.data.primer)
                    {
                        accion = 'Abrir';
                    }
                    if(this.id === '')
                        this.muestra();
                    else
                        this.muestra2();     
                    if(accion != 'cambio_estatus')
                        this.saveFiles();              
                    if(accion != '' && accion != 'Asignacion'  && accion != 'cambio_estatus')
                    {
                        switch(accion)
                        {
                            case 'Abrir':
                                if(this.seguimiento.estatus != "Atendiendo")
                                {
                                    this.seguimiento.estatus = "Atendiendo";
                                }
                            break;
                            case 'Suspender':
                                if(this.seguimiento.estatus != "Suspendida")
                                {
                                    this.seguimiento.estatus = "Suspendida";
                                }                                
                            break;
                            case 'Resolver':
                                if(this.seguimiento.estatus != "Cerrada (En espera de aprobación)")
                                {
                                    this.seguimiento.estatus = "Cerrada (En espera de aprobación)";
                                }                                
                            break;
                            case 'Terminar':
                                if(this.seguimiento.estatus != "Cerrada (En espera de aprobación)")
                                {
                                    this.seguimiento.estatus = "Cerrada (En espera de aprobación)";
                                }                                
                            break;
                            default:

                            break;
                        }  
                        this.cambiarEstatus();                      
                    }
                    if(accion == '')
                    {
                        if(tipo == 'Interna')
                            Swal.fire({
                                icon: 'success',
                                title: 'Nota agregada correctamente.',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        else
                            Swal.fire({
                                icon: 'success',
                                title: 'Comentario agregado correctamente.',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        
                    }
                    this.nueva_atencion.detalle = '';
                    this.nueva_atencion.tipo_respuesta = '';
                    this.nueva_atencion.tipo_at = '';
                }).catch(error=>{
                    console.log(error);
                });
            }
            else
            {
                if(accion == '')
                {
                    Swal.fire('Error','Llene el campo de texto','warning');
                }
            }
        },
        agregarAtencionExterno: function(tipo, accion){
            var ban = false;
            if(this.nueva_atencion.detalle)
            {
                this.nueva_atencion.tipo_respuesta = tipo;
                this.nueva_atencion.id_solicitud = this.seguimiento.id_solicitud;
                axios.post('../inserta_atencion_externo',{
                    data: this.nueva_atencion,
                    codigo: this.codigo,
                    email: this.seguimiento.usuario.correo,
                    fecha_finalizado: this.nueva_atencion.fecha_finalizado,
                }).then(result=>{
                    console.log(result);
                    this.id_atencion = result.data;
                    if(this.id === '')
                        this.muestra();
                    else
                        this.muestra2();
                    if(accion != '' && accion != 'cambio_estatus')
                    {
                        switch(accion)
                        {
                            case 'Abrir':
                                if(this.seguimiento.estatus != "Atendiendo")
                                {
                                    this.seguimiento.estatus = "Atendiendo";
                                }
                            break;
                            case 'Suspender':
                                if(this.seguimiento.estatus != "Suspendida")
                                {
                                    this.seguimiento.estatus = "Suspendida";
                                }                                
                            break;
                            case 'Resolver':
                                if(this.seguimiento.estatus != "Cerrada")
                                {
                                    this.seguimiento.estatus = "Cerrada";
                                }                                
                            break;
                            case 'Terminar':
                                if(this.seguimiento.estatus != "Cerrada")
                                {
                                    this.seguimiento.estatus = "Cerrada";
                                }                                
                            break;
                        }  
                        this.cambiarEstatus();                      
                    }
                    if(accion == '')
                    {
                        Swal.fire({
                            icon: 'success',
                            title: 'Comentario agregado correctamente.',
                            showConfirmButton: false,
                            timer: 1000
                        })
                    }
                    this.saveFiles();
                    this.nueva_atencion.detalle = '';
                    this.nueva_atencion.tipo_respuesta = '';
                    this.nueva_atencion.tipo_at = '';
                }).catch(error=>{
                    console.log(error);
                });
            }
            else
            {
                Swal.fire('Error','Llene el campo de texto','warning');
            }
        },
        cambiarEstatus: function(){
            axios.post('../cambiar_estatus',{
                id: this.seguimiento.id_solicitud,
                estatus: this.seguimiento.estatus
             }).then(result=>{           
                 //this.getSesiones();
                 this.seguimiento.estatus = result.data;
                 if(this.seguimiento.estatus == 'Cerrada')
                    Swal.fire({
                        icon: 'success',
                        title: 'El ticket fue cerrado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    }) 
                else
                 Swal.fire({
                     icon: 'success',
                     title: 'Se cambio el estatus a '+this.seguimiento.estatus,
                     showConfirmButton: false,
                     timer: 2000
                 })                 
                this.nueva_atencion.detalle= 'Cambio de estatus a ' + this.seguimiento.estatus;
                this.nueva_atencion.id_usuario= this.user.id_sgu;
                this.nueva_atencion.tipo_at= 'Estatus';
                this.agregarAtencion('Todos', 'cambio_estatus');
                this.nueva_atencion.estatus = "";
             }).catch(error=>{
                console.log(error);
             });
        },
        cambiarEstatusExterno: function(estatus){
            this.seguimiento.estatus = estatus;
            axios.post('../cambiar_estatus',{
                id: this.seguimiento.id_solicitud,
                estatus: this.seguimiento.estatus
             }).then(result=>{           
                 //this.getSesiones();
                 this.seguimiento.estatus = result.data;
                 if(this.seguimiento.estatus == 'Cerrada')
                    Swal.fire({
                        icon: 'success',
                        title: 'El ticket fue cerrado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    }) 
                else
                 Swal.fire({
                     icon: 'success',
                     title: 'Se cambio el estatus a '+this.seguimiento.estatus,
                     showConfirmButton: false,
                     timer: 2000
                 })                 
                this.nueva_atencion.detalle= 'Cambio de estatus a ' + this.seguimiento.estatus;
                this.nueva_atencion.tipo_at= 'Estatus';
                this.agregarAtencionExterno('Todos', 'cambio_estatus');
                this.nueva_atencion.estatus = "";
             }).catch(error=>{
                console.log(error);
             });
        },
        
         fileChangeFormato(e){
            if(e.target.files.length > 1){
                 if(e.target.files.length < 5){
                     for (let i = 0; i < e.target.files.length; i++){
                         let nombre = e.target.files[i].name.split('.');
                         let extencion = nombre[1].toLowerCase();
                         if(!(extencion == 'pdf' || extencion == 'xls' || extencion == 'png' || extencion == 'jpg' || extencion == 'jpeg')){
                             swal.fire('Incorrecto','El archivo '+e.target.files[i].name+' tiene una extensión no permitida' ,'warning');
                             return;
                         }
                     }
                     this.files = e.target.files;
                     $("#label_formato").text(e.target.files.length + ' Archivos');
                 }
                 else{
                     swal.fire('Incorrecto','Solo puedes seleccionar un maximo de 4 archivos','warning');
                     this.files = {};
                     $("#label_formato").text('Selecciona Archivos');
                 }
            }
            else{
                 let nombre = e.target.files[0].name.split('.');
                 let extencion = nombre[1].toLowerCase();
                 if(!(extencion == 'pdf' || extencion == 'xls' || extencion == 'png' || extencion == 'jpg' || extencion == 'jpeg')){
                     swal.fire('Incorrecto','El archivo '+e.target.files[0].name+' tiene una extensión no permitida' ,'warning');
                     return;
                 }
                 this.files = e.target.files;
                 $("#label_formato").text(e.target.files[0].name);
                 
            }
         },
         fileChangeFormatoNotes(e){
            if(e.target.files.length > 1){
                 if(e.target.files.length < 5){
                     for (let i = 0; i < e.target.files.length; i++){
                         let nombre = e.target.files[i].name.split('.');
                         let extencion = nombre[1].toLowerCase();
                         if(!(extencion == 'pdf' || extencion == 'xls' || extencion == 'png' || extencion == 'jpg' || extencion == 'jpeg')){
                             swal.fire('Incorrecto','El archivo '+e.target.files[i].name+' tiene una extensión no permitida' ,'warning');
                             return;
                         }
                     }
                     this.files = e.target.files;
                     $("#label_formato_notes").text(e.target.files.length + ' Archivos');
                 }
                 else{
                     swal.fire('Incorrecto','Solo puedes seleccionar un maximo de 4 archivos','warning');
                     this.files = {};
                     $("#label_formato_notes").text('Selecciona Archivos');
                 }
            }
            else{
                 let nombre = e.target.files[0].name.split('.');
                 let extencion = nombre[1].toLowerCase();
                 if(!(extencion == 'pdf' || extencion == 'xls' || extencion == 'png' || extencion == 'jpg' || extencion == 'jpeg')){
                     swal.fire('Incorrecto','El archivo '+e.target.files[0].name+' tiene una extensión no permitida' ,'warning');
                     return;
                 }
                 this.files = e.target.files;
                 $("#label_formato_notes").text(e.target.files[0].name);
                 
            }
         },
         saveFiles: function(){  
            if(this.files.length != undefined){
                swal.fire({ title: "Subiendo Archivos...", imageUrl: "../assets/images/loading-79.gif", imageWidth: 250, imageHeight: 250, showConfirmButton: false,});
                const config = {headers: { 'content-type': 'multipart/form-data' }};
                let formData = new FormData();
                formData.set('id_solicitud', this.seguimiento.id_solicitud);
                formData.set('id_atencion', this.id_atencion);
                for (let i = 0; i < this.files.length; i++) 
                {
                    if(this.files[0].size < (3*1000000))
                        formData.append('files[' + i + ']', this.files[i]);
                    else{
                        swal.close();
                        Swal.fire('Atención','El archivo '+ this.files[0].name + ' pesa más de 3MB','warning');
                        return;
                    }
                }
                axios.post('../save_files', formData, config)
                .then(response => {
                    if(response.data.status){
                        swal.close();
                        //Swal.fire('Atención','Comentario agregado correctamente.','success');
                        if(this.id === '')
                            this.muestra();
                        else
                            this.muestra2();
                        this.files = [];
                        $("#label_formato").text('Selecciona Archivos');
                    }
                    else{
                        swal.close();
                        Swal.fire('Atención','Comentario agregado correctamente.','info');
                    }
                }).catch(error=>{
                    console.log(error);
                })
            }
            else{
                //Swal.fire('Atención','No tienes archivos por guardar','warning');
            }
        },
         verificar: function(){
            this.id = $("#id_orig").val();
        
            axios.post('../verifica_codigo',{
                codigo: this.codigo,
                id: this.id
            }).then(response=>{
                this.banVerif = response.data.status;
                if(this.banVerif)
                {
                    this.id = response.data.id_solicitud;
                    this.muestra2();
                }
                else
                {
                    Swal.fire('Incorrecto','Codigo no corresponde','warning');
                }
            }).catch(function (error) {
            });
        },
        getUserData: function(){            
            axios.get('../getUserData').then(response=>{
                this.user = response.data;   
                this.validatePermission();       
                this.eventosCambiosAsignacion();
                this.eventoCambioDpto();
                this.eventoCambioSubcategoria();
                if(this.user.rol == 'TECNICO')
                {
                    //slim.destroy();
                    //slim2.destroy();
                } 
            }).catch(function (error) {
                console.log(error);
            });
            //this.styleObject.display = 'none';
        },
        validatePermission: function()
        {
            this.integrantesSeleccionadosCompletoSolicitud = [];
            this.integrantesSeleccionadosCompleto = [];
            this.integrantesSeleccionados = [];
            this.departamentoValido = '';
            var c = 0;
            for(var i = 0; i < this.seguimiento.departamento.length; i++)
            {            
                if(this.seguimiento.departamento[i].id == this.user.id_departamento)
                    this.departamentoValido = this.seguimiento.departamento[i];
                for(var z = 0; z < this.seguimiento.departamento[i].integrantes.length; z++)
                {
                    for(var x = 0; x < this.seguimiento.solicitud_usuario.length; x++)
                    {
                        if(this.seguimiento.departamento[i].integrantes[z].id_sgu == this.seguimiento.solicitud_usuario[x].id_usuario && this.seguimiento.solicitud_usuario[x].estado === "Atendiendo")
                        { 
                            this.integrantesSeleccionadosSolicitud[c] = this.seguimiento.departamento[i].integrantes[z].id_sgu;
                            this.integrantesSeleccionadosCompletoSolicitud[c] = this.seguimiento.departamento[i].integrantes[z];
                            c++;
                        }
                    }                    
                }  
            }
            c = 0;
            if(this.departamentoValido != '')
            {
                if(this.banSlimDest)
                {
                    slim.enable();
                }
                this.isSection1 = true;
                //this.styleObject.display = 'run-in';
                for(var i = 0; i < this.departamentoValido.integrantes.length; i++)
                {
                    //console.log(this.seguimiento.departamentoValido);
                    for(var x = 0; x < this.seguimiento.solicitud_usuario.length; x++)
                    {
                        //console.log(this.departamentoValido.integrantes[i].id);
                        if(this.departamentoValido.integrantes[i].id_sgu == this.seguimiento.solicitud_usuario[x].id_usuario && this.seguimiento.solicitud_usuario[x].estado === "Atendiendo")
                        { 
                            this.integrantesSeleccionados[c] = this.departamentoValido.integrantes[i].id_sgu;
                            this.integrantesSeleccionadosCompleto[c] = this.departamentoValido.integrantes[i];
                            c++;
                        }
                    }                    
                }  
            }
            else
            {
                if(!this.banSlimDest)
                {
                    slim.disable();
                } 
                this.banSlimDest = true;
            }
            if(this.seguimiento.estatus === 'Cerrada (En espera de aprobación)' && this.user === '')
            {
                Swal.fire('Espera de aprobación','El ticket se ha marcado como cerrado, reviselo y apruebe este estado, o cancele para volver a abrirla','info');
            }              
        },
        validatePermissionExterno: function()
        {
            var c = 0;
            for(var i = 0; i < this.seguimiento.departamento.length; i++)
            {               
                for(var z = 0; z < this.seguimiento.departamento[i].integrantes.length; z++)
                {
                    for(var x = 0; x < this.seguimiento.solicitud_usuario.length; x++)
                    {
                        if(this.seguimiento.departamento[i].integrantes[z].id_sgu == this.seguimiento.solicitud_usuario[x].id_usuario && this.seguimiento.solicitud_usuario[x].estado === "Atendiendo")
                        { 
                            this.integrantesSeleccionadosSolicitud[c] = this.seguimiento.departamento[i].integrantes[z].id_sgu;
                            this.integrantesSeleccionadosCompletoSolicitud[c] = this.seguimiento.departamento[i].integrantes[z];
                            c++;
                        }
                    }                    
                }  
            }
            if(this.seguimiento.estatus === 'Cerrada (En espera de aprobación)')
            {
                Swal.fire('Espera de aprobación','El ticket se ha marcado como cerrado, reviselo y apruebe este estado, o cancele para volver a abrirlo','info');
            }    
            
        },
        updateIntegrantes: function()
        {
            this.integrantesSeleccionadoAntesUpdate = [];
            this.integrantesSeleccionadoAntesUpdate = this.integrantesSeleccionadosCompleto;
            this.banCambio = true;
            axios.post('../UpdateSolicitud_usuario',{
                integrantes: this.integrantesSeleccionados,
                id_solicitud: this.seguimiento.id_solicitud,
                id_departamento: this.user.id_departamento,
            }).then(response=>{
                //console.log(response);
                this.muestra();                
                var c = 0;  
                Swal.fire('Correcto','Se han actualizado los usuarios','success');                
            }).catch(function (error) {
                //console.log(error);
            });
        },
        updateDepartamento: function()
        {
            this.banCambioDpto= true;
            axios.post('../update_departamentos',{
                id_solicitud: this.seguimiento.id_solicitud,
                id_departamentos: this.seguimiento.departamentos_seleccionados_id,
            }).then(response=>{
                console.log(response);
                this.muestra();                
                var c = 0;  
                Swal.fire('Correcto','Se actualizo la asignación de departamento','success');                
            }).catch(function (error) {
                console.log(error);
            });
        },
        eventoCambioDpto: function()
        {
            if(this.banCambioDpto)
            {
                if(this.departamentosAntesUpdate.length < this.seguimiento.departamento.length)//Se asigno otro
                {
                    for(var x = 0; x < this.seguimiento.departamento.length; x++)
                    {
                        const busq = this.departamentosAntesUpdate.find( departamento => departamento.id == this.seguimiento.departamento[x].id);
                        if(busq === undefined)
                        {
                            this.nueva_atencion.detalle= 'asignó a el departamento ' + this.seguimiento.departamento[x].nombre + ' a este ticket';
                        }
                        //console.log(busq);
                        //const busq = this.seguimiento.departamento.find( departamento => departamento.id == this.departamentosAntesUpdate);
                    } 
                } 
                else if(this.departamentosAntesUpdate.length > this.seguimiento.departamento.length)
                {                
                    for(var x = 0; x < this.departamentosAntesUpdate.length; x++)
                    {
                        const busq = this.seguimiento.departamento.find( departamento => departamento.id == this.departamentosAntesUpdate[x].id);
                        if(busq === undefined)
                        {
                            this.nueva_atencion.detalle= 'desasignó a el departamento ' + this.departamentosAntesUpdate[x].nombre + ' de este ticket';
                        }
                        //console.log(busq);
                        //const busq = this.seguimiento.departamento.find( departamento => departamento.id == this.departamentosAntesUpdate);
                    }
                }                
                
                this.nueva_atencion.id_usuario= this.user.id_sgu;
                this.nueva_atencion.tipo_at= 'Asignacion';
                this.agregarAtencion('Todos', 'Asignacion');
                this.nueva_atencion.estatus = "";
                this.banCambioDpto = false;
            }
        },
        eventosCambiosAsignacion: function()
        {
            if(this.banCambio)
            {
                this.integrantesDesasignados = [];
                this.integrantesAsignados = [];
                //Integrantes desasignados
                for(var x = 0; x < this.integrantesSeleccionadoAntesUpdate.length; x++)
                {
                    let ban = false;
                    for(var i = 0; i < this.integrantesSeleccionadosCompleto.length; i++)
                    {
                        if(this.integrantesSeleccionadoAntesUpdate[x].id_sgu== this.integrantesSeleccionadosCompleto[i].id_sgu)
                            ban = true;
                    }
                    if(!ban)
                    {
                        this.integrantesDesasignados[this.integrantesDesasignados.length] = this.integrantesSeleccionadoAntesUpdate[x];
                        this.banCambio = false;
                        this.nueva_atencion.detalle= 'desasignó a ' + this.integrantesSeleccionadoAntesUpdate[x].nombre + ' de este ticket.';;
                        this.nueva_atencion.id_usuario= this.user.id_sgu;
                        this.nueva_atencion.tipo_at= 'Asignacion';
                        this.agregarAtencion('Todos', 'Asignacion');
                        this.nueva_atencion.estatus = "";

                    }
                }

                //Integrantes Asignados
                for(var x = 0; x < this.integrantesSeleccionadosCompleto.length; x++)
                {
                    let ban = false;
                    for(var i = 0; i < this.integrantesSeleccionadoAntesUpdate.length; i++)
                    {
                        if(this.integrantesSeleccionadosCompleto[x].id_sgu == this.integrantesSeleccionadoAntesUpdate[i].id_sgu)
                            ban = true;
                    }
                    if(!ban)
                    {
                        this.integrantesAsignados[this.integrantesAsignados.length] = this.integrantesSeleccionadosCompleto[x];
                        this.banCambio = false;
                        this.nueva_atencion.detalle= 'asignó a ' + this.integrantesSeleccionadosCompleto[x].nombre + ' a este ticket.';
                        this.nueva_atencion.id_usuario= this.user.id_sgu;
                        this.nueva_atencion.tipo_at= 'Asignacion';
                        this.agregarAtencion('Todos', 'Asignacion');
                        this.nueva_atencion.estatus = "";
                    }
                }
            }
        },

        compruebaCerradoAuto: function (){           
            if(this.seguimiento.estatus == 'Cerrada')
            {
                var ultimaAtencionUsuario = '';
                var ultimaAtencionDetalle = '';
                if(this.seguimiento.atencion.length > 0)
                {
                    ultimaAtencionUsuario = this.seguimiento.atencion[this.seguimiento.atencion.length-1].correo_usuario;
                    ultimaAtencionDetalle = this.seguimiento.atencion[this.seguimiento.atencion.length-1].detalle;
                    if(ultimaAtencionUsuario != 'Sistema' && ultimaAtencionDetalle != 'Cambio de estatus a Cerrada')
                    {
                        this.nueva_atencion.detalle= 'Vencio tiempo. Solicitud cerrada automaticamente por el sistema';
                        this.nueva_atencion.tipo_at= 'Estatus';
                        this.nueva_atencion.fecha_finalizado = this.seguimiento.fecha_finalizado;
                        this.agregarAtencionExterno('Todos', 'cambio_estatus');
                        this.nueva_atencion.estatus = "";                        
                    }
                }

            }
        },

        getCategorias: function(){
            this.categoria = '';
            this.subcategoria = '';
            this.Campos = [];
            axios.get('../categorias?id_perfil='+this.seguimiento.perfil.id)
            .then(result =>{
                console.log(result.data);
                this.Categorias = result.data;
            }).catch(error=>{
                console.log(error);
            })
        },

        getSubcategorias: function(tipo){
            if(tipo != 'back' && this.seguimiento.categoria.id != this.categoriaAntesUpdateId)
                this.seguimiento.subcategoria.id = '';
            this.subcategoria = '';
            this.Campos = [];
            axios.get('../subcategorias?id_categoria='+this.seguimiento.categoria.id).then(result =>{
                console.log(result.data);
                this.Subcategorias = result.data;
            }).catch(error=>{
                console.log(error);
            });
        },

        UpdateSubcategoria: function()
        {            
            if(this.seguimiento.subcategoria.id != '')
            {
                this.banCambioSubcategoria = true;
                axios.post('../update_subcategoria',{
                    id_solicitud: this.seguimiento.id_solicitud,
                    id_subcategoria: this.seguimiento.subcategoria.id,
                    id_categoria: this.seguimiento.categoria.id
                }).then(response=>{
                    console.log(response);                
                    this.muestra();
                    Swal.fire('Correcto','Cambio la categoria y/o subcategoria','success');  
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },

        eventoCambioSubcategoria: function()
        {
            if(this.banCambioSubcategoria)
            {
                if(this.seguimiento.categoria.id != this.categoriaAntesUpdateId)
                {
                    const nombre = this.Categorias.find( categoria => categoria.id == this.categoriaAntesUpdateId ).nombre;
                    this.nueva_atencion.detalle= 'cambio categoria de ' + nombre + ' a ' + this.seguimiento.categoria.nombre + ' y cambio subcategoria de ' + this.subcategoriaAntesUpdate + ' a ' + this.seguimiento.subcategoria.nombre;;
                    this.nueva_atencion.id_usuario= this.user.id_sgu;
                    this.nueva_atencion.tipo_at= 'Asignacion';
                    this.agregarAtencion('Todos', 'Asignacion');
                    this.nueva_atencion.estatus = "";
                }
                else
                {
                    this.nueva_atencion.detalle= 'cambio subcategoria de ' + this.subcategoriaAntesUpdate + ' a ' + this.seguimiento.subcategoria.nombre;
                    this.nueva_atencion.id_usuario= this.user.id_sgu;
                    this.nueva_atencion.tipo_at= 'Asignacion';
                    this.agregarAtencion('Todos', 'Asignacion');
                    this.nueva_atencion.estatus = "";
                }

                this.banCambioSubcategoria = false;

                var banDpto = false;
                var arr = [];
                for(var x = 0; x < this.seguimiento.departamentos_seleccionados_id.length; x++)
                {
                    const ban = this.seguimiento.subcategoria_departamento.find( departamento => departamento.id_departamento == this.seguimiento.departamentos_seleccionados_id[x]);
                    if(ban === undefined)
                    {
                        banDpto = true;
                    }   
                    else
                        arr.push(this.seguimiento.departamentos_seleccionados_id[x]);                 
                }
                if(banDpto == true)
                {
                    this.seguimiento.departamentos_seleccionados_id = arr;
                    this.updateDepartamento();
                }

            }
        },

        getDptos: function(){
            axios.post('../sub_dptos',{
                id_subcategoria: this.seguimiento.subcategoria.id
            }).then(response=>{
                console.log(response);
                this.seguimiento.subcategoria_departamento = response.data;         
            }).catch(function (error) {
                console.log(error);
            });
        }
    }
});
