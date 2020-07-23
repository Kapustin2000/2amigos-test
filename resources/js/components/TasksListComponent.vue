<template>
    <div class="todoapp">
        <header class="header">
            <h1>todos</h1>
            <input
                    class="new-todo"
                    autofocus
                    autocomplete="off"
                    placeholder="What needs to be done?"
                    v-model="newTodo"
                    @keyup.enter="addTodo"
            />
        </header>
        <section class="main" v-show="todos.length">
            <ul class="todo-list">
                <li
                        v-for="todo in filteredTodos"
                        class="todo"
                        :key="todo.id"
                        :class="{ completed: todo.isComplete, editing: todo == editedTodo }"
                >
                    <div class="view">
                        <input class="toggle" @click="setIsComplete(todo)" type="checkbox" v-model="todo.isComplete" />
                        <label @dblclick="editTodo(todo)">{{ todo.name }}</label>
                        <button v-if="todo.isComplete" class="destroy" @click="removeTodo(todo)"></button>
                    </div>
                    <input
                            class="edit"
                            type="text"
                            v-model="todo.title"
                            @blur="doneEdit(todo)"
                            @keyup.enter="doneEdit(todo)"
                            @keyup.esc="cancelEdit(todo)"
                    />
                </li>
            </ul>
        </section>
        <footer class="footer" v-show="todos.length" v-cloak>
        <span class="todo-count">
          <strong>{{ remaining }}</strong> {{ remaining | pluralize }} left
        </span>
            <ul class="filters">
                <li>
                    <a @click.prevent="visibility = 'all'"   :class="{ selected: visibility == 'all' }">All</a>
                </li>
                <li>
                    <a @click.prevent="visibility = 'active'"   :class="{ selected: visibility == 'active' }"
                    >Active</a
                    >
                </li>
                <li>
                    <a

                            @click.prevent="visibility = 'completed'"
                            :class="{ selected: visibility == 'completed' }"
                    >Completed</a
                    >
                </li>
            </ul>

        </footer>
        <footer class="info">
            <p>Double-click to edit a todo</p>
        </footer>
    </div>
</template>




<script>
    var filters = {
        all: function(todos) {
            return todos;
        },
        active: function(todos) {
            return todos.filter(function(todo) {
                return !todo.isComplete;
            });
        },
        completed: function(todos) {
            return todos.filter(function(todo) {
                return todo.isComplete;
            });
        }
    };

    export default {
        data: function() {
            return {
                todos: [],
                newTodo: "",
                editedTodo: null,
                visibility: "all"
            }
        },
        created: function () {
            let vm = this;

            axios.get('/api/tasks').then(res => {
                vm.todos = res.data;
        });
        },

        computed: {
            filteredTodos: function() {
                return filters[this.visibility](this.todos);
            },
            remaining: function() {
                return filters.active(this.todos).length;
            }
        },

        filters: {
            pluralize: function(n) {
                return n === 1 ? "item" : "items";
            }
        },

        methods: {
            addTodo: function() {
                let vm = this;

                var value = this.newTodo && this.newTodo.trim();
                if (!value) {
                    return;
                }
                axios.post('/api/tasks', {name: value})
                        .then((response) => {
                    vm.todos.push(response.data);
                     vm.newTodo = '';
                 })
                .catch((error) => {
                    console.log(error);
                });
            },

            removeTodo: function(todo) {
                let vm = this;

                axios.delete('/api/tasks/'+todo.id
                ).then((response) => {
                    vm.todos.splice(vm.todos.indexOf(todo), 1);
                 })
                .catch((error) => {
                    console.log(error);
            });
             },

            editTodo: function(todo) {
                this.beforeEditCache = todo.title;
                this.editedTodo = todo;
            },
            setIsComplete: function (todo) {
                axios.put('/api/tasks/'+todo.id,
                        {
                            name: todo.name,
                            isComplete: !todo.isComplete
                        }
                );
            },
            doneEdit: function(todo) {
                if (!this.editedTodo) {
                    return;
                }
                this.editedTodo = null;
                todo.name = todo.title.trim();
                if (!todo.name) {
                    this.removeTodo(todo);
                }else{
                    axios.put('/api/tasks/'+todo.id,
                            {name: todo.name}
                    );
                }
            },

            cancelEdit: function(todo) {
                this.editedTodo = null;
                todo.title = this.beforeEditCache;
            },

            removeCompleted: function() {
                this.todos = filters.active(this.todos);
            }
        },

    };
</script>
