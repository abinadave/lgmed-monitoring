<template>
    <div>
        <div style="margin-top: 60px" class="modal fade" id="create-report-program" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 300px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> {{ program.program_name }} reports</h4>
              </div>
              <div class="modal-body">
                    <div class="form-group">
                      <label class="control-label">Submission Date</label>
                      <input v-model="form.submission_date" id="datepicker" type="text" class="form-control input-form" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Report Freq.</label>
                      <select v-model="form.reporting_freq" class="form-control input-form">
                          <option>Monthly</option>
                          <option>Weekly</option>
                          <option>Quarterly</option>
                          <option value="others">Others</option>
                      </select>
                    </div>
                    <div class="form-group" v-show="form.reporting_freq === 'others'">
                      <label class="control-label">Enter Report type</label>
                      <input class="form-control input-form" v-model="form.others" type="text" >
                    </div>
                    <div class="form-group">
                      <label class="control-label">Status</label>
                      <select v-model="form.status" class="form-control input-form" disabled>
                          <option :value="0" selected>Not Completed</option>
                          <option :value="1">Submitted</option>
                      </select>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button @click="saveReport" type="button" class="btn btn-success" >Add report</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style type="text/css">
    .input-form {
        width: 220px;
    }
</style>
<script>
    import 'jquery-ui/themes/base/core.css';
    import 'jquery-ui/themes/base/theme.css';
    import 'jquery-ui/ui/core';
    import 'jquery-ui/ui/widgets/datepicker';
    import 'jquery-ui/themes/base/datepicker.css';
    import alertify from 'alertify.js'
    export default {
        mounted() {
            this.initDatePicker();
        },
        props: {
            program: {
                type: Object
            }
        },
        data(){
            return {
                form: {
                    submission_date: '',
                    reporting_freq: '',
                    status: 0,
                    program_id: 0,
                    others: ''
                }
            }
        },
        methods: {
            clearErrors(){
                let self = this;
                $.each(self.form, function(index, val) {
                    self.form[index] = '';
                });
            },
            saveReport(){
                let self = this;
                self.form.program_id = self.program.id;
                self.$http.post('/program/stats', self.form).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        if (json.id > 0) {
                            self.clearErrors();
                            alertify.success('report successfully created.');
                            $('#create-report-program').modal('hide');
                            self.$emit('newreportcreated', json);
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
                $( function() {
                    $( "#datepicker" ).datepicker()
                        .on("change", function (e) {
                        self.form.submission_date = $(this).val();
                    });
                });
            }
        }
    }
</script>
