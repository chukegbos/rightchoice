<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <!--<div class="col-md-12">
                    <vue-bootstrap4-table :rows="reqs" :columns="columns" :config="config" :actions="actions" @edit="edit" @delete="deleteReq" @accept="accept">
                    </vue-bootstrap4-table>
                </div>-->

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Requested Products
                            </div>
                        </div>
                       
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Requested By</th>
                                    <th>Approved By</th>
                                    <th>Recieved By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>

                                <tr v-for="req in reqs" :key="req.id">
                                    <td>{{ req.product_name }}</td>
                                    <td>{{ req.qty }}</td>
                                    <td>{{ req.sender_id }} <br> {{ req.created_at | myDate }}</td>
                                    <td>
                                        <span v-if="req.status == 'declined'">
                                            <i class="fa fa-times text-red"></i>
                                        </span>
                                        <span v-else>
                                            {{ req.approved_by }}
                                        </span>
                                    </td>
                                    <td>
                                        <span v-if="req.status == 'declined'">
                                            <i class="fa fa-times text-red"></i>
                                        </span>
                                        
                                        <button @click="viewItems(req)" v-else-if="req.approved_by && !req.reciever_id && req.status != 'declined'" class="btn btn-info btn-sm">
                                            Accept
                                        </button>

                                        <span v-else>{{ req.reciever_id }}</span>
                                    </td>
                                    <td>
                                        <span v-if="req.status == 'approved'" class="badge badge-pills badge-success">{{ req.status | capitalize }}</span>
                                        <span v-if="req.status == 'concluded'" class="badge badge-pills badge-primary">{{ req.status | capitalize }}</span>
                                        <span v-if="req.status == 'pending' || req.status == 'No Quantity'">
                                            <span class="badge badge-pills badge-warning p-1">{{ req.status | capitalize }}</span>
                                        </span>
                                        <span v-if="req.status == 'declined'" class="badge badge-pills badge-danger">{{ req.status | capitalize }}</span>
                                    </td>
                                    <td>
                                        <span v-if="req.status == 'pending' || req.status == 'No Quantity'">
                                            <span v-if="user.role==3">
                                                <button class="btn btn-success btn-sm" @click="getItems(req)" v-if="req.status == 'pending'">
                                                    <i class="fa fa-check"></i>
                                                </button>

                                                <button class="btn btn-warning btn-sm" @click="decline(req)" v-if="req.status == 'pending'">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </span>

                                            <!--<button class="btn btn-info btn-sm" @click="editModal(req)">
                                                <i class="fa fa-edit"></i>
                                            </button>-->

                                            <button class="btn btn-danger btn-sm" @click="deleteProduct(req.id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="share" tabindex="-1" role="dialog" aria-labelledby="EditQtyLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="EditQtyLabel">
                                Move Product
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="updateQuantity()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="form.product_name" type="text" class="form-control" readonly="true" />
                                </div>
                            
                                <div class="form-group">
                                    <input v-model="form.quantity" type="number" class="form-control":class="{'is-invalid': form.errors.has('quantity')}"/>
                                    <has-error :form="form" field="quantity"></has-error>
                                </div>

                                <div class="form-group">
                                        <label>Select outlet to move from</label>
                                        <select v-model="form.store" class="form-control">
                                            <option v-for="store in stores" :value="store.id"> {{ store.name }} ({{ store.number }}) </option>
                                        </select>
                                    </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-success">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
        created() {
            this.getUser();
            this.loadInventory();
        },

        components: {
            VueBootstrap4Table
        },

        data() {
            return {
                reqs: [],
                stores: [],
                is_busy: false,
                user: '',
                columns: [
                    {
                        label: "Quantity",
                        name: "qty",
                    },

                    {
                        label: "Product",
                        name: "product_name",
                        sort: true,
                    },

                    {
                        label: "Requested By",
                        name: "sender_id",
                    },

                    {
                        label: "Approved By",
                        name: "approved_by",
                    },
                    {
                        label: "Recieved By",
                        name: "reciever_id",
                    },

                    {
                        label: "Status",
                        name: "status",
                    },
                ],

                actions: [
                    {
                        btn_text: "Accept",
                        event_name: "accept",
                        class: "btn btn-primary btn-xs m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Edit",
                        event_name: "edit",
                        class: "btn btn-warning btn-xs m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Delete",
                        event_name: "delete",
                        class: "btn btn-danger btn-xs m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },
                ],

                config: {
                    checkbox_rows: true,
                    rows_selectable: false,
                    show_reset_button: false,
                    show_refresh_button: false,
                    card_title: "Requested Products"
                },

                queue: {
                    payload: '',
                }, 

                form: new Form({
                    id: "",
                    product_name: '',
                    quantity: '',
                    store: null,
                }),
            };
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                let req_id  = this.$route.params.req_id;

                axios.get("/api/store/viewrequest/" + req_id )
                .then(({ data }) => {
                    this.reqs = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            viewItems(req) {
                axios.get("/api/store/accept/" + req.id)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Product Accepted.",
                        "success"
                    );
                    this.loadInventory();
                    this.getUser();
                })
                .catch(error => {
                    Swal.fire(
                        "Failed!",
                        "There is an error somewhere",
                        "error"
                    );
                });
            },

            edit(payload) {
                this.queue.payload = payload.selectedItems;

                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select a product to edit.",
                        "warning"
                    )
                }
                else if(payload.selectedItems.length > 1) 
                {
                    Swal.fire(
                        "Failed!",
                        "Please select just 1 product to edit.",
                        "warning"
                    )
                }
                else{
                    (this.editMode = true), this.form.reset();
                    $("#addNewInventory").modal("show");
                    this.form.category = this.queue.payload[0].category;
                    this.form.fill(this.queue.payload[0]);
                }               
            },

            deleteProduct(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                })
                .then(result => {
                    if (result.value) {
                        this.$Progress.start();
                        axios.delete("/api/store/request/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Product has been deleted.",
                                "success"
                            );
                            this.$Progress.finish();
                            this.loadInventory();
                            this.getUser();
                        })

                        .catch(() => {
                            Swal.fire(
                                "Failed!",
                                "Ops, Something went wrong, try again.",
                                "warning"
                            );
                            this.$Progress.finish();
                            this.loadInventory();
                            this.getUser();
                        });
                    }
                });
            },

            updateQuantity() {
                this.form.post("/api/admin/discharge")
                //axios.post("/api/admin/discharge/" + req.id)
                .then((data) => {
                    if(data.data.error){
                        Swal.fire(
                            "Failed!",
                            "You cannot disburse more than you dont have in outlet",
                            "error"
                        );
                    }
                    else {
                        Swal.fire(
                            "Created!",
                            "Product Disbursed.",
                            "success"
                        );
                    }

                    $("#share").modal("hide");
                    this.loadInventory();
                    this.getUser();
                })
                .catch(error => {
                    Swal.fire(
                        "Failed!",
                        "There is an error somewhere",
                        "error"
                    );
                });

            },


            decline(req) {
                axios.get("/api/admin/decline/" + req.id)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Product Rejected.",
                        "success"
                    );
                    $("#getItem").modal("hide");
                    this.loadInventory();
                    this.getUser();
                })
                .catch(error => {
                    Swal.fire(
                        "Failed!",
                        "There is an error somewhere",
                        "error"
                    );
                });
            },

            getItems(req) {
                this.form.reset();
                $("#share").modal("show");
                this.form.id = req.id;
                this.form.quantity = req.qty;
                this.form.product_name = req.product_name;

                axios.get("/api/store/transferinventory/" + req.product_id)
                .then(({ data }) => {
                    this.stores = data;
                    console.log(data)
                });
            },
        }
    };
</script>