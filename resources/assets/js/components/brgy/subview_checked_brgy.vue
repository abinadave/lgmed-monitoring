<template>

    <div class="panel panel-info" v-show="currentBrgys.length">
        

        <h4 class="text-center">{{ currentMunicipality.name.toUpperCase() }} ({{ currentCheckedBrgys.length }}) </h4>
        <table class="table table-bordered table-striped table-condensed table-brgy" style="font-size: 11px">
            <thead>
                <tr>
                    <th>Brgy</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="brgy in currentBrgys">
                    <td>{{ brgy.name }}</td>
                    <td class="text-center">
                        <input v-bind:id="stat.id + '-' + brgy.id" class="squaredFour" type="checkbox" @change="changeCheckbox(brgy, $event)">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>  
<style type="text/css">
    table-brgy td {
        padding: 0px;
    }
</style>  
<script>
    export default {
        mounted() {
           
        },
        props: {
            currentBrgys: {
                type: Array
            },
            stat: {
                type: Object
            },
            currentCheckedBrgys: {
                type: Array
            },
            currentMunicipality: {
                type: Object
            },
            checkedBrgys: {
                /* actual data from db */
                type: Array
            },
            checkAllCurrentBrgy: {
                type: String
            }
        },
        methods: {
            changeCheckbox(brgy, event){
                let self = this;
                console.log(brgy);
                let isChecked = $(event.target).is(':checked');
                // alert(isChecked);
                self.$http.post('/fetch_checked_brgy', {
                    program_stat_id: self.stat.id,
                    municipality_id: brgy.municipality_id,
                    brgy_id: brgy.id,
                    is_checked: (isChecked === true) ? 'save' : 'delete'
                 }).then((resp) => {
                     if (resp.status === 200) {
                         let json = resp.body;
                         if (json.type === 'save') {
                             let modelSaved = json.saved;    
                             self.$emit('addcheckedbrgy', modelSaved);         
                         }else {
                             let modelDeleted = json.deleted_model;
                             self.$emit('removecheckedbrgy', modelDeleted); 
                         }
                     }
                 }, (resp) => {
                     if (resp.status === 422) {
                        console.log(resp);
                     }
                 });
            }
        },
        watch: {
            'currentCheckedBrgys': function(checkedBrgys){
                let self = this;
                setTimeout(function(){
                    checkedBrgys.forEach(function(model){
                        $('#'+ self.stat.id + '-' + model.brgy_id).prop('checked', true);
                    });
                    if (checkedBrgys.length === 0) {
                        $('.table-brgy').find(':checkbox').prop('checked', false);
                    }
                }, 500);
            },
            'currentBrgys': function(){
                $('.table-brgy').find('input:checkbox').prop('checked', false);
            },
            'checkAllCurrentBrgy': function(newVal){
                let self = this;
                // alert(newVal)
                let headers = {
                    program_stat_id: self.stat.id,
                    municipality_id: self.currentMunicipality.id,
                    type: newVal
                };
                self.$http.post('/check_of_uncheck_all_checked_brgys', headers).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        if (json.type === 'checkall') {
                            self.$emit('refreshcurrentcheckedbrys', json.saved_models);
                        }else {
                            self.$emit('removeuncheckedbrgys', json.deleted_models);
                        }
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
