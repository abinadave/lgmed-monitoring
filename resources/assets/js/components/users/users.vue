<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">LGMED Program Managers</div>

                    <div class="panel-body">
                        <!-- Nav tabs -->
                          <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Add program manager</a></li>
                            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">List of Program managers</a></li>
                          </ul>

                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home">
                                <form-program-manager @newuseradded="createNewUser"></form-program-manager>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="profile">
                                <user-list :users="users"></user-list>
                            </div>
                          </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CompForm from './create_program_manager.vue'
    import CompList from './users_list.vue'
    export default {
        mounted() {
            console.log('Component mounteds.');
            this.fetch();
        },
        data(){
            return {
                users: []
            }
        },
        methods: {
            createNewUser(respUser){
                let self = this;
                self.users.push(respUser);
            },
            fetch(){
                let self = this;
                self.$http.get('/user/management').then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.users = json.users;
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            }
        },
        components: {
            'form-program-manager': CompForm,
            'user-list': CompList
        }
    }
</script>
