new Vue({
    el: '#editar_solicitud',
    data: {
        Perfiles: [],
        Categorias: [],
        Subcategorias: [],
        Campos: [],
        files: {},
        solicitud:{
            categoria: '',
            subcategoria: '',
            perfil: '',
            descripcion:'',
            necesita_respuesta: false,
            correo_contacto:'',
        },
        show_inputs: false,
    },
    created: function(){
        this.getPerfiles();
    },
    methods: {
        getPerfiles: function(){
            axios.get('perfiles').then(result =>{
                this.Perfiles = result.data;
            }).catch(error=>{
                console.log(error);
            })
        },
        getCategorias: function(){
            this.categoria = '';
            this.subcategoria = '';
            this.Campos = [];
            axios.get('categorias?id_perfil='+this.solicitud.perfil)
            .then(result =>{
                this.Categorias = result.data;
            }).catch(error=>{
                console.log(error);
            })
        },
        getSubcategorias: function(){
            this.subcategoria = '';
            this.Campos = [];
            axios.get('subcategorias?id_categoria='+this.solicitud.categoria).then(result =>{
                this.Subcategorias = result.data;
            }).catch(error=>{
                console.log(error);
            });
        },
        mostrar_inputs: function(){
            this.show_inputs = true;
        },
        guardar: function()
        {
            if(this.solicitud.necesita_respuesta)
                this.solicitud.necesita = 'true';
            else
                this.solicitud.necesita = 'false';
            axios.post('editar_solicitud',{
                solicitud: this.solicitud,
                datos: this.Campos,
            }).then(result=>{
                if(result.data.status){
                    this.solicitud.id = result.data.id_solicitud;
                    this.solicitud.id_atencion = result.data.id_atencion;
                    Swal.fire('Correcto','Ticket editado correctamente','success');
                    this.saveFiles();
                }
                else
                    Swal.fire('Incorrecto',result.data.message,'warning');
            }).catch(error=>{
                console.log(error);
            })
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
        saveFiles: function(){  
            if(this.files.length != undefined){
                swal.fire({ title: "Subiendo Archivos...", imageUrl: "assets/images/loading-79.gif", imageWidth: 250, imageHeight: 250, showConfirmButton: false,});
                const config = {headers: { 'content-type': 'multipart/form-data' }};
                let formData = new FormData();
                formData.set('id_solicitud', this.solicitud.id);
                formData.set('correo', this.solicitud.correo_contacto);
                formData.set('id_atencion', this.solicitud.id_atencion);
                
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
                axios.post('edit_files', formData, config)
                .then(response => {
                    if(response.data.status){
                        swal.close();
                        Swal.fire('Ticket creado','Tu ID de ticket es: '+this.solicitud.id,'success');
                    }
                    else{
                        swal.close();
                        Swal.fire('Ticket creado','No se pudieron subir los archivos, Tu ID de ticket es: '+this.solicitud.id,'warning');
                    }
                }).catch(error=>{
                    console.log(error);
                })
            }
            else{
                Swal.fire('Ticket creado','Tu ID de ticket es: '+this.solicitud.id,'success');
            }
        },
    },
});