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
        <table style="padding: 20px; font-size: 12px" class="table table-hover">
            <thead>
                <tr>
                    <th>Program Names</th>
                    <th>Program Managers</th>
                    <th style="text-align: center">Completed</th>
                    <th style="text-align: center">Pending</th>
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
                    <td style="text-align: center">{{ getProgramsCompleted(program) }}</td>
                    <td style="text-align: center">0</td>
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
            }
        },
        data(){
            return {
                search: ''
            }
        },
        methods: {
            getProgramsCompleted(program){
                let self = this;
                let rs = _.filter(self.programStats, {program_id: Number(program.id)});
                if (rs.length) {
                    console.log(rs)
                }
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
