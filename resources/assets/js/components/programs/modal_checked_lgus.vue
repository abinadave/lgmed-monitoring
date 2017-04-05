<template>
    <div>
        <div class="modal fade" id="modal-lgus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                {{ province.name.toUpperCase() }} {{ lgus.length }}</h4>
              </div>
              <div class="modal-body">
                  <button @click="checkOrUncheck('check-all')" class="btn btn-success btn-xs">Check all <i class="fa fa-check" aria-hidden="true"></i></button>
                  <button @click="checkOrUncheck('uncheck-all')" class="btn btn-danger btn-xs">Un Check all <i class="fa fa-remove" aria-hidden="true"></i></button><br>
                  <table class="table table-bordered table-condensed table-striped" style="margin-top: 10px">
                      <thead>
                          <tr>
                              <th>City / Municipality</th>
                              <th width="50">Submitted</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="lgu in filteredLgus">
                              <td>{{ lgu.name }}</td>
                              <td class="text-center">
                                 <input :disabled="whileSavingCheckedLgu" @change="markAsChecked(lgu, $event)" type="checkbox" :checked="findCheckedLgu(lgu) === true">
                              </td>
                          </tr>
                      </tbody>
                  </table>
                  
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
    export default {
        mounted() {
            console.log('Component mounted.');
        },
        props: {
            provinces: {
                type: Array
            },
            filteredLgus: {
                type: Array
            },
            province: {
                type: Object
            },
            stat: {
                type: Object
            },
            program: {
                type: Object
            },
            checkedLgus: {
                type: Array
            },
            lgus: {
                type: Array
            }
        },
        data(){
            return {
                whileSavingCheckedLgu: false,
                checkAll: false
            }
        },
        methods: {
            markAllAsCheck(){
                let self = this;
                // self.checkOrUncheck('check-all')
            },
            getCurrentLgus(pid){
                let self = this;
                return self.lgus.filter(function(index) {
                    return Number(index.province_id) === Number(pid);
                });
            },
            findCheckedLgu(lgu){
                let self = this;
                let rs = _.filter(self.checkedLgus, {municipality_id: lgu.id});
                return (rs.length > 0) ? true : false;
            },
            getHeaders(lgu){
                let self = this;
                return {
                      municipality_id: lgu.id,
                      province_id: self.province.id,
                      program_id: self.stat.program_id,
                      program_stat_id: self.stat.id
                };
            },
            markAsChecked(lgu, event){
                let self = this;
                let is = $(event.target).is(':checked');
                let headers = {
                    municipality_id: lgu.id,
                    province_id: self.province.id,
                    program_id: self.stat.program_id,
                    program_stat_id: self.stat.id
                };
                self.whileSavingCheckedLgu = true;
                self.$http.post('/checked/lgu', headers).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        if (json.existed === true) {
                            /* remove */
                            let rs = _.filter(self.checkedLgus, { municipality_id: lgu.id});
                            if (rs.length) {
                                let index = self.checkedLgus.findIndex(checked => checked.municipality_id === lgu.id);
                                self.checkedLgus.splice(index , 1);
                                self.$emit('removecheckedlgu', rs[0]);
                            }
                            this.afterChecked();
                        }else {
                            /* add */ 
                            let model = json.checked_lgu;
                            self.checkedLgus.push(model);
                            self.$emit('addcheckedlgu', model);
                            this.afterChecked();
                        }
                        this.afterChecked();
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp);
                      this.afterChecked();
                    }
                });
            },
            afterChecked(){
                let self = this;
                setTimeout(function(){
                    self.whileSavingCheckedLgu = false;
                }, 700);
            },
            evaluateResult(json){
                let self = this;
                if (json.saved.length) {
                    let models = json.saved;
                    self.$emit('checkalllgus', models);
                }else if(json.deleted.length) {
                    // alert(json.deleted)
                    let models = json.deleted;
                    self.$emit('unchecklgus', models);
                }
            },
            deleteExistingCheckedLgu(models){
                let self = this;
                self.$http.post('/delete_existing_checked_lgu', {
                    models: models
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        console.log(json);
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            },
            checkOrUncheck(type){
                let self = this;
                let cities = self.getCurrentLgus(self.province.id);
                if (self.checkedLgus.length > 0 && cities.length !== self.checkedLgus.length) {
                    if (type === 'check-all') {
                        let models = self.checkedLgus;
                        self.deleteExistingCheckedLgu(models);
                    }else {
                        self.$http.post('remove/checked/lgu', {
                            checked_lgu: self.checkedLgus
                        }).then((resp) => {
                            if (resp.status === 200) {
                                let json = resp.body;
                                let models = json.models;
                                self.$emit('checkedlguremove', models);
                            }
                        }, (resp) => {
                            console.log(resp);
                        });
                    }
                }else {
                    self.SaveOrDelete(type, cities);
                }
            },
            SaveOrDelete(type, cities){
                let self = this;
                self.$http.post('/checkall/checked/lgu', {
                    type: type,
                    province_id: self.province.id,
                    program_id: self.stat.program_id,
                    program_stat_id: self.stat.id,
                    cities: cities
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        self.evaluateResult(json);
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            }
        },
        watch: {
           
        }
    }
</script>
