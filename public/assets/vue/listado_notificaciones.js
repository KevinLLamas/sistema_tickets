new Vue({
    el: '#notificaciones_listado',
    data:{
        notificaciones: [],
        ocultarLista:false,
        Solicitudes:[],
        medioReporte:'',
        estadoReporte:'',
        numFiltro: '20',
        busqueda: '',
        busquedaid:'',
        ver: '',
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
    created: function(){
       this.getNotificaciones();
       //this.getNumSolicitudesByStatusMisSolicitudes();
    },
    methods:{
        getNotificaciones: function(page){
            axios.post('get_listado_notificaciones',
            {
                busquedaid: this.busquedaid,
                leidas: this.ver,
            })
            .then(response => {
                this.notificaciones = response.data.notificaciones;
            });
        },
        esLeida: function(status){
            return status === 'Leida' ? 'bg-white' : 'bg-light';
        },
        verSolicitud: function(id,id_solicitud)
        {
            axios.post('/set_notificacion_leida',{id: id})
            .then(response => {
                window.location = '/seguimiento/'+id_solicitud;
            });
        },
        tipo: function(tipo){
            if(tipo == 'Atencion')
                return 'border-bottom-primary ';
            else if(tipo == 'Asignacion')
                return 'border-bottom-success ';
            else if(tipo == 'Estatus')
                return 'border-bottom-info ';  
            else 
                return 'border-bottom-secondary ';
        },
    },
   
    siguientePagina: function(page){
        this.pagination.current_page = page;
        this.getNotificaciones(page);
    },
});