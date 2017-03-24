<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <img style="width: 50px; height: 50px" :src="imgUrl">
                        <h2 style="display: inline; padding: 15px">LGMED Monitoring Dashboard</h2>
                    </div>

                    <div class="panel-body">
                        <button @click="createProgram" class="btn btn-info pull-right">Add &nbsp;&nbsp;<i class="fa fa-tasks" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <modal-create-program :users="users"></modal-create-program>
    </div>
</template>

<script>
    import CompCreateProgram from './create_program.vue'
    export default {
        mounted() {
            console.log('Component mounted.');
            this.fetchProgramManagers();
        },
        data(){
            return {
                users: [],
                imgUrl: '/img/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg.png'
            }
        },
        methods: {
            createProgram(){
                let self = this;
                $('#modal-create-program').modal('show');
            },
            fetchProgramManagers(){
                let self = this;
                self.$http.get('/user/management').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.users = json.users;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            }
        },
        components: {
            'modal-create-program': CompCreateProgram
        }
    }
</script>
