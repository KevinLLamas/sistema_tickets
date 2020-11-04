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
        integrantesSeleccionados: [],
        integrantesSeleccionadosSolicitud: [],
        integrantesSeleccionadosCompletoSolicitud: []
    },
    created: function(){
        this.muestra2();        
    },
    methods:{
        muestra2: function(){
            axios.get(`../getSolicitud/`+this.id).then(response=>{
                this.seguimiento = response.data;                
                this.validatePermissionExterno();
                this.compruebaCerradoAuto();
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
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
        }
    }
});
