<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container pt-1">
            <div class="row">
                <div class="col-md-12">
                    <vue-bootstrap4-table :rows="inventories" :columns="columns" :config="config" :actions="actions" @on-cart="onCart" @on-request="onRequest" @on-move="onMove">
                    </vue-bootstrap4-table>
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
                inventories: [],
                filterForm1: {
                    store_code: '',
                },

                queue: {
                    payload: [],
                }, 

                user: "",

                columns: [
                    {
                        label: "Product ID",
                        name: "product_id",
                        sort: true,
                    },
                    {
                        label: "Product Name",
                        name: "product_name",
                        sort: true,
                    },
                    {
                        label: "Quantity",
                        name: "number",
                        sort: true,
                    },
                ],

                actions: [

                    {
                        btn_text: "Request",
                        event_name: "on-request",
                        class: "btn btn-warning btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Move to Room",
                        event_name: "on-move",
                        class: "btn btn-warning btn-sm m-1",
                        event_payload: {
                            msg: "my custom msg"
                        }
                    },

                    {
                        btn_text: "Sell",
                        event_name: "on-cart",
                        class: "btn btn-info btn-sm m-1",
                        selectedItems: {
                            selectedItems: this.selectedItems
                        }
                    },
                ],

                config: {
                    checkbox_rows: true,
                    rows_selectable: true,
                    show_reset_button: false,
                    show_refresh_button: false,
                    card_title: "Store Products"
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

            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                this.filterForm1.store_code = this.$route.params.store_code;

                if(this.filterForm1.store_code ) {
                    
                    axios.get('/api/store/tradeinventory', { params: this.filterForm1})

                    .then(({ data }) => {
                        this.inventories = data;
                        console.log(this.inventories);
                    })
                    .finally(() => {
                        this.is_busy = false;
                    });
                }
                else
                {
                    axios.get("/api/store/tradeinventory")
                    .then(({ data }) => {
                        this.inventories = data;
                    })
                    .finally(() => {
                        this.is_busy = false;
                    });
                }       
            },

            onCart(payload) {
                this.queue.payload = payload.selectedItems;
                if(payload.selectedItems.length>0){
                    Swal.fire({
                        title: "Are you sure you want to proceed?",
                        text: "NB: Products without any quantity will not be added!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Proceed!"
                    })

                    .then(result => {
                        if (result.value) {
                            axios.get('/api/cart/addCart', { params: this.queue})

                            .then(({ data }) => {
                                this.$router.push({ path: "/sale/shopping-cart"});
                            });
                        }
                    });
                }
                else
                {
                    this.$router.push({ path: "/sale/shopping-cart"});
                }         
            },

            onRequest(payload) {
                this.queue.payload = payload.selectedItems;

                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select atleast one product.",
                        "warning"
                    )
                }
                else
                {
                    axios.get('/api/store/makerequest', { params: this.queue})

                    .then(({ data }) => {
                        this.$router.push({ path: "/store/request/create/" + data.req_id });
                    });
                }
            },

            onMove(payload) {
                this.queue.payload = payload.selectedItems;

                if(payload.selectedItems.length==0)
                {
                    Swal.fire(
                        "Failed!",
                        "Please select atleast one product.",
                        "warning"
                    )
                }
                else
                {
                    axios.get('/api/store/moverequest', { params: this.queue})

                    .then(({ data }) => {
                        this.$router.push({ path: "/store/move/create/" + data.ref_id });
                    });
                }
            },

            addToCart(inventory){
                axios.get('/api/cart/addCart/'+inventory.id)
                .then(({ data }) => {
                    this.$router.go({path: '/store/inventory'})
                });
            },
        }
    };
</script>