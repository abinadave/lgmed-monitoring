<template>
    <div>
        <form @submit.prevent="saveUser" style="padding: 20px">
            <div v-bind:class="{ 'form-group col-md-5': true, 'has-error': error.name }">
              <label class="control-label">Name</label>
              <input v-model="form.name" type="text" class="form-control" >
            </div>
            <div v-bind:class="{ 'form-group col-md-5': true, 'has-error': error.email }">
              <label class="control-label">Email</label>
              <input v-model="form.email" type="text" class="form-control" >
            </div>
            <div v-bind:class="{ 'form-group col-md-5': true, 'has-error': error.password }">
              <label class="control-label">Password</label>
              <input v-model="form.password" type="text" class="form-control">
            </div>
            <div v-bind:class="{ 'form-group col-md-5': true, 'has-error': error.password_confirmation }">
              <label class="control-label">Confirm password</label>
              <input v-model="form.password_confirmation" type="text" class="form-control">
            </div>
            <div v-bind:class="{ 'form-group col-md-5': true, 'has-error': error.usertype }">
              <label class="control-label">Usertype (Designation)</label>
              <select v-model="form.usertype" class="form-control">
                  <option>program-manager</option>
                  <option>admin</option>
              </select>
            </div>
            <div class="col-md-8">
                <hr>
            </div>
            <div class="form-group col-md-5">
              <button class="btn btn-block btn-success">Add</button>
            </div>
        </form>
    </div>
</template>

<script>
    import alertify from 'alertify.js'
    export default {
        mounted() {
            console.log('Component mounted.');
        },
        data(){
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    usertype: ''
                },
                error: {
                    name: false,
                    email: false,
                    password: false,
                    password_confirmation: false,
                    usertype: false
                }
            }
        },
        methods: {
            saveUser(){
                let self = this;
                self.clearErrors();
                self.$http.post('/user', self.form).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        if (json.id > 0) {
                            alertify.alert("New User was added to LGMED");
                            self.clearForm();
                            self.$emit('newuseradded', json);
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
            },
            clearForm(){
                let self = this;
                $.each(self.form, function(index, val) {
                    self.form[index] = '';
                });
            },
            clearErrors(){
                let self = this;
                $.each(self.error, function(index, val) {
                    self.error[index] = false;
                });
            }
        }
    }
</script>
