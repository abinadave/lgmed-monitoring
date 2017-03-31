<template>
    <div>
        <div class="modal fade" id="modal-submit-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 360px">
            <div class="modal-content" >
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Report Submission</h4>
              </div>
              <div class="modal-body">
                   <form id="submit-report-form" name="submit-report-form">
                        <label>Date submitted
                           <input name="date_submitted" v-model="date" id="date-submitted" type="text" class="form-control">
                        </label>
                        <label>Report File</label>
                        <input name="photo" type="file" multiple><br>
                        <input name="program_stat_id" type="text" :value="programStat.id">
                        <input name="program_id" type="text" :value="programStat.program_id">
                    </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button :disabled="whenSubmitting" @click="submitReport" type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import 'jquery-ui/themes/base/core.css';
    import 'jquery-ui/themes/base/theme.css';
    import 'jquery-ui/ui/core';
    import 'jquery-ui/ui/widgets/datepicker';
    import 'jquery-ui/themes/base/datepicker.css';
    import moment from 'moment'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            this.initDatePicker();
        },
        props: {
            programStat: {
                type: Object
            }
        },
        data(){
            return {
                date: '',
                whenSubmitting: false,
                whileUploading: false
            }
        },
        methods: {
            uploadFiles(){
                let self = this;
                var form = document.forms.namedItem("submit-report-form"); // high importance!, here you need change "yourformname" with the name of your form
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
                        $('#submit-report-form')[0].reset();
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
            submitReport(){
                let self = this;
                self.whenSubmitting = true;
                let form = {
                    stat: self.programStat,
                    date: self.date
                };
                console.log(form)
                self.$http.post('/submitted/date', form).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        if (json.id > 0) {
                          self.whenSubmitting = false;
                          $('#modal-submit-report').modal('hide');
                          self.$emit('addsubmitteddates', json);
                          self.uploadFiles();
                        }
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
            initDatePicker(){
                let self = this;
                $(function() {
                    $('#date-submitted').datepicker()
                        .on("change", function (e) {
                        self.date = $(this).val();
                    });
                });
            }
        }
    }
</script>
