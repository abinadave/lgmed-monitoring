<template>
    <div class="container">
        <div class="modal fade" id="modal-report-files" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 800px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ program.program_name }}, {{ currentStat.reporting_freq }} Report Files</h4>
              </div>
              <div class="modal-body">
                    <form id="form-report-files" name="form-report-files">
                        <input name="photo" type="file"><br>
                        <input name="program_stat_id" type="hidden" :value="currentStat.id">
                        <input name="program_id" type="hidden" :value="currentStat.program_id">
                        <button :disabled="whileUploading" class="btn btn-primary btn-info" 
                        @click.prevent="uploadTemporaryFiles">
                            <span v-if="!whileUploading">
                                <i class="fa fa-upload" aria-hidden="true"></i> Upload selected file
                            </span>
                            <span v-else>
                                Please wait, file being uploaded...
                            </span>
                        </button>
                    </form>
                    <hr>
                    <report-files @viewfile="previewFile" :users="users" :report-files="report_files"></report-files>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<script>
    import alertify from 'alertify.js'
    import CompListOfReportFiles from './list_of_report_files.vue'
    export default {
        components: {
            'report-files': CompListOfReportFiles
        },
        props: {
            programStats: {
                type: Array
            },
            currentStat: {
                type: Object
            },
            programs: {
                type: Array
            },
            users: {
                type: Array
            }
        },
        data(){
            return {
                program: {},
                report_files: [],
                whileUploading: false
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        methods: {
            previewFile(stat){
                let self = this;
                let url = '/report/files/view/'+self.program.id+'/'+stat.program_stat_id+'/' + stat.source;
                window.open(url, "_blank");
            },
            uploadTemporaryFiles(){
                let self = this;
                var form = document.forms.namedItem("form-report-files"); // high importance!, here you need change "yourformname" with the name of your form
                var formdata = new FormData(form); // high importance!
                self.whileUploading = true;
                $.ajax({
                    async: true,
                    type: "POST",
                    dataType: "json", // or html if you want...
                    contentType: false, // high importance!
                    url: 'upload/report/file', // you need change it.
                    data: formdata, // high importance!
                    processData: false, // high importance!
                    success: function (data) {
                        //do thing with data....
                        $('#yourformname')[0].reset();
                        if (data.uploaded === true) {
                            setTimeout(function(){
                                alertify.success('File successfully uploaded');
                                self.whileUploading = false;
                                self.report_files.unshift(data.report_file);
                            }, 700);
                        }
                        if (data.file_found === false) {
                            setTimeout(function(){
                                alertify.error("Please select a file to upload!");
                                self.whileUploading = false;
                            }, 1500);
                        }
                    },
                    timeout: 10000
                });
            },
            fetchFiles(){
                let self = this;
                let resource = self.$resource('report/files{/id}');
                resource.get({
                    id: self.currentStat.id
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.report_files = json;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
            getProgram(){
                let self = this;
                let rs = _.filter(self.programs, {id: self.currentStat.program_id});
                if (rs.length) {
                    self.program = rs[0];
                }
            }
        },
        watch: {
            'currentStat': function(newVal){
                this.fetchFiles();
                this.getProgram();
            }
        }
    }
</script>
 