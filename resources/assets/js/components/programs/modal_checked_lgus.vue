<template>
    <div>
        <div class="modal fade" id="modal-lgus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 60%">
            <div class="modal-content" >
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                {{ province.name.toUpperCase() }} {{ brgys.length }}</h4>
              </div>
              <div class="modal-body">
                  <button @click="checkOrUncheck('check-all')" class="btn btn-success btn-xs">Check all <i class="fa fa-check" aria-hidden="true"></i></button>
                  <button @click="checkOrUncheck('uncheck-all')" class="btn btn-danger btn-xs">Un Check all <i class="fa fa-remove" aria-hidden="true"></i></button><br><br>
                  <div class="col-md-5" style="overflow: auto; height: 500px">
                      <table class="table table-bordered table-condensed table-striped" style="margin-top: 10p">
                          <thead>
                              <tr>
                                  <th>City / Municipality</th>
                                  <th width="50">Submitted</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-for="lgu in filteredLgus" v-show="user.usertype === 'program-manager'">
                                  <td><a @click="showBrgys(lgu)" style="cursor: pointer">{{ lgu.name.toUpperCase() }}</a></td>
                                  <td class="text-center">
                                     <input :disabled="whileSavingCheckedLgu" @change="markAsChecked(lgu, $event)" type="checkbox" :checked="findCheckedLgu(lgu) === true">
                                  </td>
                              </tr>
                              <tr v-for="lgu in filteredLgus" v-show="user.usertype === 'admin'">
                                  <td><a style="cursor: pointer">{{ lgu.name.toUpperCase() }}</a></td>
                                  <td class="text-center">
                                     <span v-show="findCheckedLgu(lgu) === true">
                                         <i class="fa fa-check" aria-hidden="true"></i>
                                     </span>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="col-md-6">
                      <subview-brgy :current-brgys="currentBrgys"></subview-brgy>
                  </div>
              </div>
              <div class="modal-footer" style="border-color: transparent;">
                  <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import CompSubviewCheckedBrgy from '../brgy/subview_checked_brgy.vue'

    export default {
        components: {
            'subview-brgy': CompSubviewCheckedBrgy
        },
        mounted() {
            let self = this;
            console.log('Component mounted.');
            $('#modal-lgus').on('hidden.bs.modal', function (e) {
                // self.currentBrgys = [];
            })
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
            },
            user: {
                type: Object
            },
            brgys: {
                type: Array
            }
        },
        data(){
            return {
                whileSavingCheckedLgu: false,
                checkAll: false,
                currentBrgys: []
            }
        },
        methods: {
            showBrgys(lgu){
                let self = this;
                let rs = _.filter(self.brgys, { municipality_id: lgu.id });
                // console.log(rs);
                self.currentBrgys = rs;
            },
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
                // self.$emit('fetchmyreports');
                if (json.saved.length) {
                    let models = json.saved;
                    // self.$emit('checkalllgus', models);
                    let first = _.first(json.saved);
                    self.$emit('refetchmymodels', first.program_id);
                    console.log(first)
                }else if(json.deleted.length) {
                    let models = json.deleted;
                    // self.$emit('unchecklgus', models);
                    let first = _.first(json.deleted);
                    self.$emit('refetchmymodels', first.program_id);
                    console.log(first)
                }
            },
            deleteExistingCheckedLgu(models){
                let self = this;
                let munIds = _.map(models, 'municipality_id');
                let cities = self.getCurrentLgus(self.province.id);
                let diffArr = _.difference(_.map(cities, 'id'), munIds);
                let rs = [];
                let model = {};
                if (diffArr.length) {
                    self.$http.post('create_checked_lgu', {
                        mud_ids: diffArr,
                        province_id: self.province.id,
                        program_id: self.stat.program_id,
                        program_stat_id: self.stat.id
                    }).then((resp) => {
                        if (resp.status === 200) {
                            let json = resp.body;
                            json.saved.forEach(function(model){
                                self.$emit('addcheckedlgu', model);
                            }); 
                       }
                    }, (resp) => {
                        console.log(resp);
                    });
                }else {
                    alert('No municipality_id was found');
                }
                // self.$http.post('/delete_existing_checked_lgu', {
                //     models: models
                // }).then((resp) => {
                //     if (resp.status === 200) {
                //         let json = resp.body;
                //         console.log(json);
                //     }
                // }, (resp) => {
                //     if (resp.status === 422) {
                //       console.log(resp)
                //     }
                // });
            },
            checkOrUncheck(type){
                let self = this;
                // alert(1);
                let cities = self.getCurrentLgus(self.province.id);
                if (self.checkedLgus.length > 0 && cities.length !== self.checkedLgus.length) {
                    if (type === 'check-all') {
                        let models = self.checkedLgus;
                        self.deleteExistingCheckedLgu(models);
                    }else {
                        self.$http.post('remove_checked_lgu', {
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
                // alert(type);
                self.$http.post('/checkall_checked_lgu', {
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
