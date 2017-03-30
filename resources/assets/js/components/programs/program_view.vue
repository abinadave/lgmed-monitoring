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
                    <div v-if="noReportWasFound">
                        No Report was found for
                    </div>
                    <div v-else="noReportWasFound">
                        <report-list 
                        @setcurrentreport="setCurrentReport" 
                        :program="program" 
                        :program-stats="program_stats"
                        :submitted-dates="submitted_dates"
                        ></report-list>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <modal-create-report 
        @newreportcreated="createReportChild"
        :program="program">
        </modal-create-report>

        <modal-submit-report 
        @addsubmitteddates="addSubmittedDateChild"
        :program="program"
        :program-stat="currentStat"
        ></modal-submit-report>

        <modal-report-files 
        :program-stats="program_stats"
        :programs="programs"
        :current-stat="currentStat"
        :users="users"
        ></modal-report-files>
    </div>
</template>

<script>
    import CompCreateReport from './report/create_report_program.vue'
    import CompReportList   from './report/report_list.vue'
    import CompModalSubmit  from './report/modal_submit_report_now.vue'
    import CompReportFiles  from './report/modal_report_files.vue'
    export default {
        mounted() {
            this.fetch();
            this.fetchUsers();
            this.fetchSubmittedDates();
            this.fetchPrograms();
        },
        data(){
            return {
                users: [], program_stats: [],
                programs: [],
                program: {
                    program_name: '',
                    program_manager: ''
                },
                noReportWasFound: false,
                currentStat: {},
                submitted_dates: []
            }
        },
        props: {
            user: {
                type: Object
            }
        },
        methods: {
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
            createReportChild(respReport){
                this.program_stats.unshift(respReport);
            },
            addSubmittedDateChild(respSubmittedDate){
                this.submitted_dates.push(respSubmittedDate);
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
            'modal-report-files': CompReportFiles,
            'report-list': CompReportList
        }
    }
</script>
