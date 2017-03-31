<template>
    <div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <img style="width: 50px; height: 50px" :src="imgUrl">
                        <h2 style="display: inline; padding: 15px">LGMED Monitoring Dashboard</h2>
                    </div>
                    <div class="panel-body">
                        <button @click="createProgram" class="btn btn-sm btn-primary pull-right">
                            Add program
                        <i class="fa fa-tasks" aria-hidden="true"></i></button>
                        <list-of-programs
                         @sortresponse="reSortPrograms"
                         :program-stats="program_stats"
                         :user="user" 
                         :programs="programs" 
                         :submitted-dates="submitted_dates"
                         :users="users"></list-of-programs>
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
            self.fetchProgramStats();
            self.fetchSubmittedDates();
        },
        props: {
            user: {
                type: Object
            }
        },
        data(){
            return {
                users: [], programs: [],
                program_stats: [],
                submitted_dates: [],
                imgUrl: '/img/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg.png'
            }
        },
        methods: {
            reSortPrograms(respPrograms){
                let self = this;
                self.programs = respPrograms;
            },
            fetchSubmittedDates(){
                let self = this;
                self.$http.get('/submitted/date').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.submitted_dates = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
            fetchProgramStats(){
                let self = this;
                self.$http.get('/program/stats').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.program_stats = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
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
