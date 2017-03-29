<template>
    <div>
        <div class="modal fade" id="modal-submit-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 280px">
            <div class="modal-content" >
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Report Submission</h4>
              </div>
              <div class="modal-body">
                  <label>Date submitted
                     <input v-model="date" id="date-submitted" type="text" class="form-control">
                  </label>
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
                whenSubmitting: false
            }
        },
        methods: {
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
                        console.log(json)
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
