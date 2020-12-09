<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Products</h3>
                        </div>

                        <form v-on:submit.prevent="editMode ? updateRequest() : submitRequest()" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3" v-for="inventory in inventories" :key="inventory.id">
                                        <label> {{ inventory.product_name }}</label>
                                        <input type="number" name="quantity" class="form-control" :id="inventory.id"/>
                                        <hr>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    Submit
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
        
        data() {
            return {
                editMode: false,
                is_busy: false,
                formData: {
                    productQty: []
                },
                inventories: []
            };
        },

        created() {
            this.loadInventory();
        },

        methods: {
            loadInventory() {
                if(this.is_busy) return;
                this.is_busy = true;

                let req_id  = this.$route.params.req_id;

                axios.get("/api/store/getrequest/" + req_id )
                .then(({ data }) => {
                    this.inventories = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            submitRequest(event) {
                if(this.is_busy) return;
                this.is_busy = true;

                const qty = document.querySelectorAll('input[name="quantity"]');

                qty.forEach(element => {
                    let ID = element.getAttribute("id");

                    this.formData.productQty.push({
                        qtyId: ID,
                        quantity: element.value
                    });
                       
                });

                axios.post("/api/store/request/", this.formData)
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Your request has been noted.",
                        "success"
                    );
                    this.$router.push({ path: "/store/request" });
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
