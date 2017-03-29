<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <b title="PROGRAM TITLE">{{ program.program_name.toUpperCase() }}</b>
                        <span class="pull-right btn btn-default btn-xs"><router-link :to="{ name: 'programs' }">back</router-link></span>
                        <button @click="createReport" class="btn btn-xs btn-default pull-right" style="margin-right: 10px">add report to {{ program.program_name}}</button>
                    </div>
                    <div class="panel-body">
                    <div v-if="noReportWasFound" class="alert alert-info" role="alert">
                        No Report was found for : 
                    </div>
                    <div v-else="noReportWasFound">
                        <report-list @setcurrentreport="setCurrentReport" :program="program" :program-stats="program_stats"></report-list>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <modal-create-report :program="program"></modal-create-report>
        <modal-submit-report 
        :program="program"
        :program-stat="currentStat"
        ></modal-submit-report>
    </div>
</template>

<script>
    import CompCreateReport from './report/create_report_program.vue'
    import CompReportList   from './report/report_list.vue'
    import CompModalSubmit  from './report/modal_submit_report_now.vue'
    export default {
        mounted() {
            this.fetch();
            this.fetchUsers();
        },
        data(){
            return {
                users: [], program_stats: [],
                program: {
                    program_name: '',
                    program_manager: ''
                },
                noReportWasFound: false,
                currentStat: {}
            }
        },
        props: {
            user: {
                type: Object
            }
        },
        methods: {
            setCurrentReport(stat){
                let self = this;
                self.currentStat = stat;
            },
            createReport(){
                let self = this;
                $('#create-report-program').modal('show');
            },
            fetchUsers(){
                let self = this;
                self.$http.get('/user/management').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.users = json.users;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                        console.log(resp);
                    }
                });
            },
            fetch(){
                let self = this;
                let resource = self.$resource('program/stats{/id}');
                let id = router.history.current.params.id;
                resource.get({
                    id: id
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.program = json.program;
                        for (var i = json.reports.length - 1; i >= 0; i--) {
                            self.program_stats.push(json.reports[i]);
                        }
                        if (!json.reports.length) {
                            self.noReportWasFound = true;
                        }else {
                            self.noReportWasFound = false;
                        }
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp);
                    }
                });
            }
        },
        watch: {
            '$route.params.id': function(newVal){
                console.log(newVal)
            }
        },
        components: {
            'modal-create-report': CompCreateReport,
            'modal-submit-report': CompModalSubmit,
            'report-list': CompReportList
        }
    }
</script>
