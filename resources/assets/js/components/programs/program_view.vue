<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <b title="PROGRAM TITLE">{{ program.program_name.toUpperCase() }}</b>
                        <span class="pull-right btn btn-default btn-xs"><router-link :to="{ name: 'programs' }">back</router-link></span>
                        <button @click="createReport" class="btn btn-xs btn-success pull-right" style="margin-right: 10px">Add report to {{ program.program_name }}</button>
                    </div>
                    <div class="panel-body">
                    <div v-if="!program_stats.length">
                        No Report was found for
                    </div>
                    <div v-else>
                        <report-list
                        @fetchcheckedbyprovince="fetchCheckedLguByProvince"
                        @newprovince="updateCurrentProvince"
                        @setcurrentreport="setCurrentReport" 
                        :program="program"
                        :program-stats="program_stats"
                        :submitted-dates="submitted_dates"
                        :report-files="report_files"
                        :provinces="provinces"
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
        @addreportfile="addnewReportFileChild"
        @addsubmitteddates="addSubmittedDateChild"
        :program="program"
        :program-stat="currentStat"
        :report-files="report_files"
        ></modal-submit-report>

        <modal-report-files 
        :program-stats="program_stats"
        :programs="programs"
        :current-stat="currentStat"
        :users="users"
        :report-files="report_files"
        ></modal-report-files>

        <modal-checked-lgus 
        @setcurrentreport="setCurrentReport"
        :filtered-lgus="modalFilteredLgus"
        :province="currentProvince"
        :stat="currentStat"
        :provinces="provinces"></modal-checked-lgus>
    </div>
</template>

<script>
    import CompCreateReport from './report/create_report_program.vue'
    import CompReportList   from './report/report_list.vue'
    import CompModalSubmit  from './report/modal_submit_report_now.vue'
    import CompReportFiles  from './report/modal_report_files.vue'
    import CompModalLgus    from './modal_checked_lgus.vue'
    export default {
        mounted() {
            this.fetch();
            this.fetchUsers();
            this.fetchSubmittedDates();
            this.fetchPrograms();
            this.fetchReportFiles();
            this.fetchLguAndProvince();
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
                submitted_dates: [], report_files: [],
                lgus: [], provinces: [],
                modalFilteredLgus: [],
                currentProvince: {
                    name: ''
                }
            }
        },
        props: {
            user: {
                type: Object
            }
        },
        methods: {
            fetchCheckedLguByProvince(headers){
                console.log(headers);
            },
            updateCurrentProvince(json){
                let self = this;
                let provinceId= json.id;
                self.currentProvince = json;
                let rsLgus = _.filter(self.lgus, { province_id: provinceId });
                self.modalFilteredLgus = rsLgus;
            },
            fetchLguAndProvince(){
                let self = this;
                self.$http.get('/province/lgu').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.provinces = json.provinces;
                        self.lgus = json.lgus;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
            addnewReportFileChild(file){
                let self = this;
                self.report_files.push(file);
                self.currentStat = { program_id: 0, id: 0, reporting_freq: '', submission_date: '' };
            },
            fetchReportFiles(){
                let self = this;
                self.$http.get('/report/file').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.report_files = json;
                        console.log(self.report_files.length);
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
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
            createReportChild(respReport){
                let self = this;
                self.program_stats.push(respReport);
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
                console.log(newVal);
            }
        },
        components: {
            'modal-create-report': CompCreateReport,
            'modal-submit-report': CompModalSubmit,
            'modal-report-files': CompReportFiles,
            'report-list': CompReportList,
            'modal-checked-lgus': CompModalLgus
        }
    }
</script>
