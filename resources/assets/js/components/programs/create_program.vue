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
                          <input v-model="form.program_name" type="text" class="form-control" >
                        </div>
                        <div style="width: 250px" v-bind:class="{'has-error': error.program_manager }">
                          <label class="control-label">Program Manager</label>
                          <select v-model="form.program_manager" class="form-control">
                              <option :value="0">Select Program Manager</option>
                              <option :value="user.id" v-for="user in users">
                                  {{ user.name }}
                              </option>
                          </select>
                        </div>
                        <br>
                   </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button @click="createProgram" type="button" class="btn btn-primary">Create Program</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            users: {
                type: Array
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        data(){
            return {
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
            createProgram(){
                let self = this;
                self.clearErrors();
                self.$http.post('/program', self.form).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        console.log(json);
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
