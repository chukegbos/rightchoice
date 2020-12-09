<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container pt-1">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Targets
                    </div>

                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" @click="newModal">
                            Add New
                        </button>

                    </div>
                </div>

                <div v-if="targets.data.length">
                    <div class="card-body table-responsive p-0" id="printMe">
                        <table class="table table-hover">
                            <tr>
                                <th>Amount ({{ nairaSign }})</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Accomplished ({{ nairaSign }})</th>
                                <th v-if="user.role=='3'">Modify</th>
                            </tr>

                            <tr v-for="target in targets.data" :key="target.id">
                                <td>{{ target.target_amount }}</td>
                                <td>{{ target.start_date | myDate }}</td>
                                <td>{{ target.end_datee | myDate }}</td>
                                <td>{{ target.accomplished}}</td>
                                <td width="150px" v-if="user.role=='3'">
                                    
                                    <button class="btn btn-success btn-sm" @click="editModal(target)">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-footer">
                        <pagination :data="targets" @pagination-change-page="getResults"></pagination>
                    </div>
                </div>

                <div v-else class="p-3">
                    <div class="alert alert-danger text-center">
                        No Target.
                    </div>
                </div>

            </div>

            <div class="modal fade" id="target" tabindex="-1" role="dialog" aria-labelledby="targetLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="targetLabel" v-show="!editMode">
                                Add New
                            </h5>
                            <h5 class="modal-title" id="targetLabel" v-show="editMode">
                                Update Target's Info
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editMode ? updateTarget() : createTarget()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Amount({{ nairaSign }})</label>
                                    <input v-model="form.target_amount" type="number" class="form-control":class="{'is-invalid': form.errors.has('target_amount')}"/>
                                    <has-error :form="form" field="target_amount"></has-error>
                                </div>
                            
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="hidden" v-model="form.store_id">

                                    <input v-model="form.start_date" type="date" class="form-control" :class="{'is-invalid': form.errors.has('start_date')}"/>
                                    <has-error :form="form" field="start_date"></has-error>
                                </div>
                           
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input v-model="form.end_date" type="date" class="form-control" :class="{'is-invalid': form.errors.has('end_date')}"/>
                                    <has-error :form="form" field="end_date"></has-error>
                                </div>
                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button v-show="editMode" type="submit" class="btn btn-success">
                                    Update
                                </button>
                                <button v-show="!editMode" type="submit" class="btn btn-primary">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
        created() {
            this.loadInventory();
            this.getUser();
        },
        

        components: {
            VueBootstrap4Table
        },

        data() {
            return {
                is_busy: false,
                targets: [],
                user: "",
                editMode: false,
                model: {},
                nairaSign: "&#x20A6;",
                targets: {
                    data: '',
                },
                form: new Form({
                    id: "",
                    target_amount: "",
                    start_date: "",
                    end_date: "",
                    store_id: "",
                }),
                store: '',
            };
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            getResults(page = 1) {
                axios.get("/api/store/target/" + code + "/?page=" + page)
                .then(response => {
                    this.targets = response.data;
                    
                });
            },

            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                let code = this.$route.params.store_code; 
                axios.get('/api/store/target/' + code)

                .then(({ data }) => {
                    this.targets = data.targets;
                    this.store = data.store;
                })
                .finally(() => {
                    this.is_busy = false;
                });      
            },

            newModal() {
                (this.editMode = false), this.form.reset();
                $("#target").modal("show");
                this.form.store_id = this.store.id
            },

            createTarget() {
                if (this.is_busy) return;
                this.is_busy = true;
                console.log(this.form);
                this.form.post("/api/store/target")
                .then(() => {
                    $("#target").modal("hide");

                    Swal.fire(
                        "Created!",
                        "Target Created Successfully.",
                        "success"
                    );
                    
                    this.loadInventory();
                    this.getUser();
                })

                .catch()
                .finally(() => {
                    this.is_busy = false;
                });
            },

            updateTarget() {
                this.form.put("/api/store/target/" + this.form.id)

                .then(() => {
                    $("#target").modal("hide");
                    Swal.fire("Updated!", "Admin Updated Successfully.", "success");
                    
                    this.loadInventory();
                    this.getUser();
                })

                .catch();
            },
        }
    };
</script>