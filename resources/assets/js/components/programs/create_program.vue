<template>
    <div>
        <div style="margin-top: 150px; " class="modal fade" id="modal-create-program" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 320px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add new LGMED Program</h4>
              </div>
              <div class="modal-body">
                   <form>
                        <div style="width: 250px" v-bind:class="{'has-error': error.program_name }">
                           <label class="control-label">Program Name</label>
                           <input @keyup.enter="createProgram" v-model="form.program_name" type="text" class="form-control" >
                        </div>
                        <div style="width: 250px" v-bind:class="{'has-error': error.program_manager }">
                           <label class="control-label">Program Manager</label>
                           <input  type="text" class="form-control" :value="user.name" disabled>
                        </div>
                        <br>
                   </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button :disabled="whileSaving" @click="createProgram" type="button" class="btn btn-primary">
                  <span v-if="whileSaving">
                     Please Wait....
                     <i class="fa fa-spinner fa-pulse fa-fw"></i>
                     <span class="sr-only">Loading...</span>
                  </span>
                  <span v-else>
                      Create Program
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import alertify from 'alertify.js'
    export default {
        props: {
            users: {
                type: Array
            },
            user: {
                type: Object
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        data(){
            return {
                whileSaving: false,
                form: {
                    program_name: '',
                    program_manager: 0
                },
                error: {
                    program_name: false,
                    program_manager: false
                }
            }
        },
        methods: {
            clearErrors(){
                let self = this;
                $.each(self.error, function(index, val) {
                    self.error[index] = false;
                });
            },
            clearForm(){
                let self = this;
                $.each(self.form, function(index, val) {
                   self.form[index] = '';
                });
            },
            createProgram(){
                let self = this;
                self.clearErrors();
                self.whileSaving = true;
                self.form.program_manager = self.user.id;
                self.$http.post('/program', self.form).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        alertify.success('Program: ['+json.program_name.toUpperCase() + '] successfully created.');
                        setTimeout(function(){
                           self.whileSaving = false;
                           self.clearForm();
                           $('#modal-create-program').modal('hide');
                        }, 700);
                        if (json.id > 0) {
                            self.$emit('newprogram', json);
                        }
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                        let json = resp.body;
                        $.each(json, function(index, val) {
                            self.error[index] = true;
                        });
                    }
                });
            }
        }
    }
</script>
