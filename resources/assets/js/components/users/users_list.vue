<template>
    <div style="padding: 20px">
       <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Programs</th>
                </tr>
            </thead>
           <tbody>
               <tr v-for="user in users">
                   <td>{{ user.name }}</td>
                   <td>{{ (user.usertype == 'admin') ? 'Division Chief [LGMED]' : user.usertype }}</td>
                   <td>{{ user.email }}</td>
                   <td>0</td>
               </tr>
           </tbody>
       </table>
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
        }
    }
</script>
