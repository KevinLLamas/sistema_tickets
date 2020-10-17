
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
            id_usuario: 1,
            tipo_at: '',
            tipo_respuesta: '',
        },
        files: {},
        id_atencion: '',
        codigo: '',
        banVerif: false,
        id: '',
    },
    created: function(){
        this.muestra();
    },
    methods:{
        muestra: function(){
            axios.get(`/getSolicitud/`+getId).then(response=>{
                //console.log(response.data);
                this.seguimiento = response.data;
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        muestra2: function(){
            axios.get(`/getSolicitud/`+this.id).then(response=>{
                this.seguimiento = response.data;
                //console.log(this.seguimiento.correo_atencion);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        agregarAtencion: function(tipo){
            if(this.nueva_atencion.detalle)
            {
                this.nueva_atencion.tipo_respuesta = tipo;
                this.nueva_atencion.id_solicitud = this.seguimiento.id_solicitud;
                //console.log(this.nueva_atencion);
                axios.post('../inserta_atencion',{
                    data: this.nueva_atencion
                }).then(result=>{
                    this.id_atencion = result.data;
                    if(this.id === '')
                        this.muestra();
                    else
                        this.muestra2();
                    Swal.fire({
                        icon: 'success',
                        title: 'Agregada',
                        showConfirmButton: false,
                        timer: 1000
                    })
                    this.saveFiles();
                    this.nueva_atencion.detalle = '';
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
                 Swal.fire({
                     icon: 'success',
                     title: 'Se cambio el estatus',
                     showConfirmButton: false,
                     timer: 1000
                 })                 
                this.nueva_atencion.detalle= 'Cambio de estatus a ' + this.seguimiento.estatus;
                this.nueva_atencion.id_usuario= 1;
                this.nueva_atencion.tipo_at= 'Estatus';
                this.agregarAtencion('Todos');
             }).catch(error=>{
                console.log(error);
             });
        },
        fileChangeFormato(e){
            if(e.target.files.length > 1){
                 if(e.target.files.length < 5){
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
                 this.files = e.target.files;
                 $("#label_formato").text(e.target.files[0].name);
                 
            }
         },
         fileChangeFormatoNotes(e){
            if(e.target.files.length > 1){
                 if(e.target.files.length < 5){
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
                        Swal.fire('Atención','sí','success');
                        if(this.id === '')
                            this.muestra();
                        else
                            this.muestra2();
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
        test: function()
        {
            return "Hola";
        }
    }
});