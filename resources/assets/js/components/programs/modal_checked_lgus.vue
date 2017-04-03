<template>
    <div>
        <div class="modal fade" id="modal-lgus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ province.name.toUpperCase() }}</h4>
              </div>
              <div class="modal-body">
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
                              <td @click="markAsChecked(lgu)"></td>
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
            }
        },
        methods: {
            markAsChecked(lgu){
                let self = this;
                let headers = {
                    municipality_id: lgu.id,
                    province_id: self.province.id,
                    program_id: self.stat.program_id
                };
                self.$http.post('/checked/lgu', headers).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        console.log(json);
                        
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp)
                    }
                });
            }
        },
        watch: {
            'stat': function(newVal){
                console.log(newVal.program_id);
            }
        }
    }
</script>
