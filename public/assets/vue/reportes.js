//Declaracion de variables para las graficas de manera global
var comparacionChart=null;
var comparacionChartDep=null;
var solicitudesUsuarioChart=null;
var solicitudesSubcategoriaChart=null;
var ctx = document.getElementById("ComparacionSolicitudesChart");
var ctxdep = document.getElementById("ComparacionSolicitudesChartDep");
comparacionChart = new Chart(ctx,{});
comparacionChartDep = new Chart(ctxdep,{});
var pastelUser = document.getElementById("solicitudesUsuarioChart");
var pastelSubc = document.getElementById("solicitudesSubcategoriaChart");
solicitudesUsuarioChart = new Chart(pastelUser,{});
solicitudesSubcategoriaChart = new Chart(pastelSubc,{});
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Montserrat';
Chart.defaults.global.defaultFontColor = '#858796';

new Vue({
    el: '#reportes',
    data:{
        listaDepartamentos:[],
        departamentoSeleccionado:'',
        myDepartamento:'',
        rolUsuario:'',

        EstatusTodos:[],
        EstatusTime:[],

        listaSubcategorias:[],
        subcategoriaSeleccionada:'',
        numReportesSubc:[],
        tipoEstatusSubc:[],
        EstatusSubc:[],
        colorEstatusSubc:[],
        coloresHexSubc:[],

        usuarioSeleccionado:'',
        listaUsuarios:[],
        numReportes:[],
        tipoEstatus:[],
        Estatus:[],
        colorEstatus:[],
        coloresHex:[],
        numCerradas: '',
        numAtendiendo: '',
        numSinAtender: '',
        porcentajeCerrados: '',
        numEspera: '',
        rangoTiempo:'INTERVAL 1 DAY',
        EstatusbyTime:[],
        EstatusbyTimeCerradas:[],
    },
    created: async function(){   


        
        //this.getNumSolicitudesByEstatusSubcategoria();
        swal.fire({
            title: "Cargando...",
            imageUrl: "assets/images/loading-79.gif",
            imageWidth: 250,
            imageHeight: 250,
            showConfirmButton: false,
            
        });
        this.rolUsuario=$("#rol").val();
        await this.getMyDepartamento();
        await this.getUsuariosbyIdDepartamento();
        if(this.rolUsuario=="SUPER"){
            await this.getDepartamentos();   
            await this.generar_Grafica_Comparacion_Dep();
        }
        else{
            
            await this.generar_Grafica_Comparacion();
        }
        await this.generar_Grafica_Estados();
        await this.generar_Grafica_Estados_Subc();
        await this.getInfoOfTickets();
        await this.getSubcategoriasDepartamento();
        await this.getNumSolicitudesByEstatusTodos();
        
        swal.close();
        
        
    },
    mounted: async function(){
        if(this.rolUsuario=="SUPER"){
            this.generar_Grafica_ByTime_Dep();
            
        }
        else{
            
            this.generar_Grafica_ByTime();
            
        }
        this.generar_Grafica_ByStatus();
    },
    methods:{
        recargarTodoDepartamento:async function(){
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });
            this.generar_Grafica_ByTime_Dep();
            this.generar_Grafica_Comparacion_Dep();
            await this.getUsuariosbyIdDepartamento();
            await this.getNumSolicitudesByEstatusTodos();
            this.getInfoOfTickets();
            await this.getSubcategoriasDepartamento();
            swal.close();
        },
        recargarGraficaComparacion: async function(){
            
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });

            this.generar_Grafica_ByTime();
            await this.generar_Grafica_Comparacion();
            //await this.getUsuariosbyIdDepartamento()
            swal.close();


        },
        recargarGraficaComparacionDep: async function(){
            
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });

            this.generar_Grafica_ByTime_Dep();
            await this.generar_Grafica_Comparacion_Dep();
            await this.getUsuariosbyIdDepartamento()
            swal.close();


        },
        recargarGraficaByStatus: async function(){
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });
            this.generar_Grafica_ByStatus();
            await this.generar_Grafica_Estados();
            swal.close();

        },
        recargarListadoUsuariosTodos: async function(){
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });
            await this.getNumSolicitudesByEstatusTodos();
            swal.close();
        },
        recargarGraficaByStatusSubc:async function(){
            swal.fire({
                title: "Cargando...",
                imageUrl: "assets/images/loading-79.gif",
                imageWidth: 250,
                imageHeight: 250,
                showConfirmButton: false,
                
            });
            this.generar_Grafica_ByStatus_Subc();
            await this.generar_Grafica_Estados_Subc();
            swal.close();
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
        getUsuariosbyIdDepartamento:async function(){
            this.listaUsuarios=[];
            url="get_usuarios_by_id_departamento";
            data=await axios.post(url,{
                idDepartamento:this.departamentoSeleccionado,
            })
            .then(response=>{
                //console.log(response.data);
                this.listaUsuarios=response.data;
                this.usuarioSeleccionado = this.listaUsuarios[0].id_sgu;
                //Buscamos los datos de cada usuario una vez la lista esta completa
                this.getNumSolicitudesByEstatusTodos();
                //Y recarga la grafica con el usuario seleccionado
                this.generar_Grafica_ByStatus();
                this.generar_Grafica_Estados();
                
            })
        },
        getNumSolicitudesByEstatusUsuario:async function(){
            try{
                url="get_num_solicitudes_by_estatus_usuario";
                data=await axios.post(url,{
                    idUsuario:this.usuarioSeleccionado,
                })
                .then(response=>{
                    //console.log(response.data);
                    this.Estatus=response.data;
                })
            }catch(e){
                //console.log('usuario invalido');
            }
        },
        getNumSolicitudesByEstatusTodos:async function(){
            try{
                url="get_num_solicitudes_by_estatus_todos";
                data=await axios.post(url,{
                    listaUsuarios:this.listaUsuarios,
                })
                .then(response=>{
                    //console.log(response.data);
                    this.EstatusTodos=response.data;
                })
            }catch(e){
                //console.log('usuario invalido');
            }
        },
        getNumSolicitudesByEstatusSubcategoria:async function(){
            try{
                url="get_num_solicitudes_by_estatus_subcategoria";
                data=await axios.post(url,{
                    idDepartamento:this.departamentoSeleccionado,
                    idSubcategoria:this.subcategoriaSeleccionada,
                })
                .then(response=>{
                    //console.log(response.data);
                    this.EstatusSubc=response.data;
                })
            }catch(e){
                //console.log('usuario invalido');
            }
        },
        getSubcategoriasDepartamento:async function(){
            try{
                url="get_subcategorias_departamento";
                data=await axios.post(url,{
                    idDepartamento:this.departamentoSeleccionado,
                })
                .then(response=>{
                    //console.log(response.data);
                    this.listaSubcategorias=response.data;
                })
            }catch(e){
                //console.log('usuario invalido');
            }
        },
        getMyDepartamento:async function(){
            
            url="get_my_departamento";
            data=await axios.get(url)
            .then(response=>{
                //(response.data);
                //this.myDepartamento=response.data;
                this.departamentoSeleccionado=response.data;
                
                //console.log(response.data);
                
            })
        },
        getDepartamentos:async function(){
            url="get_departamentos";
            data=await axios.get(url)
            .then(response=>{
                //(response.data);
                this.listaDepartamentos=response.data;
                //console.log(response.data);
            })
        },
        getNumSolicitudesThroughTime:async function(){
            url="get_num_solicitudes_through_time";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
            })
            .then(response=>{
                //(response.data);
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
        getNumSolicitudesThroughTimeDep:async function(){
            
            url="get_num_solicitudes_through_time_dep";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
                idDepartamento:this.departamentoSeleccionado
            })
            .then(response=>{
                //(response.data);
                this.EstatusbyTime=response.data;
            })
        },
        getNumSolicitudesThroughTimeCerradasDep:async function(){
            url="get_num_solicitudes_through_time_cerradas_dep";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
                idDepartamento:this.departamentoSeleccionado
            })
            .then(response=>{
                //console.log(response.data);
                this.EstatusbyTimeCerradas=response.data;
            })
        },
        getNumSolicitudesThroughTimeByStatusDep:async function(estado){
            
            url="get_num_solicitudes_through_time_bystatus_dep";
            data=await axios.post(url,{
                rangoTiempo:this.rangoTiempo,
                idDepartamento:this.departamentoSeleccionado,
                estado:estado
            })
            .then(response=>{
                //console.log(response.data);
                this.EstatusTime=response.data;
            })
        },
        generar_Grafica_ByStatus:function(){
            
            // Pie Chart Example
            solicitudesUsuarioChart.destroy();
            var ctx = document.getElementById("solicitudesUsuarioChart");
            solicitudesUsuarioChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: this.coloresHex,
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
        generar_Grafica_ByStatus_Subc:function(){
            
            // Pie Chart Example
            solicitudesSubcategoriaChart.destroy();
            var ctx = document.getElementById("solicitudesSubcategoriaChart");
            solicitudesSubcategoriaChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: this.coloresHexSubc,
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
        generar_Grafica_ByTime:function(){
            

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
                    label: "Cerrados",
                    yAxesID:"Cerrados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#28a745",
                    borderColor: "#28a745",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#28a745",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                    
                },{
                    label: "Sin Atender",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#E9004C",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#E9004C",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                },{
                    label: "Cerrado en Espera",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#CDCDCD",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#CDCDCD",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                },{
                    label: "Atendiendo",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#007bff",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#007bff",
                    pointHoverRadius: 7,
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
        generar_Grafica_ByTime_Dep:function(){
            

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
            comparacionChartDep.destroy();
            var ctx = document.getElementById("ComparacionSolicitudesChartDep");
            comparacionChartDep = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "Cerrados",
                    yAxesID:"Cerrados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#28a745",
                    borderColor: "#28a745",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#28a745",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                    
                },{
                    label: "Sin Atender",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#E9004C",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#E9004C",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                },{
                    label: "Cerrado en Espera",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#CDCDCD",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#CDCDCD",
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [],
                    spanGaps:true
                },{
                    label: "Atendiendo",
                    //yAxesID:"Tickets Creados",
                    fill:false,
                    lineTension: 0.3,
                    //backgroundColor: "#E9004C",
                    borderColor: "#007bff",
                    pointRadius: 5,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#007bff",
                    pointHoverRadius: 7,
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
        generar_Grafica_Estados:async function(){   
            await this.getNumSolicitudesByEstatusUsuario();
            if(typeof this.Estatus !== 'undefined' && this.Estatus.length > 0){
                this.coloresHex=[];
                this.Estatus.forEach(e => {
                    //console.log(`estado: ${e.estatus}  color: ${this.asignarColorHex(e.estatus)}`);
                    this.coloresHex.push(this.asignarColorHex(e.estatus))
                });
                this.generar_Grafica_ByStatus();
                this.Estatus.forEach(e => {
                    //console.log(e.estatus);
                    this.addLabelChart(solicitudesUsuarioChart,e.estatus.toString());
                    this.addDataChartsinOrden(solicitudesUsuarioChart,e.total,0);
                    
                });
            }
            
            
        },
        generar_Grafica_Estados_Subc:async function(){   
            await this.getNumSolicitudesByEstatusSubcategoria();
            if(typeof this.EstatusSubc !== 'undefined' && this.EstatusSubc.length > 0){
                this.coloresHexSubc=[];
                this.EstatusSubc.forEach(e => {
                    //console.log(`estado: ${e.estatus}  color: ${this.asignarColorHex(e.estatus)}`);
                    this.coloresHexSubc.push(this.asignarColorHex(e.estatus))
                });
                this.generar_Grafica_ByStatus_Subc();
                this.EstatusSubc.forEach(e => {
                    //console.log(e.estatus);
                    this.addLabelChart(solicitudesSubcategoriaChart,e.estatus.toString());
                    this.addDataChartsinOrden(solicitudesSubcategoriaChart,e.total,0);
                    
                });
            }
            
            
        },
        generar_Grafica_Comparacion:async function(){
            //console.log("Tickets en total");
            await this.getNumSolicitudesThroughTime();
            let ListCreadas=this.EstatusbyTime;
            //console.log("Creadas:"+ListCreadas);
            //await this.getNumSolicitudesThroughTimeCerradasDep();
            //console.log(this.EstatusbyTime);
            await this.getNumSolicitudesThroughTimeByStatusDep('Sin Atender');
            let ListSinAtender=this.EstatusTime;
            //console.log("Sin Atender:"+ListSinAtender);

            await this.getNumSolicitudesThroughTimeByStatusDep('Atendiendo');
            let ListAtendiendo=this.EstatusTime;
            //console.log("Atendiendo:"+ListAtendiendo);

            await this.getNumSolicitudesThroughTimeByStatusDep('Cerrada (En espera de aprobación)');
            let ListCerradaEnEspera=this.EstatusTime;
            //console.log("Cerrada (En espera de aprobación):"+ListCerradaEnEspera);

            await this.getNumSolicitudesThroughTimeByStatusDep('Cerrada');
            let ListCerradas=this.EstatusTime;
            //console.log("Cerrada:"+ListCerradas);
            
            
            switch(this.rangoTiempo){
                case 'INTERVAL 1 DAY':
                        let horas = this.LastHours(24);
                        horas.forEach(h => {
                            this.addLabelChart(comparacionChart,h.toString());
                        });
                        
                        
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.hora,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChart,sa.total,sa.hora,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChart,ce.total,ce.hora,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChart,a.total,a.hora,3);
                        });
                        
                        this.fillNullDataChart(comparacionChart,0,horas.length);
                        this.fillNullDataChart(comparacionChart,1,horas.length);
                        this.fillNullDataChart(comparacionChart,2,horas.length);
                        this.fillNullDataChart(comparacionChart,3,horas.length);
                    break;
                case 'INTERVAL 7 DAY':
                        
                        this.LastDays(7).forEach(d => {
                            this.addLabelChart(comparacionChart,d.toString());
                        });
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.fecha,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChart,sa.total,sa.fecha,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChart,ce.total,ce.fecha,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChart,a.total,a.fecha,3);
                        });
                        this.fillNullDataChart(comparacionChart,0,7);
                        this.fillNullDataChart(comparacionChart,1,7);
                        this.fillNullDataChart(comparacionChart,2,7);
                        this.fillNullDataChart(comparacionChart,3,7);
                    break;
                case 'INTERVAL 1 MONTH':
                        
                        this.LastDays(30).forEach(m => {
                            //console.log(m);
                            this.addLabelChart(comparacionChart,m.toString());
                        });
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.fecha,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChart,sa.total,sa.fecha,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChart,ce.total,ce.fecha,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChart,a.total,a.fecha,3);
                        });
                        this.fillNullDataChart(comparacionChart,0,30);
                        this.fillNullDataChart(comparacionChart,1,30);
                        this.fillNullDataChart(comparacionChart,2,30);
                        this.fillNullDataChart(comparacionChart,3,30);
                    break;
                case 'INTERVAL 3 MONTH':
                        this.LastMonths(3).forEach(m => {
                            //console.log(m);
                            this.addLabelChart(comparacionChart,m.toString());
                        });
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChart,c.total,c.mes,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChart,sa.total,sa.mes,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChart,ce.total,ce.mes,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChart,a.total,a.mes,3);
                        });
                        
                        this.fillNullDataChart(comparacionChart,0,3);
                        this.fillNullDataChart(comparacionChart,1,3);
                    break;
            }
            
        },
        generar_Grafica_Comparacion_Dep:async function(){
            //console.log("Tickets en total");
            await this.getNumSolicitudesThroughTimeDep();
            let ListCreadas=this.EstatusbyTime;
            //console.log("Creadas:"+ListCreadas);
            //await this.getNumSolicitudesThroughTimeCerradasDep();
            //console.log(this.EstatusbyTime);
            await this.getNumSolicitudesThroughTimeByStatusDep('Sin Atender');
            let ListSinAtender=this.EstatusTime;
            //console.log("Sin Atender:"+ListSinAtender);

            await this.getNumSolicitudesThroughTimeByStatusDep('Atendiendo');
            let ListAtendiendo=this.EstatusTime;
            //console.log("Atendiendo:"+ListAtendiendo);

            await this.getNumSolicitudesThroughTimeByStatusDep('Cerrada (En espera de aprobación)');
            let ListCerradaEnEspera=this.EstatusTime;
            //console.log("Cerrada (En espera de aprobación):"+ListCerradaEnEspera);

            await this.getNumSolicitudesThroughTimeByStatusDep('Cerrada');
            let ListCerradas=this.EstatusTime;
            //console.log("Cerrada:"+ListCerradas);
            
            
            switch(this.rangoTiempo){
                case 'INTERVAL 1 DAY':
                        let horas = this.LastHours(24);
                        horas.forEach(h => {
                            this.addLabelChart(comparacionChartDep,h.toString());
                        });
                        
                        
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.hora,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChartDep,sa.total,sa.hora,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChartDep,ce.total,ce.hora,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChartDep,a.total,a.hora,3);
                        });
                        
                        this.fillNullDataChart(comparacionChartDep,0,horas.length);
                        this.fillNullDataChart(comparacionChartDep,1,horas.length);
                        this.fillNullDataChart(comparacionChartDep,2,horas.length);
                        this.fillNullDataChart(comparacionChartDep,3,horas.length);
                    break;
                case 'INTERVAL 7 DAY':
                        
                        this.LastDays(7).forEach(d => {
                            this.addLabelChart(comparacionChartDep,d.toString());
                        });

                        /*this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChartDep,s.total,s.fecha,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.fecha,0);
                        });*/
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.fecha,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChartDep,sa.total,sa.fecha,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChartDep,ce.total,ce.fecha,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChartDep,a.total,a.fecha,3);
                        });
                        this.fillNullDataChart(comparacionChartDep,0,7);
                        this.fillNullDataChart(comparacionChartDep,1,7);
                        this.fillNullDataChart(comparacionChartDep,2,7);
                        this.fillNullDataChart(comparacionChartDep,3,7);
                    break;
                case 'INTERVAL 1 MONTH':
                        
                        this.LastDays(30).forEach(m => {
                            //console.log(m);
                            this.addLabelChart(comparacionChartDep,m.toString());
                        });
                        
                        /*this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChartDep,s.total,s.fecha,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.fecha,0);
                        });*/
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.fecha,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChartDep,sa.total,sa.fecha,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChartDep,ce.total,ce.fecha,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChartDep,a.total,a.fecha,3);
                        });
                        this.fillNullDataChart(comparacionChartDep,0,30);
                        this.fillNullDataChart(comparacionChartDep,1,30);
                        this.fillNullDataChart(comparacionChartDep,2,30);
                        this.fillNullDataChart(comparacionChartDep,3,30);
                    break;
                case 'INTERVAL 3 MONTH':
                        this.LastMonths(3).forEach(m => {
                            //console.log(m);
                            this.addLabelChart(comparacionChartDep,m.toString());
                        });
                        /*this.EstatusbyTime.forEach(s => {
                            this.addDataChart(comparacionChartDep,s.total,s.mes,1);
                        });
                        this.EstatusbyTimeCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.mes,0);
                        });*/
                        ListCerradas.forEach(c => {
                            this.addDataChart(comparacionChartDep,c.total,c.mes,0);
                        });
                        ListSinAtender.forEach(sa => {
                            this.addDataChart(comparacionChartDep,sa.total,sa.mes,1);
                        });
                        ListCerradaEnEspera.forEach(ce => {
                            this.addDataChart(comparacionChartDep,ce.total,ce.mes,2);
                        });
                        ListAtendiendo.forEach(a => {
                            this.addDataChart(comparacionChartDep,a.total,a.mes,3);
                        });
                        
                        this.fillNullDataChart(comparacionChartDep,0,3);
                        this.fillNullDataChart(comparacionChartDep,1,3);
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
            //console.log("ingresando dato en:")
            //console.log(label);
            posicion=chart.data.labels.findIndex((f) => f == label);
            chart.data.datasets[dataset].data[posicion]=data;   
            //console.log("posicion: "+ posicion);
            //chart.data.datasets[dataset].data.push(data);
            chart.update();
        },
        addDataChartsinOrden:function(chart, data,dataset) {
            //console.log("ingresando dato sin orden:");
           // console.log(data);
            chart.data.datasets[dataset].data.push(data);   
            //console.log("posicion: "+ posicion);
            //chart.data.datasets[dataset].data.push(data);
            chart.update();
        },
        fillNullDataChart:function(chart,dataset,total){
            //console.log(`array ${chart.data.datasets[dataset].data}`)
            d=chart.data.datasets[dataset].data;
            for (let p = 0; p < total; p++) {
                if(d[p]==null){
                    d[p]=0;
                }
                
            }
            //console.log(`array resulta: ${d}`)
            chart.data.datasets[dataset].data=d;
            chart.update();
        },
        LastHours:function(hours) {
            var result = [];
            for (var i=0; i<=hours && result[result.length - 1]!="0 Horas"; i++) {
                var t = new Date();
                t.setTime(t.getTime() - (i*60*60*1000));
                
                
                result.push(this.formatHour(t));
            }
        
            return(result.reverse());
        },
        formatHour:function(date){
            //console.log(`fecha a convertir ${date}`);
            let hour = date.getHours().toString()+' Horas';
            return hour;
            /*if(month < 10){
                return `0$`;
            }else{
                return `${day}-${month}-${year}`;
            }*/
            
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
            this.getSolicitudesDeptoEspera();
            this.getSolicitudesDeptoAtendiendo();
            this.getSolicitudesDeptoSinAtender(); 
            this.getPorcentajeCerradas();           
        },
        getSolicitudesDeptoCerradas: function(){
            var url = 'get_solicitudes_departamento_rep';
            axios.post(url,{
                estado: 'Cerrada',
                orden: 'ASC',
                id_departamento: this.departamentoSeleccionado
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numCerradas = response.data;
            });
        },
        getSolicitudesDeptoEspera: function(){
            var url = 'get_solicitudes_departamento_rep';
            axios.post(url,{
                estado: 'Cerrada (En espera de aprobación)',
                orden: 'ASC',
                id_departamento: this.departamentoSeleccionado
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numEspera = response.data;
            });
        },
        getSolicitudesDeptoAtendiendo: function(){
            var url = 'get_solicitudes_departamento_rep';
            axios.post(url,{
                estado: 'Atendiendo',
                orden: 'ASC',
                id_departamento: this.departamentoSeleccionado
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numAtendiendo = response.data;
            });
        },
        getSolicitudesDeptoSinAtender: function(){
            var url = 'get_solicitudes_departamento_rep';
            axios.post(url,{
                estado: 'Sin atender',
                orden: 'ASC',
                id_departamento: this.departamentoSeleccionado
            })
            .then(response => {
                //console.log(response.data);
                //this.solicitudesDepto = response.data;
                this.numSinAtender = response.data;
                //this.porcentajeCerrados = ((this.numCerradas/(this.numAtendiendo + this.numSinAtender + this.numCerradas + this.numEspera)) * 100).toFixed(2);
            });
        },
        getPorcentajeCerradas: function(){
            var url = 'get_porcentaje_cerradas';
            axios.post(url,{
                id_departamento: this.departamentoSeleccionado
            })
            .then(response => {
                
                this.porcentajeCerrados = response.data.toFixed(2);
            });
        },
    }
});