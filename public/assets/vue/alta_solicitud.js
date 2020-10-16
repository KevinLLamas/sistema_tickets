new Vue({
    el: '#alta_solicitud',
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
       
    },
    created: function(){
        this.getPerfiles();
    },
    methods: {
        getPerfiles: function(){
            axios.get('perfiles').then(result =>{
                console.log(result.data);
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
                console.log(result.data);
                this.Categorias = result.data;
            }).catch(error=>{
                console.log(error);
            })
        },
        getSubcategorias: function(){
            this.subcategoria = '';
            this.Campos = [];
            axios.get('subcategorias?id_categoria='+this.solicitud.categoria).then(result =>{
                console.log(result.data);
                this.Subcategorias = result.data;
            }).catch(error=>{
                console.log(error);
            });
        },
        getCampos: function(){
            this.Campos = [];
            axios.get(`getCampos?id_perfil=${this.solicitud.perfil}&id_subcategoria=${this.solicitud.subcategoria}`)
            .then(result =>{
                console.log(result.data);
                this.Campos = result.data;
            }).catch(error=>{
                console.log(error);
            });
        },
        buscar: function(curp)
        {
            if(curp != undefined )
            {
                axios.post('buscar_usuario',{
                    perfil: this.solicitud.perfil,
                    curp: curp,
                }).then(result=>{
                    console.log(result);
                    if(result.data.status)
                    {
                        if(result.data.data.ok)
                        {
                            let persona = result.data.data.result[0];
                            Swal.fire({
                                title: 'Encontramos información de la cuenta, ¿deseas utilizarla?',
                                text: `Nombre: ${persona.nombre} Usuario: ${persona.usuario} CURP: ${persona.curp}`,
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#E9004C',
                                confirmButtonText: 'Sí, utilizar mi infomación',
                                cancelButtonText: "No",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire(
                                        'Correcto',
                                        'Utilizaremos tu información',
                                        'success'
                                    )
                                }
                            })
                        }
                    }
                }).catch(error=>{
                    console.log(error);
                })
            }
        },
        guardar: function()
        {
            if(this.solicitud.necesita_respuesta)
                this.solicitud.necesita = 'true';
            else
                this.solicitud.necesita = 'false';
            axios.post('guardar_solicitud',{
                solicitud: this.solicitud,
                datos: this.Campos,
            }).then(result=>{
                console.log(result);
                if(result.data.status){
                    this.solicitud.id = result.data.id_solicitud;
                    this.solicitud.id_atencion = result.data.id_atencion;
                    Swal.fire('Correcto','Solicitud guardada correctamente','success');
                    this.saveFiles();
                }
                else
                    Swal.fire('Incorrecto','Hay un error con los datos','warning');
            }).catch(error=>{
                console.log(error);
            })
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
                axios.post('save_files', formData, config)
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
    },
});