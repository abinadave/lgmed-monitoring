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
                    <div>
                        <report-list
                        @fetchcheckedbyprovince="fetchCheckedLguByProvince"
                        @newprovince="updateCurrentProvince"
                        @setcurrentreport="setCurrentReport" 
                        :program="program"
                        :program-stats="program_stats"
                        :submitted-dates="submitted_dates"
                        :report-files="report_files"
                        :provinces="provinces"
                        :actual-checked-lgus="actual_checked_lgus"
                        :no-report="noReport"
                        ></report-list>
                    </div>
                    <div>
                        <!-- 
                        <table class="table table-bordered table-condensed" style="font-size: 10px; display: none">
                              <tr>
                                  <th>PROGRAM_ID</th>
                                  <th>PROGRAM_STAT_ID</th>
                                  <th>MUNICIPALITY_ID</th>
                                  <th>PROVINCE_ID</th>
                                  <th>ID</th>
                              </tr>
                              <tr v-for="actual_model in actual_checked_lgus">
                                  <td>{{ actual_model.program_id }}</td>
                                  <td>{{ actual_model.program_stat_id }}</td>
                                  <td>{{ actual_model.municipality_id }}</td>
                                  <td>{{ actual_model.province_id }}</td>
                                  <td>{{ actual_model.id }}</td>
                              </tr>
                        </table> 
                        -->
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
        @addcheckedlgu="createCheckedLgu"
        @removecheckedlgu="deletedCheckedLgu"
        @checkalllgus="checkAllAddLgu"
        @unchecklgus="uncheckAllLgu"
        @checkedlguremove="deleteCheckedLgu" 
        @refetchmymodels="fetchMyReports"
        :filtered-lgus="modalFilteredLgus"
        :province="currentProvince"
        :stat="currentStat"
        :program="program"
        :provinces="provinces"
        :checked-lgus="checked_lgus"
        :lgus="lgus">
        </modal-checked-lgus>  
        
    </div>
</template>

<script>
    import CompCreateReport from './report/create_report_program.vue'
    import CompReportList   from './report/report_list.vue'
    import CompModalSubmit  from './report/modal_submit_report_now.vue'
    import CompReportFiles  from './report/modal_report_files.vue'
    import CompModalLgus    from './modal_checked_lgus.vue'
    import alertify from 'alertify.js'
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
                    id: 0,
                    program_name: '',
                    program_manager: ''
                },    
                noReportWasFound: false,
                currentStat: {},
                submitted_dates: [], report_files: [],
                lgus: [], provinces: [],
                modalFilteredLgus: [],
                checked_lgus: [],
                actual_checked_lgus: [],
                currentProvince: {
                    name: ''
                },
                /* current program id of the panel */
                currentProgramId: 0,
                noReport: false
            }
        },
        props: {
            user: {
                type: Object
            }
        },
        methods: {
            deleteCheckedLgu(models){
                let self = this;
                let model = {};
                console.log(models);
                let foundIndexes = [], foundIndex = 0;
                for (var i = models.length - 1; i >= 0; i--) {
                    model = models[i];
                    let rs = _.filter(self.actual_checked_lgus, { id: Number(model.id)});
                    if (rs.length) {
                        let actual = rs[0];
                        foundIndex = self.actual_checked_lgus.findIndex(actual => actual.id === model.id);
                        self.actual_checked_lgus.splice(foundIndex, 1);
                    }
                }
            },
            uncheckAllLgu(deletedCheckedLgu){
                let self = this;
                let indexes = [], index = 0;
                let model = {}, rs = [];
                deletedCheckedLgu.forEach(function(model){
                    rs = _.filter(self.actual_checked_lgus, { id: model.id  });
                    if (rs.length) {
                        // index = self.actual_checked_lgus.findIndex(actual => actual.id === model.id);
                        // indexes.push(index);
                        index = _.findIndex(self.actual_checked_lgus, {
                            id: Number(model.id)
                        });
                        indexes.push(index)
                    }
                });
                indexes.forEach(function(i){
                    self.actual_checked_lgus.splice(i, 1);
                });
            },
            checkAllAddLgu(models){
                let self = this;
                let rs = [];
                for (var i = models.length - 1; i >= 0; i--) {
                    rs = _.filter(self.actual_checked_lgus, { id: models[i].id});
                    if (!rs.length) {
                        self.actual_checked_lgus.push(models[i])
                    }
                }
            },
            deletedCheckedLgu(model){
                let self = this;
                let index = self.actual_checked_lgus.findIndex
                    (
                    checked => checked.municipality_id === model.municipality_id &&
                               checked.program_stat_id === model.program_stat_id &&
                               checked.province_id     === model.province_id
                    );
                    self.actual_checked_lgus.splice(index , 1);
            },
            createCheckedLgu(json){
                let self = this;
                self.actual_checked_lgus.push(json);
            },
            fetchCheckedLguByProvince(headers){
                let self = this;
                self.$http.post('fetch/checked/lgu', headers).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.checked_lgus = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
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
                        // alert(json.length)
                        // console.log(json);
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
                        self.currentProgramId = json.program.id;
                        self.program = json.program;
                        for (var i = json.reports.length - 1; i >= 0; i--) {
                            self.program_stats.push(json.reports[i]);
                        }
                        if(!json.reports.length){
                            alertify.alert('please create atlease one program');
                            self.noReport = true;
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
            },
            fetchMyReports(programId){
                let self = this;
                self.$http.post('/fetch_checked_lgu_by_program', {
                    program_id: programId
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.actual_checked_lgus = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            }
        },
        watch: {
            '$route.params.id': function(newVal){
                // console.log(newVal);
            },
            'currentProgramId': function(programId){
                let self = this;
                self.fetchMyReports(programId);
            },
            'actual_checked_lgus': function(newVal){
                let self = this;
                self.fetchCheckedLguByProvince({
                    program_id: self.currentProgramId,
                    program_stat_id: self.currentStat.id,
                    province_id: self.currentProvince.id
                });
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
