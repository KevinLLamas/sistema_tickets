new Vue({
    el: '#notificaciones',
    data:{
        notificaciones: [],
        cont: 0,
        id_ticket: '',
        tickets: [],
        banIr: false
    },
    created: function(){
       this.getNotificaciones();
       //this.getNumSolicitudesByStatusMisSolicitudes();
    },
    methods:{
        getNotificaciones: function(){
            axios.post('/sass/get_notificaciones')
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
            axios.post('/sass/set_notificacion_leida',{id: id})
            .then(response => {
                console.log(response.data);
                window.location = '/sass/seguimiento/'+id_solicitud;
            });
        },
        ir_solicitud: function()
        {
            window.location = '/sass/seguimiento/'+this.id_ticket;
        },
        buscarTiket: function()
        {
            if(this.id_ticket.length > 2)
            {
                axios.post('/sass/get_ticket',
                {id: this.id_ticket})
                .then(response => {
                    console.log(response.data);
                    this.tickets = response.data;
                    if(this.tickets.length > 0){
                        const busq = this.tickets.find( ticket => ticket.id_solicitud == parseInt(this.id_ticket));
                        if(busq === undefined){
                            this.banIr = false;
                        }else
                        {
                            this.banIr = true;
                        }
                    }else{
                        this.banIr = false;
                    }
                });
            }
        }
    }
});
