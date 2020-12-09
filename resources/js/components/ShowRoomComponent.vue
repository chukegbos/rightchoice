<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container pt-1">
            <div class="row">
                <div class="col-md-12">
                    <vue-bootstrap4-table :rows="inventories" :columns="columns" :config="config" :actions="actions" @on-move="onMove">
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
                        filter: {
                            type: "simple",
                            placeholder: "Enter Product Id"
                        },
                        sort: true,
                    },
                    {
                        label: "Product Name",
                        name: "product_name",
                        filter: {
                            type: "simple",
                            placeholder: "Enter Product Name"
                        },
                        sort: true,
                    },
                    {
                        label: "Quantity",
                        name: "number",
                        sort: false,
                    }
                ],

                actions: [
                    {
                        btn_text: "Move to store",
                        event_name: "on-move",
                        class: "btn btn-warning btn-sm m-1",
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
                    card_title: "Show Room"
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
                    
                    axios.get('/api/store/roominventory', { params: this.filterForm1})

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
                    axios.get("/api/store/roominventory")
                    .then(({ data }) => {
                        this.inventories = data;
                    })
                    .finally(() => {
                        this.is_busy = false;
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
                    axios.get('/api/store/moveback', { params: this.queue})

                    .then(({ data }) => {
                        this.$router.push({ path: "/store/moveback/create/" + data.ref_id });
                    });
                }
            },
        }
    };
</script>