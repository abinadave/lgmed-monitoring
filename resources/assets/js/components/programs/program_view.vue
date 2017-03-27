<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component {{ $route.params.id }}</div>
                    <div class="panel-body">
                        I'm an example component!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetch();
        },
        data(){
            return {

            }
        },
        props: {
            user: {
                type: Object
            }
        },
        methods: {
            fetch(){
                let self = this;
                let resource = self.$resource('program/stats{/id}');
                let id = router.history.current.params.id;
                resource.get({
                    id: id
                }).then((resp) => {
                    if (resp.status === 200) {
                        let json = resp.body;
                        console.log(json)
                    }
                }, (resp) => {
                    if (resp.status === 422) {
                      console.log(resp);
                    }
                });
            }
        },
        watch: {
            '$route.params.id': function(newVal){
                console.log(newVal)
            }
        }
    }
</script>
