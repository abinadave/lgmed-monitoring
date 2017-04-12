<template>
    <div class="panel panel-info">
        Total Baranggays: {{ currentBrgys.length }}
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>Brgy</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="brgy in currentBrgys">
                    <td>{{ brgy.name }}</td>
                    <td>
                        <input class="squaredFour" type="checkbox" @change="changeCheckbox(brgy, $event)">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>    
<script>
    export default {
        mounted() {
           
        },
        props: {
            currentBrgys: {
                type: Array
            }
        },
        methods: {
            changeCheckbox(brgy, event){
                let self = this;
                console.log(brgy);
                let isChecked = $(event.target).is(':checked');
                if (isChecked) {
                     self.$http.post('/checked_brgy').then((resp) => {
                         if (resp.status === 200) {
                             let json = resp.body;
                             console.log(json)
                         }
                     }, (resp) => {
                         if (resp.status === 422) {
                           console.log(resp)
                         }
                     });
                }
            }
        }
    }
</script>
