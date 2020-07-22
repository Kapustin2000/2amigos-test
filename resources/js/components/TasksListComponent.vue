<template>
    <div class="container">
        <form v-if="activeTask">
              <p>Edit task</p>
              <input name="name" v-model="activeTask.name">
              <input type="checkbox" name="isComplete" v-model="activeTask.isComplete">
              <button @click.prevent="updateTask">update</button>
        </form>
        <form v-else>
            <p>Create task</p>
            <input name="name"  >
            <input type="checkbox" name="isComplete"  >
            <button @click.prevent="update">update</button>
        </form>

        <br><br><br>
        <ul>
            <li v-if="tasks.length > 0" v-for="task in tasks">
                <span>{{task.name}}</span> <span @click="editTask(task)">Edit</span> <span @click="deleteTask(task)" v-if="task.isComplete">Delete</span>
            </li>
            <li v-else>No tasks yet</li>
        </ul>
    </div>
</template>

<script>
    export default {


        data: function () {
            return {
                tasks : [],
                activeTask: false
            }
        },
        mounted() {

        },
        created: function () {
            let vm = this;

            axios.get('/api/tasks').then(res => {
                vm.tasks = res.data;
            });
        },
        methods: {
           editTask(task){
               this.activeTask = task;
           },
           updateTask(){
               console.log(this.activeTask);
           },
           deleteTask(task){
               console.log(task);
           }
        },
        watch: {

        },
        computed: {

        }
    }
</script>
