<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container pt-3">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <b-button variant="outline-primary" size="sm" v-b-modal.filter-modal><i class="fa fa-filter"></i> Filter</b-button>
                    <b-modal id="filter-modal" ref="filter" title="Filter" hide-footer>
                        <b-form @submit.stop.prevent="onFilterSubmit">
                            <b-form-group label="Name" label-for="keyword">
                                <b-form-input id="name" v-model="filterForm.name" type="text"></b-form-input>
                            </b-form-group>
                            <b-button type="submit" variant="primary">Search</b-button>
                        </b-form>
                    </b-modal>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Products
                            </div>
                        </div>
                      
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Selling Price (&#8358;)</th>
                                    <th>Cost Price (&#8358;)</th>
                                    <th v-if="user.role!=1">Quantity</th>
                                    <th>Threshold</th>
                                    <th>Action</th>
                                </tr>

                                <tr v-for="inventory in inventories.data" :key="inventory.id">
                                    <td>{{ inventory.product_name }}</td>
                                    <td>{{ inventory.price }}</td>
                                    <td>{{ inventory.cost_price }}</td>
                                    <td v-if="user.role!=1">
                                        <span v-if="inventory.number<=0">0</span>
                                        <span v-else>{{ inventory.number }}</span>
                                    </td>  
                                    <td>{{ inventory.threshold }}</td>

                                    <td>
                                        <span v-if="inventory.number<=0" class="text-red">Out of Stock</span>

                                        <a href="#" v-else  @click=addToCart(inventory)>
                                            <i class="fa fa-shopping-cart text-blue fa-2x"></i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <pagination :data="inventories" @pagination-change-page="getResults"></pagination>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    export default {
        created() {
            this.loadInventory();
            this.getUser();
        },

        data() {
            return {
                is_busy: false,
                editMode: false,
                addMode: false,
                model: {},

                inventories: {},
                form: new Form({
                    id: "",
                    product_name: "",
                    price: "",
                    quantity: ""
                }),

                filterForm: {
                    name: '',
                }, 

                filterForm1: {
                    store_code: '',
                }, 
                user: "",           
            };
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            onFilterSubmit()
            {
                this.loadInventory();
                this.getUser();
                this.$refs.filter.hide();
            },

            getResults(page = 1) {
                axios.get('/api/store/inventory?page=' + page)
                .then(response => {
                    this.inventories = response.data;
                });
            },

            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                this.filterForm1.store_code = this.$route.params.store_code;

                if(this.filterForm1.store_code ) {
                    
                    axios.get('/api/store/inventory', { params: this.filterForm1})

                    .then(({ data }) => {
                        this.inventories = data;
                    })
                    .finally(() => {
                        this.is_busy = false;
                    });
                }
                else
                {
                    axios.get("/api/store/inventory", { params: this.filterForm })
                    .then(({ data }) => {
                        this.inventories = data;
                    })
                    .finally(() => {
                        this.is_busy = false;
                    });
                }       
            },

            addToCart(inventory){
                axios.get('/api/cart/addCart/'+inventory.id)
                .then(({ data }) => {
                    this.$router.go({path: '/store/inventory'})
                });
            }
        }
    };
</script>
