<template>
    <div>
        files found : {{ reportFiles.length }}
        <table class="table table-hover">
             <thead>
                 <tr>
                    <th></th>
                     <th>Filename</th>
                     <th>Uploaded by</th>
                     <th>Date uploaded</th>
                     <th></th>
                 </tr>
             </thead>
             <tbody>
                 <tr v-for="report in reportFiles">
                     <th><b v-html="getFileType(report.source)"></b></th>
                     <td>{{ report.source }}</td>
                     <td>{{ getUser(report.uploaded_by) }}</td>
                     <td>{{ formatDate(report.created_at) }}</td>
                   
                     <th><i @click="viewFile(report)" style="cursor: pointer" class="fa fa-2x fa-eye text-primary" aria-hidden="true"></i></th>
                 </tr>
             </tbody>
        </table>
    </div>
</template>

<script>
    import moment from 'moment'
    export default {
        props: {
            reportFiles: {
                type: Array
            },
            users: {
                type: Array
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        methods: {
            viewFile(report){
                let self = this;
                self.$emit('viewfile', report);
            },
            downloadFile(report){
                let self = this;
                self.$emit('downloadfile', report);
            },
            getFileType(filename){
                let ext = filename.split('.').pop().toLowerCase();
                if (ext === 'csv' || ext === 'xlsx' || ext === 'xls') {
                    return '<i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
                }else if(ext === 'pdf'){
                    return '<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>';
                }else if(ext === 'jpeg' || ext === 'png' || ext === 'jpg'){
                    return '<i class="fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
                }else if(ext === 'docx'){
                    return '<i class="fa fa-file-word-o fa-2x" aria-hidden="true"></i>';
                }else {
                    return '<i class="fa fa-file fa-2x" aria-hidden="true"></i>';
                }
            },
            getUser(uploadedBy){
                let self = this;
                let rs = _.filter(self.users, {id: uploadedBy});
                if (rs.length) {
                    return rs[0].name;
                }
            },
            formatDate(date){
                return moment(date).format('MMMM DD, YYYY, dddd');
            }
        }
    }
</script>
