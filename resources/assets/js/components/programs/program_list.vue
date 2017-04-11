<template>
    <div >
        <div class="col-lg-3">
            <div class="input-group">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button">Go!</button>
              </span>
              <input v-model="search" type="text" class="form-control" placeholder="Search for...">
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <br>
        <table style="padding: 20px" class="table table-hover table-striped">
            <thead>
                <tr style="cursor: pointer">
                    <th @click="sortBy('program_name')">Program Name</th>
                    <th @click="sortBy('program_manager')">Program Manager</th>
                    <th>Report</th>
                    <th class="text-center">Submitted Lgu</th>
                    <th class="text-center">Percentage</th>
                    <th v-show="user.usertype === 'program-manager'">Edit</th>
                    <th v-show="user.usertype === 'program-manager'">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(program, index) in filterPrograms">
                    <td>
                        <router-link :to="{ name: 'program-reports', params: { id: program.id }}">
                            {{ program.program_name.toUpperCase() }}
                        </router-link>
                    </td>
                    <td>{{ getName(program).toUpperCase() }}</td>
                    <th>{{ getReports(program) }}</th>
                    <th class="text-center">{{ getTotalLguSubmitted(program) }}</th>
                    <td style="font-weight: bolder; text-align: center" :class="{ 'text-danger' : (getTotalPercentage(program) <= 20) ? true : false, 'text-primary' : (getTotalPercentage(program) >= 50) ? true : false }"><b>{{ getTotalPercentage(program) }} %</b></td>
                    <td v-show="user.usertype === 'program-manager'">
                        <i @click="updateReport(program)" style="cursor: pointer" class="fa fa-pencil"></i>
                    </td>
                    <td v-show="user.usertype === 'program-manager'">
                        <i @click="removeREport(program, index)" style="cursor: pointer" class="fa fa-remove"></i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import alertify from 'alertify.js'
    import accounting from 'accounting'
    export default {
        mounted() {

        },
        props: {
            programs: {
                type: Array
            },
            users: {
                type: Array
            },
            user: {
                type: Object
            },
            programStats: {
                type: Array
            },
            submittedDates: {
                type: Array
            },
            checkedLgus: {
                type: Array
            }
        },
        data(){
            return {
                search: '',
                clickSort: 0
            }
        },
        methods: {
            getTotalPercentage(program){
                let self = this;
                let length = self.getTotalLguSubmitted(program);
                let total = 146;
                let calculated = (length / total) * 100;
                return accounting.formatNumber(calculated, 1, " ");
            },
            getTotalLguSubmitted(program){
                let self = this;
                return _.filter(self.checkedLgus, {
                    program_id: program.id
                }).length;
                // console.log(rs);
            },
            removeREport(program, index){
                let self = this;
                let rsProgramStats = _.filter(self.programStats, {
                    program_id: program.id
                });
                console.log(rsProgramStats)
                if (!rsProgramStats.length) {
                    alertify.confirm("Are you sure you want to delete: (" + program.program_name.toUpperCase() + ') Program ?', function () {
                        // user clicked "ok"
                        let resource = self.$resource('program{/id}');
                        resource.delete({
                            id: program.id
                        }).then((resp) => {
                            if (resp.status === 200) {
                                let json = resp.body;
                                if (json.rsProgram) {
                                    self.programs.splice(index, 1);
                                }
                            }
                        }, (resp) => {
                            console.log(resp);
                        })
                    }, function() {
                        // user clicked "cancel"
                        console.log('ok');
                    });
                }else {
                    alertify.alert("(" + program.program_name.toUpperCase() + ') Cant be deleted, because it has existing reports.');
                }
                
            },
            sortBy(attr){
                let self = this;
                ++self.clickSort;
                let type = (self.clickSort % 2 === 0) ? 'asc' : 'desc';
                let resource =self.$resource('programs/sortby{/attribute}{/type}'); 
                resource.get({
                    attribute: attr,
                    type: type
                }).then((resp) => {
                    let json = resp.body;
                    self.$emit('sortresponse', json);
                }, (resp) => {
                    console.log(resp);
                })
            },
            getReports(program){
                let self = this;
                let stats = _.filter(self.programStats, {program_id: Number(program.id)});
                if (stats.length) {
                    return _.map(stats, 'reporting_freq').join(',   ');
                }
            },
            getTotalReports(program){
                let self = this;
                return _.filter(self.programStats, {program_id: Number(program.id)}).length;
            },
            getProgramsCompleted(program){
                let self = this;
                let completed = 0
                let rsProgramStats = _.filter(self.programStats, {program_id: Number(program.id)});
                if (rsProgramStats.length) {
                    let stat = {};
                    let rsSubmittedDates = [], submittedDate = {};
                    for (var i = rsProgramStats.length - 1; i >= 0; i--) {
                        stat = rsProgramStats[i];
                        let rsSubmittedDates = _.filter(self.submittedDates, { program_stat_id: stat.id});
                        if (rsSubmittedDates.length) {
                            ++completed;
                        }
                    }
                }
                return completed;
            },
            getProgramsInComplete(program){
                let self = this;
                let notCompleted = 0
                let rsProgramStats = _.filter(self.programStats, {program_id: Number(program.id)});
                if (rsProgramStats.length) {
                    let stat = {};
                    let rsSubmittedDates = [], submittedDate = {};
                    for (var i = rsProgramStats.length - 1; i >= 0; i--) {
                        stat = rsProgramStats[i];
                        let rsSubmittedDates = _.filter(self.submittedDates, { program_stat_id: stat.id});
                        if (!rsSubmittedDates.length) {
                            ++notCompleted;
                        }
                    }
                }
                return notCompleted;
            },
            updateReport(program){
                let self = this;
                router.push({ name: 'program-reports', params: { id: program.id }});
            },
            getName(program){
                let self = this;
                let rs = _.filter(self.users, {id: program.program_manager});
                return (rs.length > 0) ? rs[0].name : '[Not found]';
            }
        },
        computed: {
            filterPrograms(){
                let self = this;
                let keyword = self.search.toLowerCase();
                let rsUsers = [], program_manager = '';
                return self.programs.filter(function(index) {
                    rsUsers = _.filter(self.users, { id: index.program_manager});
                    program_manager = (rsUsers.length > 0) ? rsUsers[0].name : '';
                    return index.program_name.toLowerCase().indexOf(keyword) != -1 ||
                           program_manager.toLowerCase().indexOf(keyword) != -1;
                });
            }
        }
    }
</script>
