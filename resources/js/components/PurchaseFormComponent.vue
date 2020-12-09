<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Products</h3>
                </div>

                <form v-on:submit.prevent="editMode ? updateRequest() : submitRequest()" role="form">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>Name</th>
                                <th width="50px">Quantity</th>
                                <th>Amount Paid</th>
                                <th>Suppliers</th>
                                <th>Date of Purchase</th>
                                <th>Outlet</th>
                            </tr>

                            <tr v-for="inventory in inventories" :key="inventory.id">
                                <td>{{ inventory.product_name }}</td>
                                <td>
                                    <input type="number" name="quantity" :id="'quantity'+inventory.id" required :class="'el' + inventory.id" />
                                </td>
                                <td><input type="number" name="total_price" required :class="'el' + inventory.id"/></td>
                                
                                <td>
                                    <select name="supplier" :id="'supplier'+inventory.id" :class="'el' + inventory.id">
                                        <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.supplier_name }}</option> 
                                    </select>
                                </td>

                                <td><input type="date" :id="'purchase_date'+inventory.id" :class="'el' + inventory.id" name="purchase_date"/></td>

                                <td>
                                    <select name="outlet" :id="'outlet'+inventory.id" :class="'el' + inventory.id">
                                        <option v-for="store in stores" :value="store.id">
                                            {{ store.name }}
                                        </option> 
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </b-overlay>
</template>

<script>
   
    export default {

        data() {
            return {
                editMode: false,
                is_busy: false,
                formData: {
                    productQty: []
                },
                inventories: [],
                suppliers: [],
                stores: [],
                purchase_id: '',
            };
        },

        created() {
            this.loadInventory();
            this.loadSuppliers();
            this.loadStores();
        },

        methods: {
            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                this.purchase_id  = this.$route.params.purchase_id;

                axios.get("/api/inventory/purchase/" + this.purchase_id )
                .then(({ data }) => {
                    this.inventories = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            loadSuppliers() {
                axios.get("/api/suppliers/all")
                .then(({ data }) => {
                    this.suppliers = data;
                });
            },

            loadStores() {
                axios.get('/api/store')
                .then(({ data }) => {
                    this.stores = data;
                });
            },

            submitRequest(event) {
                if(this.is_busy) return;
                this.is_busy = true;

                const qty = document.querySelectorAll('input[name="quantity"]');
                
                let supplier, quantity, total_price, outlet, purchase_date;

                qty.forEach(element => {

                    let pID = element.getAttribute("id");
                    let getId = pID.match(/(\d+)/);
                    let ID = getId[0];
                    let elementInSameRow = document.querySelectorAll(
                        ".el" + ID
                    );
                    
                    quantity = elementInSameRow[0];
                    total_price = elementInSameRow[1];
                    supplier = elementInSameRow[2];
                    purchase_date = elementInSameRow[3];
                    outlet = elementInSameRow[4];
                    

                    this.formData.productQty.push({
                        qtyId: ID,
                        quantity: quantity.value,
                        total_price: total_price.value,
                        supplier: supplier.value,
                        purchase_date: purchase_date.value,
                        outlet: outlet.value,
                    });
                       
                });
           
                axios.post("/api/purchases/", this.formData)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Successful.",
                        "success"
                    );
                    this.$router.push({ path: "/admin/purchases/"+ this.purchase_id });
                })
                .catch(error => {
                    Swal.fire(
                        "Failed!",
                        "There is error somewhere",
                        "error"
                    );
                })
                .finally(() => {
                    this.is_busy = false;
                });
            }
        },
    };
</script>