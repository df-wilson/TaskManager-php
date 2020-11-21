<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">Task List</h1>
            </div>
        </div>

        <template v-if="isCreateFormShown">
            <div class="row">
                <div class="col-12">
                  <p v-if="error" class="alert alert-danger" role="alert">{{error}}</p>
                  <span><b>Create a new task</b></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 md-6 col-lg-5">
                    <textarea v-model="description" cols=40 rows=5></textarea>
                </div>
                <div class="col-12 col-md-6 col-lg-7">
                    <label for="status" style="width: 60px">Priority:</label>
                    <select v-model="priority" name="priority">
                      <option>High</option>
                      <option>Medium</option>
                      <option>Low</option>
                    </select>
                    <br>

                   <label for="status" style="width: 60px">Status:</label>
                   <select v-model="status" name="status">
                      <option>Not Started</option>
                      <option>In Progress</option>
                      <option>Done</option>
                   </select>

                    <br>
                   <label for="due-date" style="width: 60px">Due date:</label>

                   <input type="date" id="due-date" name="due-date"
                          min="2018-07-22"
                          max="2050-12-31"
                          v-model="due_at">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-success mt-1" v-on:click="create">Add</button>
                    <button type="button" class="btn btn-success mt-1" v-on:click="closeCreateForm">Done</button>
                </div>
            </div>
        </template>

        <template v-else>
            <button type="button" class="btn btn-primary" v-on:click="showCreateForm">New</button>

            <input type="checkbox" id="done-option" v-model="showDone">
            <label for="checkbox">Show Done</label>

            <label for="sort-by" id="sort-option">Sort By:</label>
            <select v-model="sortBy" name="sort-by" v-on:change="sortOrderChanged">
               <option>Date Created</option>
               <option>Priority</option>
               <option>Date Due</option>
            </select>
        </template>

        <div class="row">
            <div class="col-12" style="margin-top: 30px">
                <div v-for="(task, index) in tasks">
                    <div class="card" v-if="showDone || task.status != 'Done'">
                      <div class="card-body">
                          <p class="task-description">{{ task.description }} ID: {{task.id}}</p>
                          <hr>
                          <p>
                             <label for="status">Status:</label>
                             <select v-model="tasks[index].status" name="status" v-on:change="onStatusChanged(index)">
                                <option>Not Started</option>
                                <option>In Progress</option>
                                <option>Done</option>
                             </select>

                              <label for="priority" style="margin-left: 20px">Priority:</label>
                              <select v-model="tasks[index].priority" name="priority" v-on:change="onPriorityChanged(index)">
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                              </select>
                          </p>
                          <p>
                              <label for="due-date">Due:</label>
                               <input type="date" id="due-date" name="due-date"
                                      min="2018-07-22"
                                      max="2050-12-31"
                                      v-bind:class="{'text-danger': isTodoDue(task.due_at)}"
                                      v-model="task.due_at"
                                      v-on:change="onDueDateChanged(index)">
                              <button type="button" v-on:click="remove(task.id)" class="btn btn-danger float-right">X</button>
                          </p>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default
    {
        mounted()
        {
            this.currentDate = new Date().toISOString().slice(0, 10);

            axios.get('/api/task')
                 .then(response => {
                    response.data.tasks.forEach((task) => {
                        this.tasks.push({
                            id: task.id,
                            description: task.description,
                            status: task.status,
                            priority: task.priority,
                            due_at: task.due_at,
                        })
                    })
                 })
        },

        data()
        {
            return {
                tasks: [],
                isCreateFormShown: false,
                showDone: false,
                sortBy: "Date Created",
                description: "",
                priority: "Medium",
                status: "Not Started",
                due_at: "",
                currentDate: "",
                error: ""
            }
        },

        methods: {
            showCreateForm: function() {
                this.isCreateFormShown = true
            },

            closeCreateForm() {
                this.description = "";
                this.priority = "Medium";
                this.status = "Not Started";
                this.due_at = "";

                this.isCreateFormShown = false;
            },

            checkInput() {
                let error = "";

                if(this.description == "") {
                    error = "Please enter a description."
                } else if (this.due_at == "") {
                    error = "Please enter a due date."
                }

                return error;
            },

            create() {
                let component = this;

                this.error = this.checkInput()
                if(this.error) {
                    return;
                }

                axios.post('/api/task',
                           {
                               description: this.description,
                               priority: this.priority,
                               status: this.status,
                               due_at: this.due_at
                           })
                     .then(function(response)
                     {
                        if(response.status == 400) {
                            component.error = response.data.msg;
                        } else {
                            component.tasks.push({
                                id: response.data.id,
                                description: component.description,
                                status: component.status,
                                priority: component.priority,
                                due_at: component.due_at
                            });

                            component.description = "";
                            component.priority = "Medium";
                            component.status = "Not Started";
                            component.due_at = "";
                        }
                     })
                     .catch(function(error)
                     {
                        console.log('Error creating task: ' + error);
                        component.error = "Invalid data.";
                     });
            },

            isTodoDue(dueDate) {
                return (dueDate < this.currentDate);
            },

            remove(id) {
                let component = this;
                axios.delete('/api/task/'+id)
                     .then(function(response)
                     {
                        if(response.status === 200) {
                            for(let i = 0; i < component.tasks.length; ++i) {
                                if(component.tasks[i].id === id) {
                                    component.tasks.splice(i,1);
                                    break;
                                }
                            }
                        }
                     });
            },

            onDueDateChanged(index) {
                console.log("In on dueDateChanged. Index is " + index);
                axios({
                   method: 'put',
                   url: '/api/task/due/'+this.tasks[index].id,
                   data: {
                      due: this.tasks[index].due_at
                   }
                })
                 .then(function(response)
                 {
                     if(response.data.msg != "ok") {
                     }
                 })
                 .catch(function(error)
                 {
                    console.log('Error updating task: ' + error);
                 });
            },

            onStatusChanged(index) {
                axios({
                   method: 'put',
                   url: '/api/task/status/'+this.tasks[index].id,
                   data: {
                      status: this.tasks[index].status
                   }
                })
                 .then(function(response)
                 {
                     if(response.data.msg != "ok") {
                     }
                 })
                 .catch(function(error)
                 {
                    console.log('Error updating task: ' + error);
                 });
            },

            onPriorityChanged(index) {
                axios({
                   method: 'put',
                   url: '/api/task/priority/'+this.tasks[index].id,
                   data: {
                      priority: this.tasks[index].priority
                   }
                })
                 .then(function(response)
                 {
                     if(response.data.msg != "ok") {
                     }
                 })
                 .catch(function(error)
                 {
                    console.log('Error updating task: ' + error);
                 });
            },

            sortByDueDate(a, b) {
                if(a.due_at > b.due_at) {
                    return 1;
                } else if (a.due_at < b.due_at) {
                    return -1;
                } else {
                    return 0;
                }
            },

            sortById(a, b) {
               return a.id - b.id;
            },

            sortByPriority(a, b) {
                let val = 0;

                if(a.priority === b.priority) {
                    val = 0;
                } else if(a.priority === "High") {
                    val = -1;
                } else if(a.priority === "Medium" &&
                          b.priority !== "High") {
                    val = -1;
                } else {
                    val = 1;
                }

                return val;
            },

            sortOrderChanged() {
                if(this.sortBy ==="Priority") {
                    this.tasks.sort(this.sortByPriority);
                } else if(this.sortBy === "Date Created") {
                    this.tasks.sort(this.sortById);
                } else if(this.sortBy === "Date Due") {
                    this.tasks.sort(this.sortByDueDate);
                } else {
                    // Unknown. Don't sort.
                }
            }
        }
    }
</script>