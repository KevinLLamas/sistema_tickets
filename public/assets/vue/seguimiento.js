var url = window.location.pathname;
var getId = url.substring(url.lastIndexOf('/')+1);

new Vue({
    el: '#app',
    data:{
        var: '',
        seguimiento: {
            subategoria:
            {
                nombre: '',
            }
        },
        nueva_atencion:{
            detalle: '',
            id_solicitud: '',
            id_usuario: '',
            tipo_at: '',
            tipo_respuesta: '',
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
        departamentos: [],
    },
    created: function(){
        this.muestra();
        this.getUserData();
    },
    methods:{
        muestra: function(){
            axios.get(`../getSolicitud/`+getId).then(response=>{
                //console.log(response.data);
                this.seguimiento = response.data;
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        muestra2: function(){
            axios.get(`../getSolicitud/`+this.id).then(response=>{
                this.seguimiento = response.data;
                this.getUserData();
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        agregarAtencion: function(tipo, accion){
            let ban = false;
            if(this.nueva_atencion.detalle)
            {
<<<<<<< HEAD
                this.nueva_atencion.id_usuario = this.user.id;
=======
                this.nueva_atencion.id_usuario= this.user.id;
>>>>>>> e7a4463f8759faa04af960403fde27fc7206423c
                this.nueva_atencion.tipo_respuesta = tipo;
                this.nueva_atencion.id_solicitud = this.seguimiento.id_solicitud;
                //console.log(this.nueva_atencion);
                axios.post('../inserta_atencion',{
                    data: this.nueva_atencion,
                    codigo: this.codigo,
                    email: this.seguimiento.usuario.correo,
                    rol: this.user.rol,
                }).then(result=>{
                    console.log(result);
                    this.id_atencion = result.data;
                    if(this.id === '')
                        this.muestra();
                    else
                        this.muestra2();
                    if(accion != '')
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Listo',
                        showConfirmButton: false,
                        timer: 1000
                    })
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
                 Swal.fire({
                     icon: 'success',
                     title: 'Se cambio el estatus',
                     showConfirmButton: false,
                     timer: 1000
                 })                 
                this.nueva_atencion.detalle= 'Cambio de estatus a ' + this.seguimiento.estatus;
                this.nueva_atencion.id_usuario= this.user.id;
                this.nueva_atencion.tipo_at= 'Estatus';
                this.agregarAtencion('Todos', '');
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
                        Swal.fire('Atención','sí','success');
                        if(this.id === '')
                            this.muestra();
                        else
                            this.muestra2();
                        this.files = {};
                        $("#label_formato").text('Selecciona Archivos');
                    }
                    else{
                        swal.close();
                        Swal.fire('Atención','no','warning');
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
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        getUserData: function(){
             //console.log("User data");
            axios.get('../getUserData').then(response=>{
                //console.log(response.data);
                this.user = response.data;   
                this.validatePermission();             
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        validatePermission: function()
        {
            for(var i = 0; i < this.seguimiento.departamento.length; i++)
            {
                console.log(this.seguimiento.departamento);
                if(this.seguimiento.departamento[i].id == this.user.id_departamento)
                    this.departamentoValido = this.seguimiento.departamento[i];
            }
            var c = 0;
            for(var i = 0; i < this.departamentoValido.integrantes.length; i++)
            {
                for(var x = 0; x < this.seguimiento.solicitud_usuario.length; x++)
                {
                    //console.log(this.departamentoValido.integrantes[i].id);
                    if(this.departamentoValido.integrantes[i].id == this.seguimiento.solicitud_usuario[x].id_usuario && this.seguimiento.solicitud_usuario[x].estado === "Atendiendo")
                    { 
                        this.integrantesSeleccionados[c] = this.departamentoValido.integrantes[i].id;
                        this.integrantesSeleccionadosCompleto[c] = this.departamentoValido.integrantes[i];
                        c++;
                    }
                }                    
            }  
            if(this.seguimiento.estatus === 'Cerrada (En espera de aprobación)')
            {
                Swal.fire('Espera de aprobación','La solicitud se ha marcado como cerrada, revisela y apruebe este estado, o cancele para volver a abrirla','info');
            }  
            this.getDepartamentos();   
        },
        updateIntegrantes: function()
        {
            axios.post('../UpdateSolicitud_usuario',{
                integrantes: this.integrantesSeleccionados,
                id_solicitud: this.seguimiento.id_solicitud,
            }).then(response=>{
                this.muestra();
                var c = 0;  
                /*this.nueva_atencion.detalle = 'Cambio en asignación';
                this.nueva_atencion.id_usuario= this.user.id;
                this.nueva_atencion.tipo_at= 'Asignación';
                this.agregarAtencion('Todos', '');
                this.nueva_atencion.estatus = "";*/
                //console.log(response);
            }).catch(function (error) {
                //console.log(error);
            });
        },

        getDepartamentos: function()
        {
            axios.get('../getDepartamentos').then(response=>{
                //console.log(response.data);
                this.departamentos = response.data;            
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        }
    }
});