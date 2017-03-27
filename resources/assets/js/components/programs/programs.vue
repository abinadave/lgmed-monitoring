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
                        <button @click="createProgram" class="btn btn-sm btn-primary pull-right">
                            
                            <span v-if="user.usertype === 'program-manager'">
                                Build program
                            </span>
                            <span v-else="user.usertype === 'program-manager'">
                                Add program
                            </span>
                        <i class="fa fa-tasks" aria-hidden="true"></i></button>
                        <list-of-programs :user="user" :programs="programs" :users="users"></list-of-programs>
                    </div>
                </div>
            </div>
        </div>
        <modal-create-program :user="user" @newprogram="newChildProgram" :users="users"></modal-create-program>
    </div>
</template>

<script>
    import CompCreateProgram from './create_program.vue'
    import CompProgramList from './program_list.vue'
    export default {
        mounted() {
            let self = this;
            console.log('Component program.vue mounted.');
            self.fetchProgramManagers();
            self.fetchPrograms();
        },
        props: {
            user: {
                type: Object
            }
        },
        data(){
            return {
                users: [], programs: [],
                imgUrl: '/img/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg.png'
            }
        },
        methods: {
            newChildProgram(respProgram){
                let self = this;
                self.programs.unshift(respProgram);
            },
            createProgram(){
                let self = this;
                $('#modal-create-program').modal('show');
            },
            fetchPrograms(){
                let self = this;
                self.$http.get('/program/management').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.programs = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
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
            'modal-create-program': CompCreateProgram,
            'list-of-programs': CompProgramList
        },
        watch: {
            'user': function(newVal){
                console.log(newVal)
            }
        }
    }
</script>
