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
                  <label>Check all: <input type="checkbox" v-model="checkAll"></label>
                  <table class="table table-bordered table-condensed table-striped">
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
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp);
                    }
                });
            },
            afterChecked(){
                let self = this;
                setTimeout(function(){
                    self.whileSavingCheckedLgu = false;
                }, 500);
            },
            evaluateResult(json){
                let self = this;
                if (json.saved.length) {
                    let models = json.saved;
                    self.$emit('checkalllgus', models);
                }
            }
        },
        watch: {
            'stat': function(newVal){
                // console.log(newVal.program_id);
            },
            'checkAll': function(newVal){
                let self = this;
                let cities = self.getCurrentLgus(self.province.id);
                self.$http.post('/checkall/checked/lgu', {
                    type: (newVal === true) ? 'check-all' : 'uncheck-all',
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
        }
    }
</script>
