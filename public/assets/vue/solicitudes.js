new Vue({
    el: '#solicitudes',
    data:{
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],

        estado_ticket:'',
        ocultarListaSolicitudes:false,
        Solicitudes:[],
        medioReporte:'',
        estadoReporte:'',
        numFiltro: '10',
        busqueda: '',
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
       this.getSolicitudes();
       //this.getNumSolicitudesByStatus();
    },
    mounted: async function(){
        await this.getNumSolicitudesByStatus();
        //console.log("ok",this.Estatus);
        this.tipoEstatus=await this.Estatus.map(s=>s.estatus);
        this.numEstatus=await this.Estatus.map(n=>n.total);
        
        await this.Estatus.forEach(e => {
            this.colorEstatus.push(this.asignarColor(e.estatus));
            this.coloresHex.push(this.asignarColorHex(e.estatus))
        });
        //this.colorEstatus=await this.Estatus.map(c=>c.color);
        //await console.log("ök",colorEstatus);
        this.generar_Grafica_ByStatus();
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
        asignarColor:function(tipo){
            if (tipo == 'Sin atender') {
                return 'text-danger'
            } 
            else if (tipo == 'Atendiendo') {
                return 'text-warning'
            } 
            else if (tipo == 'Suspendida') {
                return 'text-dark'
            } 
            else if (tipo == 'Cancelada') {
                return 'text-info'
            } 
            else if (tipo == 'Cerrada') {
                return 'text-success'
 
            }
        },
        asignarColorHex:function(tipo){
            if (tipo == 'Sin atender') {
                return '#d9534f'
            } 
            else if (tipo == 'Atendiendo') {
                return '#f0ad4e'
            } 
            else if (tipo == 'Suspendida') {
                return '#343a40'
            } 
            else if (tipo == 'Cancelada') {
                return '#5bc0de'
            } 
            else if (tipo == 'Cerrada') {
                return '#5cb85c'
 
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
        getNumSolicitudesByStatus:async function(){
            url="get_Num_Solicitudes_ByStatus_General";
            data= await axios.get(url)
            .then(response=>{
                //console.log(response.data);
                
                this.Estatus= response.data;
            })
            
            
        },
        generar_Grafica_ByStatus:function(){
            
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#858796';
            console.log("colores para grafica",this.coloresHex);
            // Pie Chart Example
            var ctx = document.getElementById("myPieChart");
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
        
        getSolicitudes: function(page){
            var url = 'get_Solicitudes';
            axios.post(url,{
                page: page,
                busqueda: this.busqueda,
                num: this.numFiltro,
                medio: this.medioReporte,
                estado: this.estadoReporte,
            })
            .then(response => {
                console.log(response.data);
                this.pagination=response.data;
                this.Solicitudes=response.data.data;
            });
        },
        siguientePagina: function(page){
            this.pagination.current_page = page;
            this.getSolicitudes(page);
        },
    }
});