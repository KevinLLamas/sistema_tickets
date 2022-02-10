new Vue({
    el: '#mis_solicitudes_asignadas',
    data:{
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],
        orden:'DESC',
        estado_ticket:'',
        ocultarTabla:false,
        ocultarListaSolicitudes:false,
        ocultarGrafica:false,
        MisSolicitudes:[],
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
       this.getSolicitudesAsignadas();
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
        getSolicitudesAsignadas: function(page){
            var url = 'get_solicitudes_asignadas';
            axios.post(url,{
                page: page,
                busqueda: this.busqueda,
                num: this.numFiltro,
                medio: this.medioReporte,
                estado: this.estadoReporte,
                id: this.busquedaid,
                orden: this.orden,
            })
            .then(response => {
                this.pagination=response.data;
                this.MisSolicitudes=response.data.data;
            });
        },
        siguientePagina: function(page){
            this.pagination.current_page = page;
            this.getSolicitudesAsignadas(page);
        },
    }
});