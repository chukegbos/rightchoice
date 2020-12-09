<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="card">
                        <div v-if="products" class="card-body">
                            <form @submit.prevent="updateInventory()">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit Price (Naira)</th>
                                                <th>Price (Naira)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="product in products" :key="product.id">
                                                <td>
                                                    {{ product.item.product_name}}

                                                </td>
                                                <td>
                                                    <span @click=reduceOne(product.item.id) class="btn btn-danger btn-sm"><i class="fa fa-minus fa-x"></i></span>

                                                    {{ product.quantity }}
                                                    
                                                    <span @click=addOne(product) class="btn btn-info btn-sm"><i class="fa fa-plus fa-x"></i></span>
                                                </td>

                                                <td>{{ product.item.price}}</td>
                                                <td>{{ product.price }}</td>

                                                <td width="200px">
                                                    <span @click=edit(product) class="btn btn-info btn-sm">Edit Qty</span>

                                                    <span @click=reduceAll(product.item.id) class="btn btn-danger btn-sm">Remove</span>
                                                </td>
                                            </tr>                                
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th class="text-right  pd-10"></th>
                                                <th class="text-right  pd-10">
                                                    Amount<br>
                                                    Discount({{ discount }}%)<br>
                                                    Total Price
                                                </th>
                                                <th class="text-right  pd-10 ">
                                                    {{ totalPrice }}<br>
                                                    {{ form.discount }}
                                                    <br>
                                                    N{{ form.amount }}
                                                </th>
                                                <th>
                                                    <br>
                                                        <small>
                                                            <a href="#" @click="ApplyModal">Apply Discount</a>
                                                        </small>
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>

                                    </table>  
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Amount to pay</label>
                                            <input type="number" class="form-control" v-model="form.paid" :max="form.amount">
                                        </div>
                                    </div>

                                    <div class="col-md-6" v-if="!setUser">
                                        <div class="form-group">
                                            <label>Select Customer</label>
                                            <b-form-select
                                                v-model="form.customer_id"
                                                :options="customers"
                                                value-field="id"
                                                text-field="name"
                                            >
                                                <template v-slot:first>
                                                    <b-form-select-option :value="null" disabled>
                                                        -- Select customer --
                                                    </b-form-select-option>
                                                </template>
                                            </b-form-select>
                                            <small><a href="#" @click=addUser>Add User</a></small>
                                        </div>
                                    </div>                            

                                    <div class="col-md-6" v-if="setUser">
                                        <div class="form-group">
                                            <label>Customer Name</label>
                                            <input type="text" class="form-control" v-model="form.name">
                                        </div>
                                    </div>

                                    <div class="col-md-6" v-if="setUser">
                                        <div class="form-group">
                                            <label>Customer Phone</label>
                                            <input type="tel" class="form-control" v-model="form.phone">
                                        </div>
                                    </div>

                                    <div class="col-md-6" v-if="setUser">
                                        <div class="form-group">
                                            <label>Customer Email</label>
                                            <input type="email" class="form-control" v-model="form.email">
                                        </div>
                                    </div>

                                    <div class="col-md-12" v-if="setUser">
                                        <div class="form-group">
                                            <label>Customer Address</label>
                                            <input type="text" class="form-control" v-model="form.address">
                                        </div>
                                        <small><a href="#" @click=removeUser>Remove</a></small>
                                    </div>

                                    

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Mode of Payment</label>
                                            <select v-model="form.selected" class="form-control" @change="onChange($event)">
                                                <option v-for="option in options" v-bind:value="option.value">
                                                    {{ option.text }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6" v-if="sec_id==1">
                                        <div class="form-group">
                                            <label>Teller/POS ID</label>
                                            <input type="text" class="form-control" v-model="form.sec_id">
                                        </div>
                                    </div>

                                    

                                </div>

                                <button type="submit" class="btn btn-success">Checkout</button>
                            </form>
                        </div> 
                        <div v-else class="card-body">
                            <div class="alert alert-info text-center">
                                No item in cart!!!
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="addNewstoreLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Percentage Discount</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.stop.prevent="onApply">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="applied_discount.percent" type="number" class="form-control" :max="user.sale_percent"/>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Apply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editQty" tabindex="-1" role="dialog" aria-labelledby="qtyLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Quantity</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.stop.prevent="EditQty">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="set_product.quantity" type="number" class="form-control"/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Edit
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
    export default {
        created() {
            this.loadCart();
            this.getUser();
        },

        data() {
            return {
                is_busy: false,
                totalPrice: '',
                products: '',
                inventories: '',
                options: [
                    { text: 'Cash', value: 'Cash' },
                    { text: 'Transfer', value: 'Transfer' },
                    { text: 'POS', value: 'POS' }
                ],

                pay_type: [
                    { text: 'Prepaid', value: 'Prepaid' },
                    { text: 'Paid', value: 'Paid' },
                    { text: 'Postpaid', value: 'Postpaid' }
                ],

                customers: [],
                form: new Form({
                    selected: 'Cash',
                    customer_id: null,
                    name: '',
                    phone: '',
                    email: '',
                    address: '',
                    percent_discount: '',
                    discount: '',
                    pay_type: '',
                    amount: '',
                    paid: '',
                    sec_id: '',
                }),
                sec_id: '',
                applied_discount: {
                    percent: '',
                },

                set_product: {
                    product_id: '',
                    quantity: '',
                    qty: '',
                },

                setUser: '',
                user: '',
                store: '',
                discount: '0',
            };
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                    this.store = data.store;
                    this.customers = data.customers;
                });
            },

            loadCart() {
                if(this.is_busy) return;
                this.is_busy = true;
                axios.get('/api/cart/getCart')
                .then(({ data }) => {
                    this.totalPrice = data.totalPrice;
                    this.form.paid = this.totalPrice;
                    this.form.percent_discount = 0;
                    this.form.discount = 0;
                    this.form.amount = this.totalPrice;
                    this.products = data.products;
                    this.inventories = data.inventories;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            ApplyModal() {
                this.applied_discount.percent = this.user.sale_percent;
                $("#discountModal").modal("show");
            },

            addUser() {
                this.setUser = 1;
            },

            edit(product) {
                this.set_product.quantity = product.quantity;
                this.set_product.qty = product.quantity;
                this.set_product.product_id = product.product_id;
                $("#editQty").modal("show");
            },

            EditQty(){
                axios.post('/api/cart/setqty', this.set_product)
                .then((data)=>{
                    if(data.data=='no'){
                        Swal.fire(
                            'Failed!',
                            'You do not have upto that number of stock',
                            "warning"
                        )
                    }
                    this.set_product.quantity = '';
                    this.set_product.product_id = '';
                    $("#editQty").modal("hide");
                    this.loadCart();
                })
                .catch();       
            },


            removeUser() {
                this.setUser = '';
            },

            onApply()
            {   
                this.discount = this.applied_discount.percent;
                
                this.form.discount = (this.applied_discount.percent/100)*this.totalPrice;
                this.form.amount = this.totalPrice - this.form.discount;
                this.form.percent_discount = this.applied_discount.percent;

                this.form.paid = this.totalPrice - this.form.discount;

                $("#discountModal").modal("hide");
            },

            addOne(product) {
                axios.get('/api/cart/testqty/', { params: product})
                .then(({ data }) => {
                    if(data=='no'){
                        Swal.fire(
                            'Failed!',
                            'You do not have upto that number of stock',
                            'error'
                        )
                        this.loadCart();
                    }
                    else
                    {
                        axios.get('/api/cart/addOne/'+product.item.id)
                        .then(({ data }) => {
                            this.loadCart();
                        });
                    }
                    
                });           
            },

            reduceOne(id) {
                axios.get('/api/cart/reduceOne/'+id)
                .then(({ data }) => {
                    this.loadCart();
                });
            },

            reduceAll(id) {
                axios.get('/api/cart/reduceAll/'+id)
                .then(({ data }) => {
                    this.loadCart();
                });
            },

            updateInventory(){
                axios.post('/api/cart/checkout', this.form)
                .then((data)=>{
                    Swal.fire(
                        'Created!',
                        'Order Completed.',
                        'success'
                    )
                    this.$router.push({ path: '/orderview/' + data.data});
                })

                .catch((error) => {
                    Swal.fire(
                        'Failed!',
                        'Error checking out',
                        'error'
                    )
                });       
            },

            onChange($event){
                let value = event.target.value;
                if ((value == "POS") || (value == "Transfer"))
                {
                    this.sec_id = 1;
                }
                else{
                    this.sec_id = '';
                }
            }
        },
    };
</script>
