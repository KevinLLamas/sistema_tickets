//Declaracion de variables para las graficas de manera global
var comparacionChart=null;
var ctx = document.getElementById("ComparacionSolicitudesChart");
comparacionChart = new Chart(ctx, {
    type: 'line',
    data: {},
    options: {}
});

new Vue({
    el: '#reportes',
    data:{
        
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],
        numCerradas: '',
        numAtendiendo: '',
        numSinAtender: '',
        porcentajeCerrados: '',
        rangoTiempo:'INTERVAL 7 DAY',
        EstatusbyTime:[],
        EstatusbyTimeCerradas:[],
    },
    created: function(){      
        
        this.getInfoOfTickets();
        this.generar_Grafica_Comparacion();
    },
    mounted: async function(){
        this.generar_Grafica_ByTime()
        
        
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
        getNumSolicitudesThroughTime:async function(){
            url="get_num_solicitudes_through_time";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
            })
            .then(response=>{
                //console.log(response.data);
                this.EstatusbyTime=response.data;
            })
        },
        getNumSolicitudesThroughTimeCerradas:async function(){
            url="get_num_solicitudes_through_time_cerradas";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
            })
            .then(response=>{
                //console.log(response.data);
                this.EstatusbyTimeCerradas=response.data;
            })
        },
        generar_Grafica_ByStatus:function(){
            
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#858796';
            //console.log("colores para grafica",this.coloresHex);
            // Pie Chart Example
            var ctx = document.getElementById("SolicitudesUsuarioChart");
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
        generar_Grafica_Comparacion:async function(){
            //console.log("Tickets en total");
            await this.getNumSolicitudesThroughTime();
            await this.getNumSolicitudesThroughTimeCerradas();
            //console.log(this.EstatusbyTime);
            switch(this.rangoTiempo){
                case 'INTERVAL 1 DAY':
                        this.LastDays(2).forEach(d => {

                            console.log(d.toString());
                            this.addLabelChart(comparacionChart,d.toString());
                            
                        });
                        this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChart,s.total,s.fecha,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.fecha,0);
                        });
                    break;
                case 'INTERVAL 7 DAY':
                        
                        this.LastDays(7).forEach(d => {

                            console.log(d.toString());
                            this.addLabelChart(comparacionChart,d.toString());
                            
                            
                        });
                        this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChart,s.total,s.fecha,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.fecha,0);
                        });
                    break;
                case 'INTERVAL 1 MONTH':
                        
                        this.LastMonths(2).forEach(m => {
                            console.log(m);
                            this.addLabelChart(comparacionChart,m.toString());
                        });
                        this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChart,s.total,s.mes,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.fecha,0);
                        });
                    break;
                case 'INTERVAL 3 MONTH':
                        this.LastMonths(3).forEach(m => {
                            console.log(m);
                            this.addLabelChart(comparacionChart,m.toString());
                        });
                        this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChart,s.total,s.mes,1);
                        });
                    break;
            }
            
            /*console.log("Tickets cerrados");
            await this.getNumSolicitudesThroughTime('Cerrada');
            this.EstatusbyTime.forEach(s => {
                this.addDataChart(comparacionChart,s.fecha,s.total,0);
            });
            console.log(this.EstatusbyTime);*/
            
        },
        addLabelChart:function(chart,label){
            chart.data.labels.push(label);
        },
        addDataChart:function(chart, data,label, dataset) {
            console.log("ingresando dato en fecha")
            console.log(label);
            posicion=chart.data.labels.findIndex((f) => f == label);
            chart.data.datasets[dataset].data[posicion]=data;   
            console.log("posicion: "+ posicion);
            //chart.data.datasets[dataset].data.push(data);
            chart.update();
        },
        LastDays:function(days) {
            var result = [];
            for (var i=days-1; i>=0; i--) {
                var d = new Date();
                d.setDate(d.getDate() - i);
                result.push( this.formatDate(d) );
            }
        
            return(result);
        },
        formatDate:function(date){
            //console.log(`fecha a convertir ${date}`);
            let day = date.getDate()
            let month = date.getMonth() + 1
            let year = date.getFullYear()
            //console.log(`dia ${day}`);
            //console.log(`mes ${month}`);
            //console.log(`anio ${year}`);
            if(month < 10){
                return `${day}-0${month}-${year}`;
            }else{
                return `${day}-${month}-${year}`;
            }
        },
        LastMonths:function(months) {
            var monthNames = ["enero", "febrero", "marzo", "abril", "mayo", "junio",
                "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
            ];
            var d = new Date();
            var result = []

            for (i = months-1; i >= 0; i--) {
                result.push(monthNames[(d.getMonth() - i)] + ' - ' +d.getFullYear()  );
            }
            return result;
        },
        formatMonth:function(date){
            var monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
            return monthNames[(date.getMonth() - i)] + ' - ' +date.getFullYear();
        },
        generar_Grafica_ByTime:function(){
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#858796';

            function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
            }

            // Area Chart Example
            comparacionChart.destroy();
            var ctx = document.getElementById("ComparacionSolicitudesChart");
            comparacionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "Tickets Resueltos",
                    yAxesID:"Tickets Resueltos",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#28a745",
                    borderColor: "#28a745",
                    pointRadius: 3,
                    pointBackgroundColor: "#28a745",
                    pointBorderColor: "#28a745",
                    pointHoverRadius: 5,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                    
                },{
                    label: "Tickets Pendientes",
                    yAxesID:"Tickets Pendientes",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#E9004C",
                    pointRadius: 3,
                    pointBackgroundColor: "#E9004C",
                    pointBorderColor: "#E9004C",
                    pointHoverRadius: 5,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        id:"Tickets Resueltas",
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },{
                        id:"Tickets Pendientes",
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ' : ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
            });
            

        },
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
                //console.log(response.data);
                this.pagination=response.data;
                this.Solicitudes=response.data.data;
            });
        },
        getInfoOfTickets: function()
        {
            this.getSolicitudesDeptoCerradas();
            this.getSolicitudesDeptoAtendiendo();
            this.getSolicitudesDeptoSinAtender();            
        },
        getSolicitudesDeptoCerradas: function(){
            var url = 'get_solicitudes_departamento';
            axios.post(url,{
                estado: 'Cerrada',
                orden: 'ASC',
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numCerradas = response.data.data.length;
            });
        },
        getSolicitudesDeptoAtendiendo: function(){
            var url = 'get_solicitudes_departamento';
            axios.post(url,{
                estado: 'Atendiendo',
                orden: 'ASC',
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numAtendiendo = response.data.data.length;
            });
        },
        getSolicitudesDeptoSinAtender: function(){
            var url = 'get_solicitudes_departamento';
            axios.post(url,{
                estado: 'Sin Atender',
                orden: 'ASC',
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numSinAtender = response.data.data.length;
                this.porcentajeCerrados = ((this.numCerradas/(this.numAtendiendo + this.numSinAtender + this.numCerradas)) * 100).toFixed(2);
            });
        },
    }
});