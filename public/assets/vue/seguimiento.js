
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
                console.log(this.$route.params.id);
            }).catch(function (error) {
                //console.log(error);
            });
        },
        agregarAtencion: function(tipo){
            this.nueva_atencion.tipo_respuesta = tipo;
            this.nueva_atencion.id_solicitud = this.seguimiento.id_solicitud;
            //console.log(this.nueva_atencion);
            axios.post('../inserta_atencion',{
                data: this.nueva_atencion
             }).then(result=>{
                 this.id_atencion = result.data;
                 console.log(result.data);
                 this.muestra();
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
        },
        cambiarEstatus: function(){
            axios.post('../cambiar_estatus',{
                id: this.seguimiento.id_solicitud,
                estatus: this.seguimiento.estatus
             }).then(result=>{                
                 console.log(result);
                 //this.getSesiones();
                 Swal.fire({
                     icon: 'success',
                     title: 'Se cambio el estatus',
                     showConfirmButton: false,
                     timer: 1000
                 })                 
             }).catch(error=>{
                 console.log(error);
             });
        },
        fileChangeFormato(e){
            console.log("Hola");
            if(e.target.files.length > 1){
                console.log(e.target.files.length);
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
            console.log("Hola");
            if(e.target.files.length > 1){
                console.log(e.target.files.length);
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
                     console.log(response.data);
                     if(response.data.status){
                         swal.close();
                         Swal.fire('Atención','sí','success');
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
                 Swal.fire('Atención','No tienes archivos por guardar','warning');
             }
         },
    }
});