new Vue({
    el: '#notificaciones',
    data:{
        notificaciones: [],
        cont: 0,
    },
    created: function(){
       this.getNotificaciones();
       //this.getNumSolicitudesByStatusMisSolicitudes();
    },
    methods:{
        getNotificaciones: function(){
            axios.post('/get_notificaciones')
            .then(response => {
                //console.log(response.data);
                this.notificaciones = response.data.notificaciones;
                this.cont = response.data.cont;
            });
        },
        esLeida: function(status){
            return status === 'Leida' ? 'bg-white' : 'bg-light';
        },
        verSolicitud: function(id,id_solicitud)
        {
            console.log(id);
            axios.post('/set_notificacion_leida',{id: id})
            .then(response => {
                console.log(response.data);
                window.location = '/seguimiento/'+id_solicitud;
            });
        }
    }
});
