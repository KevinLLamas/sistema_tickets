Chart.defaults.global.defaultFontFamily = 'Montserrat';
Chart.defaults.global.defaultFontColor = '#858796';
new Vue({
    el: '#solicitudes',
    data:{
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],
        orden:'ASC',
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
       this.getSolicitudesAdmin();
       //this.getNumSolicitudesByStatus();
    },
    mounted: async function(){
        await this.generarGraficaAdmin();
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
        generarGraficaAdmin:async function(){
            await this.getNumSolicitudesByStatusAdmin();
            this.tipoEstatus=await this.Estatus.map(s=>s.estatus);
            this.numEstatus=await this.Estatus.map(n=>n.total);
            await this.Estatus.forEach(e => {
                this.colorEstatus.push(this.asignarColor(e.estatus));
                this.coloresHex.push(this.asignarColorHex(e.estatus))
            });
            this.generar_Grafica_ByStatus();
        },
        asignarColor:function(tipo){
            if (tipo == 'Sin atender') {
                return 'text-primary'
            } 
            else if (tipo == 'Atendiendo') {
                return 'text-info'
            } 
            else if (tipo == 'Suspendida') {
                return 'text-secundary'
            } 
            else if (tipo == 'Cancelada') {
                return 'text-warning'
            } 
            else if (tipo == 'Cerrada') {
                return 'text-success'
            }
            else if (tipo == 'Cerrada (En espera de aprobación)') {
                return 'text-light'
            }
        },
        asignarColorHex:function(tipo){
            if (tipo == 'Sin atender') {
                return '#E9004C'
            } 
            else if (tipo == 'Atendiendo') {
                return '#007bff'
            } 
            else if (tipo == 'Suspendida') {
                return '#6c757d'
            } 
            else if (tipo == 'Cancelada') {
                return '#ffc107'
            } 
            else if (tipo == 'Cerrada') {
                return '#28a745'
            }
            else if (tipo == 'Cerrada (En espera de aprobación)') {
                return '#CDCDCD'
            }
        },
        getRandomColor:function(){
            var letters = '0123456789ABCDEF'.split('');
            var color = '#';
            for (var i = 0; i < 6; i++ ) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        },
        getNumSolicitudesByStatusAdmin:async function(){
            url="get_num_solicitudes_bystatus_admin";
            data= await axios.get(url)
            .then(response=>{
                //console.log(response.data);
                
                this.Estatus= response.data;
            })
            
            
        },
        generar_Grafica_ByStatus:function(){
            // Pie Chart Example
            var ctx = document.getElementById("SolicitudesAdminChart");
            var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: this.tipoEstatus,
                datasets: [{
                    data: this.numEstatus,
                    backgroundColor: this.coloresHex,
                    hoverBackgroundColor: this.coloresHex,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                },
                legend: {
                display: false
                },
                cutoutPercentage: 80,
            },
            });

        },
        
        getSolicitudesAdmin: function(page){
            var url = 'get_solicitudes_admin';
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
                //console.log(response.data);
                this.pagination=response.data;
                this.Solicitudes=response.data.data;
            });
        },
        siguientePagina: function(page){
            this.pagination.current_page = page;
            this.getSolicitudesAdmin(page);
        },
    }
});