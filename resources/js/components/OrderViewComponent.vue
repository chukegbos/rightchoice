<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6" id="printMe">
                    <div class="card">
                        <div class="card-header text-center">
                            <p>{{ site.sitename }}<br>{{ site.address }}</p>
                        </div>

                        <div class="card-body" v-for="sale in sales" :key="sale.id">
                            <span v-if="sale_id==sale.sale_id">
                                <div class="p-1 text-center">
                                    Receipt No: <b>{{ sale.sale_id }}</b><br>
                                    Cashier: <b>{{ sale.sale_id }}</b><br>
                                    Date: <b>{{ sale.created_at | myDate }}</b><br>
                                    Payment type: <b> {{ sale.mop }} <span v-if="sale.sec_id">({{ sale.sec_id }})</span></b>
                                </div>
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
                                            <tr v-for="item in sale.cart.items" :key="item.id">
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
                                                <th><span class="vd_green font-sm font-normal"> N{{ sale.totalPrice }}</span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </span>   
                        </div>

                        <div class="card-footer">
                            <div class="pull-left">{{ site.phone }}</div>
                            <div class="pull-right">{{ site.email }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <button @click=onPrint>Print</button>
        </div>
    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
    created() {
        this.viewOrder();
        this.loadSite();
    },

    data() {
        return {
            is_busy: false,
            sale_id: '',
            sales: '',
            items: {},
            totalPrice: '',
            mop: '',
            sec_id: "",
            site: "",
        };
    },

    methods: {
        viewOrder() {
            if(this.is_busy) return;
            this.is_busy = true;

            this.sale_id = this.$route.params.sale_id;

            axios.get("/api/cart/getorder/" + this.sale_id)

            .then((response) => {
                this.sales = response.data;
            })

            .catch((err) => {
                console.log(err);
            })

            .finally(() => {
                this.is_busy = false;
            });
        },

        loadSite() {
            axios.get("/api/setting")
            .then(({ data }) => {
                this.site = data;
            });
        },

        onPrint() {
            this.$htmlToPaper('printMe');
        },
    }
};
</script>
