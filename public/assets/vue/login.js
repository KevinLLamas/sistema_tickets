new Vue({
    el: '#login',
    data: {
        user: '',
        pass: '',
    },
    created: function() {

    },
    methods: {
        
        Ingresar: function() {
            if (this.user != '' && this.pass != '') {
                axios.post('login', {
                    user: this.user,
                    password: this.pass
                }).then(response => {
                    if( response.data.ok ){
                        window.location = '/listado';
                    }else{
                        Swal.fire('Atención', response.data.message, 'warning');
                    }
                }).catch(err => {
                    Swal.fire('', 'Ocurrió un error, intente más tarde', 'error');
                });
            }

        },

    }
});