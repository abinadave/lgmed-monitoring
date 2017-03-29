<template>
    <div>
       <table class="table table-hover table-striped">
           <thead>
               <tr>
                   <th>Reporting Freq.</th>
                   <th>Status</th>
                   <th>Submission Date</th>
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
        }
    }
</script>
