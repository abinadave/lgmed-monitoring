<template>
    <div>
       <table class="table table-hover table-striped">
           <thead>
               <tr>
                   <th>Reporting Freq.</th>
                   <th>Status</th>
                   <th>Deadline</th>
                   <th>Date submitted</th>
                   <th>files</th>
                   <th width="1"></th>
               </tr>
           </thead>
           <tbody>
               <tr v-for="stat in programStats">
                   <td>{{ stat.reporting_freq }}</td>
                   <td>
                       <span v-if="checkIfSubmitted(stat) === 1">
                           <b class="text-primary">Submitted</b>
                       </span>
                       <span class="text-danger" v-else>
                           <b>no report yet</b>
                       </span>
                   </td>
                   <td>{{ formatDate(stat.submission_date) }} &nbsp;&nbsp;( {{ fromNOw(stat.submission_date) }} )</td>
                   <td>
                      <span v-if="checkIfSubmitted(stat) === 1">
                          {{ getSubmittedDate(stat) }} 
                      </span>
                      <span v-else>
                          <a style="cursor: pointer" @click="confirmSubmission(stat)">mark as submitted</a>
                      </span>
                   </td>
                   <td><i style="cursor: pointer" @click="uploadReportFiles(stat)" class="fa fa-2x fa-folder" aria-hidden="true"></i></td>
                   <td>
                       <span v-if="checkIfSubmitted(stat) !== 1">
                           <i @click="deleteProgram(stat)" style="cursor: pointer" class="fa fa-remove"></i>
                       </span>
                   </td>
               </tr>
           </tbody>
       </table>
    </div>
</template>

<script>
    import moment from 'moment'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            
        },
        props: {
            programStats: {
                type: Array
            },
            submittedDates: {
                type: Array
            }
        },
        methods: {
            deleteProgram(stat){
                let self = this;
                alertify.confirm("Are you sure ?", function () {
                    let resource = self.$resource('program/stat{/id}');
                    resource.delete({
                      id: stat.id
                    }).then((resp) => {
                        console.log(resp);
                    }, (resp) => {
                        console.log(resp);
                    });
                }, function() {
                    // user clicked "cancel"
                });
                
            },
            uploadReportFiles(stat){
                let self = this;
                self.$emit('setcurrentreport', stat);
                $('#modal-report-files').modal('show');
            },
            checkIfSubmitted(stat){
                let self = this;
                return _.filter(self.submittedDates, {program_stat_id: stat.id}).length;
            },
            formatDate(date){
                return moment(date).format('MMMM DD, YYYY');
            },
            fromNOw(date){
                let self = this;
                return moment(date).fromNow();
            },
            getSubmittedDate(stat){
                let self = this;
                let rs = _.filter(self.submittedDates, {program_stat_id: stat.id});
                if (rs.length) {
                    return self.formatDate(rs[0].date_submitted);
                }else {
                    return '-';
                }
            },
            confirmSubmission(stat){
                let self = this;
                self.$emit('setcurrentreport', stat);
                $('#modal-submit-report').modal('show');
            },
            submitNow(stat){
                let self = this;
                
            }
        }
    }
</script>
