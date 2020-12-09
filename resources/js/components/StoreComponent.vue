<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div v-for="error in errors" class="bg-danger">
                <ul>
                    <li>{{ error.name }}</li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <vue-bootstrap4-table :rows="stores" :columns="columns" :config="config" :actions="actions" @on-new="newModal" @edit="editModal" @trade="onTrade" @print="onPrint" @show="onShow" @target="onTarget"
                    @sale="onSale" @delete="deletestore">
                    </vue-bootstrap4-table>
                </div>
            </div>

            <div class="modal fade" id="addNewstore" tabindex="-1" role="dialog" aria-labelledby="addNewstoreLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="addNewstoreLabel"
                                v-show="!editMode"
                            >
                                Add New Outlet
                            </h5>
                            <h5
                                class="modal-title"
                                id="addNewstoreLabel"
                                v-show="editMode"
                            >
                                Update Outlet Information
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? updatestore() : createstore()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name of Outlet <span class="text-danger pulll-right">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        name="name"
                                        required
                                        class="form-control"
                                        placeholder="Outlet Name"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'name'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="name"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        name="address"
                                        class="form-control"
                                        placeholder="Address"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'address'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="address"
                                    ></has-error>
                                </div>


                                <!--<div class="form-group">
                                    <input
                                        v-model="form.target"
                                        type="number"
                                        name="target"
                                        class="form-control"
                                        placeholder="Outlet Target"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'target'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="target"
                                    ></has-error>
                                </div>-->

                                <div class="form-group">
                                    <label>Stock Limit</label>
                                    <input
                                        v-model="form.stock_limit"
                                        type="number"
                                        name="stock_limit"
                                        class="form-control"
                                        placeholder="Outlet Stock Limit"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'stock_limit'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="stock_limit"
                                    ></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button
                                    v-show="editMode"
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    Update
                                </button>
                                <button
                                    v-show="!editMode"
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" id="printMe" style="display:none">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stores</h4>
                </div>
        
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th width="400px">Address</th>
                            <th>Stock Limit</th>                        
                        </tr>

                        <tr v-for="store in stores" :key="store.id">
                            <td>{{ store.name }}</td>
                            <td>{{ store.code }}</td>
                            <td>{{ store.address }}</td>
                            <td>{{ store.stock_limit }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {

        created() {
            this.loadStores();
            this.getUser();
        },

        components: {
            VueBootstrap4Table
        },

        data() {
            return {
                is_busy: false,
                editMode: false,
                stores: [],
                user: "",
            
                errors: [],
                form: new Form({
                    id: "",
                    name: "",
                    address: "",
                    target: "",
                    stock_limit: "",
                }),

                payload: {
                    selectedItems: '',
                },

                columns: [
                    {
                        label: "Name",
                        name: "name",
                        filter: {
                            type: "simple",
                            placeholder: "Enter Name"
                        },
                        sort: true,
                    },
                    {
                        label: "Code",
                        name: "code",
                        filter: {
                            type: "simple",
                            placeholder: "Enter Code"
                        },
                        sort: true,
                    },
                    {
                        label: "Address",
                        name: "address",
                    },
                    {
                        label: "Stock Limit",
                        name: "stock_limit",
                    },
                ],

                actions: [
                    {
                        btn_text: "New",
                        event_name: "on-new",
                        class: "btn btn-primary btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Edit",
                        event_name: "edit",
                        class: "btn btn-info btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Store",
                        event_name: "trade",
                        class: "btn btn-secondary btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Show Room",
                        event_name: "show",
                        class: "btn btn-success btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Sales",
                        event_name: "sale",
                        class: "btn btn-warning btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Delete",
                        event_name: "delete",
                        class: "btn btn-danger btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Target",
                        event_name: "target",
                        class: "btn btn-secondary btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Print",
                        event_name: "print",
                        class: "btn btn-primary btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },
                ],

                config: {
                    checkbox_rows: true,
                    rows_selectable: true,
                    show_reset_button: false,
                    show_refresh_button: false,
                    card_title: "Outlets"
                } 
            };
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            loadStores() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get('/api/store')

                .then(({ data }) => {
                    this.stores = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            editModal(payload) {

                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet to edit.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 outlet to edit.",
                        "warning"
                    )
                }
                else{
                    (this.editMode = true), this.form.reset();
                    $("#addNewstore").modal("show");
                    this.form.fill(payload.selectedItems[0]);
                } 
                console.log(payload['selectedItems']);
                console.log(payload.selectedItems);        
            },

            newModal() {
                (this.editMode = false), this.form.reset();
                $("#addNewstore").modal("show");
            },

            createstore() {
                axios.post("/api/store", this.form)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Outlet Created Successfully.",
                        "success"
                    );
                    $("#addNewstore").modal("hide");
                    this.loadStores();
                })

                .catch(error => {
                    console.log(error.errors.name);
                    if (error.response.data.error == "Store Already Exist") {
                        Swal.fire("Failed!", "Store Already Exist", "error");
                    }
                    else if (error.errors.name){
                        this.errors = error.errors.name;
                        console.log(this.errors)
                    }
                    $("#addNewstore").modal("hide");
                });
            },

            updatestore() {
                this.payload.selectedItems = [];
                this.form.put("/api/store/" + this.form.id)
                .then(() => {
                    Swal.fire(
                        "Updated!",
                        "Outlet Updated Successfully.",
                        "success"
                    );
                    $("#addNewstore").modal("hide");
                    this.loadStores();
                    
                })

                .catch(() => {
                    this.$Progress.fail();
                });
            },

            onProduct(store) {
                this.$router.push({ path: "/store/inventory/" + store.code });
            },

            onTrade(payload) {
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 outlet.",
                        "warning"
                    )
                }
                else
                {
                    this.$router.push({ path: "/store/trade/" + payload.selectedItems[0].code });
                }
            },

            onShow(payload) {
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 outlet.",
                        "warning"
                    )
                }
                else
                {
                    this.$router.push({ path: "/store/room/" + payload.selectedItems[0].code });
                }
            },

            onTarget(payload) {
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 outlet.",
                        "warning"
                    )
                }
                else
                {
                    this.$router.push({ path: "/store/target/" + payload.selectedItems[0].code });
                }
            },

            onPrint(payload) {
                this.$htmlToPaper('printMe');
            },

            onSale(payload) {
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 outlet.",
                        "warning"
                    )
                }
                else
                {
                    this.$router.push({ path: "/store/report/" + payload.selectedItems[0].code });
                }
            },

            deletestore(payload) {
                console.log(payload);
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select an outlet.",
                        "warning"
                    )
                }

                else
                {

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this, also note that you cannot delete the default outlet!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    })
                    .then(result => {
                        if (result.value) {
                            axios.get('/api/store/delete', { params: payload})
                            .then(() => {
                                this.loadStores();
                                Swal.fire(
                                    "Deleted!",
                                    "Outlets(s) deleted.",
                                    "success"
                                );
                                
                            })

                            .catch(() => {
                                Swal.fire(
                                    "Failed!",
                                    "Ops, Something went wrong, try again.",
                                    "warning"
                                );
                            });
                        }
                    });
                }
            }
        }
    };
</script>
