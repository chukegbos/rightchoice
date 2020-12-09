<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <vue-bootstrap4-table :rows="inventories" :columns="columns" :config="config" :actions="actions" @on-new="newModal" @edit="editModal" @purchase="onPurchase" @delete="deleteProduct" @click="click">
                    </vue-bootstrap4-table>
                </div>
            </div>

            <div class="modal fade" id="addNewInventory" tabindex="-1" role="dialog" aria-labelledby="addNewInventoryLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="addNewInventoryLabel"
                                v-show="!editMode"
                            >
                                Add New
                            </h5>
                            <h5
                                class="modal-title"
                                id="addNewInventoryLabel"
                                v-show="editMode"
                            >
                                Update Inventory Info
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
                        <form @submit.prevent=" editMode ? updateInventory() : createInventory()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input v-model="form.product_name" type="text" name="product_name" class="form-control" :class="{'is-invalid': form.errors.has('product_name')}"/>
                                    <has-error :form="form" field="product_name"></has-error>
                                </div>

                                <!--<div class="form-group">
                                    <label>Unit of Measurement</label>
                                    <input v-model="form.unit" type="text" name="unit" class="form-control" :class="{'is-invalid': form.errors.has('unit')}"/>
                                    <has-error :form="form" field="unit"></has-error>
                                </div>-->

                                <div class="form-group">
                                    <label>Cost Price</label>
                                    <input v-model="form.cost_price" type="number" name="cost_price" class="form-control":class="{'is-invalid': form.errors.has('cost_price')}" @change="calculate()"/>

                                    <has-error :form="form" field="cost_price"></has-error>
                                </div>
                                
                                <div class="form-group">
                                    <label>Selling Price</label>
                                    <input v-model="form.price" type="number" name="price" class="form-control":class="{'is-invalid': form.errors.has('price')}"/>
                                    <has-error :form="form" field="price"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Threshold</label>
                                    <input v-model="form.threshold" type="number" class="form-control":class="{'is-invalid': form.errors.has('threshold')}"/>
                                    <has-error :form="form" field="threshold"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Select Category</label>
                                    <b-form-select
                                        v-model="form.category"
                                        :options="categories"
                                        value-field="id"
                                        text-field="name"
                                    >
                                    <template v-slot:first>
                                        <b-form-select-option :value="null" disabled>
                                            -- Please select category--
                                        </b-form-select-option>
                                    </template>
                                    </b-form-select>
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
        data() {
            return {
                is_busy: false,
                editMode: false,
                queue: {
                    payload: '',
                },
                inventories: [],
                categories: [],
                user: "",

                form: new Form({
                    id: "",
                    product_name: "",
                    price: "",
                    category: null,
                    cost_price: "",
                    threshold: '',
                    unit: '',
                }),
                site: '',

                columns: [
                    {
                        label: "Product ID",
                        name: "product_id",
                    },

                    {
                        label: "Category",
                        name: "name",
                        sort: true,
                    },

                    {
                        label: "Name",
                        name: "product_name",
                        sort: true,
                    },

                    {
                        label: "Selling Price",
                        name: "price",
                    },

                    {
                        label: "Cost Price",
                        name: "cost_price",
                    },

                    {
                        label: "Quantity",
                        name: "quantity",
                    },

                    {
                        label: "Threshold",
                        name: "threshold",
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
                        class: "btn btn-warning btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "New Purchase",
                        event_name: "purchase",
                        class: "btn btn-info btn-sm m-1",
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
                        btn_text: "Print",
                        event_name: "click",
                        class: "btn btn-secondary btn-sm m-1",
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
                    card_title: "Company Stock"
                } 
            };
        },

        created() {
            this.loadInventory();
            this.getUser();
            this.loadSite();
        },

        components: {
            VueBootstrap4Table
        },

        methods: {
            loadSite() {
                axios.get("/api/setting")
                .then(({ data }) => {
                    this.site = data;
                });
            },

            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            editModal(payload) {
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

            click() {
                window.print();
            },

            newModal() {
                (this.editMode = false), this.form.reset();
                $("#addNewInventory").modal("show");
            },

            loadInventory() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/inventory")

                .then(({ data }) => {
                    this.inventories = data.inventories;
                    this.categories = data.categories;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            createInventory() {
                axios.post("/api/inventory", this.form)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Inventory Created Successfully.",
                        "success"
                    );
                    $("#addNewInventory").modal("hide");
                    this.loadInventory();
                    this.getUser();
                    this.loadSite();
                })

                .catch(error => {
                    console.log(error.response.data.error);
                    if (
                        error.response.data.error == "Inventory Already Exist"
                    ) {
                        Swal.fire(
                            "Failed!",
                            "Inventory Already Exist",
                            "error"
                        );
                    }
                });
            },

            updateInventory() {
                this.form.put("/api/inventory/" + this.form.id)
                .then(() => {
                    Swal.fire(
                        "Updated!",
                        "Inventory Updated Successfully.",
                        "success"
                    );
                    $("#addNewInventory").modal("hide");
                    this.loadInventory();
                    this.getUser();
                    this.loadSite();
                })

                .catch(() => {
                    this.$Progress.fail();
                });
            },

            deleteProduct(payload) {
                this.queue.payload = payload.selectedItems;
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select atleast one product to delete.",
                        "warning"
                    )
                }

                else
                {
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
                            axios.get('/api/inventory/delete', { params: this.queue})
                            .then(() => {
                                Swal.fire(
                                    "Deleted!",
                                    "Product(s) deleted.",
                                    "success"
                                );
                                this.loadInventory();
                                this.loadSite();
                                this.getUser();
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
            },
            
            calculate(){
                let a = Number(this.form.cost_price)
                this.form.price = (a * this.site.percent_gain) + a;
            },

            onPurchase(payload) {
                this.queue.payload = payload.selectedItems;
                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select atleast one product to purchase.",
                        "warning"
                    )
                }

                else
                {   
                    axios.get('/api/inventory/purchase', { params: payload})
                    .then((data) => {
                        this.$router.push({ path: '/admin/purchase/create/' + data.data });
                        
                    })

                    .catch(() => {
                        Swal.fire(
                            "Failed!",
                            "Ops, Something went wrong, try again.",
                            "warning"
                        );
                    });
                }
            },   
        },
    };
</script>
