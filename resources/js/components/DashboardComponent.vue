<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container mt-3">
            <div class="p-1">
                <h4>Dashboard</h4>
            </div>

            <span v-if="user.role == 1">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-green p-3">
                                <span class="info-box-icon">
                                    <i class="fa fa-users"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Registered Customers</span>
                                    <span class="info-box-number">
                                        {{ stat.customers }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-red p-3">
                                <span class="info-box-icon"><i class="fas fa-money"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Debtors</span>
                                    <span class="info-box-number">
                                        {{ stat.debtors }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div v-if="report_items.data.length" style="text-align:center">
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <tr>
                                                <th>Sale ID</th>
                                                <th>Store</th>
                                                <th>Sales Rep</th>
                                                <th>Mode of Payment</th>
                                                <th>Est Amount (Naira)</th>
                                                <th>Discount(%)</th>
                                                <th>Amount Discounted (Naira)</th>
                                                <th>Total Price (Naira)</th>
                                                <th>Amount Paid (Naira)</th>
                                                <th>Date</th>
                                            </tr>

                                            <tr
                                                v-for="order in report_items.data"
                                                :key="order.id"
                                            >
                                                <td>
                                                    {{ order.sale_id }} <br>
                                                    <a href="#" @click=viewItems(order)>View</a>
                                                </td>
                                                <td>{{ order.store_name }}</td>
                                                <td>{{ order.user_name }}</td>
                                                <td>{{ order.mop | capitalize }}</td>
                                                <td>{{ order.initialPrice  }}</td>
                                                <td>{{ order.percent_discount  }}</td>
                                                <td>{{ order.discount  }}</td>
                                                <td>{{ order.totalPrice  }}</td>
                                                <td>{{ order.amount_paid  }}</td>
                                                <td>{{ order.created_at | myDate }}<br>{{ order.created_at }}</td> 
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div 
                            class="modal fade" 
                            id="getItem" 
                            tabindex="-1" 
                            role="dialog" 
                            aria-labelledby="addNewFridgeLabel" 
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addNewFridgeLabel">Items</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">                         
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Unit Price (Naira)</th>
                                                        <th>Price (Naira)</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr v-for="item in items" :key="item.id">
                                                        <td>{{ item.item.product_name}}</td>
                                                        <td>
                                                            {{ item.quantity }}
                                                        </td>
                                                        <td>{{ item.item.price}}</td>
                                                        <td>{{ item.price }}</td>
                                                    </tr>                                
                                                </tbody>

                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total Price</th>
                                                        <th><span class="vd_green font-sm font-normal"> N{{ totalPrice }}</span></th>
                                                    </tr>
                                                </tfoot>

                                            </table>  
                                        </div>                           
                                    </div>
                                  
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </span>

            <span v-else-if="user.role == 2">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon">
                                    <i class="fa fa-user"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Cashiers</span>
                                    <span class="info-box-number">
                                        {{ stat.users }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon">
                                    <i class="fa fa-users"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Registered Customers</span>
                                    <span class="info-box-number">
                                        {{ stat.customers }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon"><i class="fas fa-money"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Debtors</span>
                                    <span class="info-box-number">
                                        {{ stat.debtors }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Credit</span>
                                    <span class="info-box-number">{{ query_credit }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Debit</span>
                                    <span class="info-box-number">{{ query_debit }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-2">
                                <span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Balance</span>
                                    <span class="info-box-number">{{ query_credit - query_debit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Notifications
                                    </h3>
                                </div>
                            
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 p-2">
                                            <p v-if="stat.store_threshold_inventories.length > 0">
                                                <span class="text-info">Some products in your store have reached threshold <br></span>
                                             
                                                <span v-for="product in stat.store_threshold_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>

                                            <p v-if="stat.store_zero_inventories.length > 0">
                                                <span class="text-muted">Some products in your store have exhausted <br></span>
                                             
                                                <span v-for="product in stat.store_zero_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </span>

            <span v-else-if="user.role == 3">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon">
                                    <i class="fa fa-user"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Members</span>
                                    <span class="info-box-number">
                                        {{ stat.users }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon">
                                    <i class="fa fa-users"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Registered Customers</span>
                                    <span class="info-box-number">
                                        {{ stat.customers }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon"
                                    ><i class="fas fa-box"></i
                                ></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Stores</span>
                                    <span class="info-box-number">
                                        {{ stat.stores }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon"
                                    ><i class="fa fa-shopping-cart"></i
                                ></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Category</span>
                                    <span class="info-box-number">{{
                                        stat.categories
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon"
                                    ><i class="fa fa-product-hunt"></i
                                ></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Inventory</span>
                                    <span class="info-box-number">{{
                                        stat.inventories
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box bg-blue p-3">
                                <span class="info-box-icon"><i class="fas fa-money"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Debtors</span>
                                    <span class="info-box-number">
                                        {{ stat.debtors }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <b-row>
                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/stores">
                                                    <i class="fa fa-home fa-2x"></i><br>
                                                    <span class="text-black routerText">Stores</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/customers">
                                                    <i class="fa fa-users fa-2x"></i><br>
                                                    <span class="text-black routerText">Customers</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/inventory">
                                                    <i class="fa fa-shopping-basket fa-2x"></i><br>
                                                    <span class="text-black routerText">Company Stock</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/inventory/category">
                                                    <i class="fa fa-gift fa-2x"></i><br>
                                                    <span class="text-black routerText">Product Category</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/store/request">
                                                    <i class="fa fa-upload fa-2x"></i><br>
                                                    <span class="text-black routerText">Product Request</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/purchases">
                                                    <i class="fa fa-cart-plus fa-2x"></i><br>
                                                    <span class="text-black routerText">Purchases</span>
                                                </router-link>
                                            </div>
                                        </b-col>



                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/cashiers">
                                                    <i class="fa fa-user fa-2x"></i><br>
                                                    <span class="text-black routerText">Cashiers</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/account/expenditure">
                                                    <i class="fa fa-sitemap fa-2x"></i><br>
                                                    <span class="text-black routerText">Expenditures</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/account/balance">
                                                    <i class="fa fa-book fa-2x"></i><br>
                                                    <span class="text-black routerText">Balance Sheet</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/report">
                                                    <i class="fa fa-shopping-cart fa-2x"></i><br>
                                                    <span class="text-black routerText">Orders</span>
                                                </router-link>
                                            </div>
                                        </b-col>

                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/store/debtors">
                                                    <i class="fa fa-question fa-2x"></i><br>
                                                    <span class="text-black routerText">Debtors</span>
                                                </router-link>
                                            </div>
                                        </b-col>


                                        <b-col cols="4" md="2">
                                            <div class="text-center hover margin-bottom">
                                                <router-link to="/admin/setting">
                                                    <i class="fa fa-cogs fa-2x"></i><br>
                                                    <span class="text-black routerText">Settings</span>
                                                </router-link>
                                            </div>
                                        </b-col>
                                    </b-row>
                                </div>
                            </div>
                            <!--<div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Notifications
                                    </h3>
                                </div>
                            
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 p-2">
                                            <p v-if="stat.threshold_inventories.length > 0">
                                                <span class="text-primary">Some products in the general warehouse have reached threshold <br></span>
                                             
                                                <span v-for="product in stat.threshold_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>

                                            <p v-if="stat.zero_inventories.length > 0">
                                                <span class="text-danger">Some products in the general warehouse have exhausted <br></span>
                                             
                                                <span v-for="product in stat.zero_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>
                                        </div>

                                        <div class="col-md-6 p-2">
                                            <p v-if="stat.store_threshold_inventories.length > 0">
                                                <span class="text-info">Some products in your store have reached threshold <br></span>
                                             
                                                <span v-for="product in stat.store_threshold_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>

                                            <p v-if="stat.store_zero_inventories.length > 0">
                                                <span class="text-muted">Some products in your store have exhausted <br></span>
                                             
                                                <span v-for="product in stat.store_zero_inventories" :key="product.id">
                                                    <span class="pl-3"> - {{ product.product_name }} ({{ product.quantity }})</span></br>
                                                </span>
                                                <router-link to="/admin/inventory" class="pl-3">
                                                    View All
                                                </router-link>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </span>
        </div>
    </b-overlay>
</template>

<script>
    import moment from "moment";

    export default {
        created() {
            this.getUser();
            this.getStat();
            this.loadSales();
            this.loadAccount();
        },

        data() {
            return {
                is_busy: false,
                user: "",
                editMode: false,
                stat: '',

                report_items: null,
                summary: null,
                report_items: {
                    data: '',
                },

                items: {},
                totalPrice: '',

                filterForm: {
                    store: 0,
                    start_date: '',
                    end_date: '',
                },
                query_debit: '',
                query_credit: '',
                stat: {
                    threshold_inventories: '',
                    zero_inventories: '',
                    store_threshold_inventories: '',
                    store_zero_inventories: '',
                },
            };
        },

        methods: {
            getUser() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.get("api/user")
                .then(({ data }) => {
                    this.user = data.user;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            getStat() {
                axios.get("/api/stat")
                .then(({ data }) => {
                    this.stat = data;
                });
            },

            loadSales() {
                axios.get('/api/store/reports', { params: this.filterForm })
                .then((response) => {
                    this.report_items = response.data.report_data;
                    this.stores = response.data.stores;
                });
            },

            viewItems(order) {
                this.items = order.cart.items;
                this.totalPrice = order.cart.totalPrice;
                $('#getItem').modal('show');
            },

            loadAccount() {
                axios.get("/api/account/balance", { params: this.filterForm })
                .then(({ data }) => {
                    this.query_credit = data.credit_account;
                    this.query_debit = data.debit_account;
                });
            },
        }      
    };
</script>
<style>
    .help-img{
        height:40px;
        width:40px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .hover:hover {
        background-color:#ECF0F5;
    }

    .routerText{
        font-size:15px
    }

    .margin-bottom {
        margin-bottom: 50px
    }
    .text-center{
        padding: 10px
    }
</style>