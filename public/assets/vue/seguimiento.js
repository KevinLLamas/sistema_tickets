
var url = window.location.pathname;
var getId = url.substring(url.lastIndexOf('/')+1);

new Vue({
    el: '#app',
    data:{
        var: '',
        seguimiento: {}
    },
    created: function(){
        this.muestra();
    },
    methods:{
        muestra: function(){
            axios.get(`/getSolicitud/`+getId).then(response=>{
                //console.log(response);
                this.seguimiento = response.data;
                //console.log(this.seguimiento.correo_atencion);
                console.log(this.$route.params.id);
            }).catch(function (error) {
                //console.log(error);
            });
        },
    }
});