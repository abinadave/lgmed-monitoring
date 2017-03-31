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
                    <th @click="sortBy('program_name')">Program Names</th>
                    <th @click="sortBy('program_manager')">Program Managers</th>
                    <th>Report</th>
                    <th style="text-align: center">Completed Report</th>
                    <th style="text-align: center">Pending Report</th>
                    <th style="text-align: center">Total Report</th>
                    <th v-show="user.usertype === 'program-manager'">edit report</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="program in filterPrograms">
                    <td>
                        <router-link :to="{ name: 'program-reports', params: { id: program.id }}">
                            {{ program.program_name.toUpperCase() }}
                        </router-link>
                    </td>
                    <td>{{ getName(program).toUpperCase() }}</td>
                    <th>{{ getReports(program) }}</th>
                    <td style="text-align: center">{{ getProgramsCompleted(program) }}</td>
                    <td style="text-align: center">{{ getProgramsInComplete(program) }}</td>
                    <td style="text-align: center">{{ getTotalReports(program) }}</td>
                    <td v-show="user.usertype === 'program-manager'">
                        <i @click="updateReport(program)" style="cursor: pointer" class="fa fa-pencil"></i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
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
            }
        },
        data(){
            return {
                search: '',
                clickSort: 0
            }
        },
        methods: {
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
