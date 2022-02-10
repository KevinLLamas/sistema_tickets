new Vue({
    el: '#solicitudes_departamento',
    data:{
        tickets_seleccionados:[],
        asignacion_multiple:false,
        listaUsuarios:[],
        usuarioSeleccionado:'',
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],
        orden:'DESC',
        estado_ticket:'',
        ocultarListaSolicitudes:false,
        ocultarGrafica:false,
        Solicitudes:[],
        medioReporte:'',
        estadoReporte:'',
        numFiltro: '10',
        busqueda: '',
        busquedaid:'',
        pagination: {
            'total'         : 0,
            'current_page'  : 0,
            'per_page'      : 0,
            'last_page'     : 0,
            'from'          : 0,
            'to'            : 0
        },
        errors: [],
        offset: 3
    },
    created: function(){
        this.getSolicitudesDepartamento();
    },
    mounted: async function(){
    },
    computed:{
        isActived: function(){
            return this.pagination.current_page;
        },
        pagesNumber: function(){
            if(!this.pagination.to){
                return [];
            }
            var from = this.pagination.current_page - this.offset;
            if(from < 1){
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.pagination.last_page){
                to = this.pagination.last_page;
            }

            var pagesArray = [];
            while(from <= to){
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    methods:{
        getSolicitudesDepartamento: function(page){
            var url = 'get_solicitudes_departamento';
            axios.post(url,{
                page: page,
                busqueda: this.busqueda,
                num: this.numFiltro,
                medio: this.medioReporte,
                estado: this.estadoReporte,
                id_solicitud: this.busquedaid,
                orden: this.orden,
            })
            .then(response => {
                this.pagination=response.data;
                this.Solicitudes=response.data.data;
            });
        },
        siguientePagina: function(page){
            this.pagination.current_page = page;
            this.getSolicitudesDepartamento(page);
        },
        asignarSolicitudes:function(){
            if(this.tickets_seleccionados.length>0){
                Swal.fire({
                    title: 'Quieres continuar?',
                    text: `Se asignaran ${this.tickets_seleccionados.length} tickets`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si'
                  }).then((result) => {
                    if (result.isConfirmed) {
                        url="asignar_solicitudes";
                        axios.post(url,{
                            tickets_seleccionados:this.tickets_seleccionados,
                            usuarioSeleccionado:this.usuarioSeleccionado,
                        })
                        .then(response => {
                            this.asignacion_multiple=false;
                            this.tickets_seleccionados=[];
                            if(response.data.status){
                                this.getSolicitudesDepartamento();
                                Swal.fire(
                                    'Tickets Asignados',
                                    'Solicitudes asignadas con exito',
                                    'success'
                                )
                                
                            }
                            else{
                                Swal.fire(
                                    'Error al asignar',
                                    'intentelo mas tarde',
                                    'error'
                                )
                            }
                        });

                    }
                  })
                
            }
            else{
                Swal.fire(
                    'No hay tickets seleccionados',
                    'Seleccione almenos un ticket y vuelva a intentarlo',
                    'warning'
                  )
            }
            

        }
    }
});