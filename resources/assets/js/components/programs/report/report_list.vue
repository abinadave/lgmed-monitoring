<template>
    <div>
       <table class="table table-hover table-striped">
           <thead>
               <tr>
                   <th>Reporting Freq.</th>
                   <th>Status</th>
                   <th>Submission Date/Deadline</th>
                   <th>Date Submitted</th>
                   <th></th>
               </tr>
           </thead>
           <tbody>
               <tr v-for="stat in programStats">
                   <td>{{ stat.reporting_freq }}</td>
                   <td>
                       <span v-if="stat.status === 1">
                           <i class="text-success">Submitted</i>
                       </span>
                       <span class="text-danger" v-else>
                           <b>no report yet</b>
                       </span>
                   </td>
                   <td>{{ stat.submission_date }}</td>
                   <td>-</td>
                   <td>
                     <span v-if="stat.status != 1">
                        <a style="cursor: pointer" @click="confirmSubmission(stat)">mark as submitted</a>
                     </span>
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
            programStats: {
                type: Array
            }
        },
        methods: {
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
