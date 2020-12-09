<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Products Request
                            </div>
                            <div class="card-tools">
                                <router-link
                                    to="/store/trade"
                                    class="btn btn-success text-white"
                                >
                                    Make Request <i class="fa fa-plus fa-fw"></i>
                                </router-link>
                            </div>
                        </div>
                        <div v-if="reqs.data.length" style="text-align:center">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Requested By</th>
                                        <th>Approved By</th>
                                        <th>Recieved By</th>
                                        <th>Status</th>
                                    </tr>

                                    <tr v-for="req in reqs.data" :key="req.id">
                                        <td>
                                            {{ req.req_id }}
                                            <button @click="viewItems(req)" class="btn btn-info btn-sm pull-right">
                                               <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                        <td>
                                            {{ req.sender_id }} <br> {{ req.created_at | myDate }}
                                        </td>
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
                                            
                                            <a href="#" @click="viewItems(req)" v-else-if="req.approved_by && !req.reciever_id && req.status != 'declined'">
                                                Accept
                                            </a>
                                            <span v-else>{{ req.reciever_id }}</span>
                                        </td>
                                        <td>
                                            <span v-if="req.status == 'approved'" class="badge badge-pills badge-success">{{ req.status | capitalize }}</span>
                                            <span v-if="req.status == 'concluded'" class="badge badge-pills badge-primary">{{ req.status | capitalize }}</span>
                                            <span v-if="req.status == 'pending'" class="badge badge-pills badge-warning">{{ req.status | capitalize }}</span>
                                            <span v-if="req.status == 'declined'" class="badge badge-pills badge-danger">{{ req.status | capitalize }}</span>

                                            <span v-if="req.status == 'No Quantity'" class="badge badge-pills badge-secondary">{{ req.status | capitalize }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination :data="reqs" @pagination-change-page="getResults"></pagination>
                            </div>
                        </div>

                        <div v-else class="p-5">
                            <div class="alert alert-info text-center">
                                No Request Available.
                            </div>
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
    },

    data() {
        return {
            reqs: [],
            is_busy: false,
            form: new Form({
                id: "",
                qty: "",
                product: "",
            }),
            reqs: {
                data: '',
            }, 
        };
    },

    methods: {
        getResults(page = 1) {
            if(this.is_busy) return;
            this.is_busy = true;

            axios.get("/api/store/request?page=" + page)
            .then(response => {
                this.reqs = response.data;
            })
            .finally(() => {
                this.is_busy = false;
            });
        },

        loadInventory() {
            if(this.is_busy) return;
            this.is_busy = true;

            axios.get("/api/store/request")
            .then(({ data }) => {
                this.reqs = data;
            })
            .finally(() => {
                this.is_busy = false;
            });
        },

        getItems(req) {
            this.form.id = req.id;
            this.form.qty = req.qty;
            this.form.product = req.product_name;
            $("#getItem").modal("show");
        },

        viewItems(req) {
            this.$router.push({ path: "/store/request/view/" + req.req_id });
        },

        accept(id) {
            axios.get("/api/store/accept/" + id)
            .then(() => {
                Swal.fire(
                    "Created!",
                    "Product Accepted.",
                    "success"
                );
                $("#getItem").modal("hide");
                this.loadInventory();
            })
            .catch(error => {
                Swal.fire(
                    "Failed!",
                    "There is an error somewhere",
                    "error"
                );
            });
        },
    }
};
</script>

<style>
.list_product {
    list-style: none;
}
</style>
