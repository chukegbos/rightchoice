<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <b-button variant="outline-primary" size="sm" v-b-modal.filter-modal><i class="fa fa-filter"></i> Filter</b-button>
                    <b-button variant="outline-info" size="sm" @click=onPrint>Print</b-button>

                    <b-modal id="filter-modal" ref="filter" title="Filter" hide-footer>
                        <b-form @submit.stop.prevent="onFilterSubmit">
                            <div class="row">
                                <div class="col-lg-6">
                                    <b-form-group label="Start Date:" label-for="Start Date">
                                        <b-form-datepicker v-model="filterForm.start_date" placeholder="Start date"
                                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                                        </b-form-datepicker>
                                    </b-form-group>
                                </div>

                                <div class="col-lg-6">
                                    <b-form-group label="End Date:" label-for="End Date">
                                        <b-form-datepicker v-model="filterForm.end_date" placeholder="End date"
                                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                                        </b-form-datepicker>
                                    </b-form-group>
                                </div>
                            </div>

                            <!--<b-form-group label="Store:" v-if="user.role==3">
                                <b-form-select v-model="filterForm.store" :options="stores" value-field="id" text-field="name">
                                    <template v-slot:first>
                                        <b-form-select-option :value="0">
                                            All
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-form-group>-->

                            <b-button type="submit" variant="primary">Search</b-button>
                        </b-form>
                    </b-modal>
                </div>
            </div>

           
            <div class="card" id="printMe">
                <div class="card-header">
                    <div class="card-title">
                        Account Statement from {{ filterForm.start_date | myDate }} to {{ filterForm.end_date | myDate }}
                    </div>
                </div>

                <div v-if="accounts.data.length">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>Date</th>
                                <th>Income(&#8358;)</th>
                                <th>Expenditure(&#8358;)</th>
                                <th>Balance</th>
                                
                            </tr>

                            <tr v-for="account in accounts.data" :key="account.id">
                                <td>{{ account.main_date | myDate}}</td>
                                <td>{{ account.income }}</td>
                                <td>{{ account.expenditure }}</td>
                                <td>{{ account.income - account.expenditure }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="accounts" @pagination-change-page="getResults"></pagination>
                    </div>
                </div>

                <div v-else class="p-5">
                    <div class="alert alert-info text-center">
                        No Account.
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import moment from 'moment';
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
        data() {
            return {
                is_busy: false,
                model: {},
              
                accounts: [],
                user: "",
                accounts: {
                    data: '',
                },

                filterForm: {
                    store: 0,
                    start_date: moment().subtract(30, 'days').format("YYYY-MM-DD"),
                    end_date: moment().format("YYYY-MM-DD"),
                },
                query_debit: '',
                query_credit: '',
            };
        },

        created() {
            this.loadAccount();
            this.getUser();
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                });
            },

            onPrint() {
                this.$htmlToPaper('printMe');
            },
            
            getResults(page = 1) {
                axios.get("/api/account/balance?page=" + page).then(response => {
                    this.accounts = response.data;
                });
            },

            loadAccount() {
                if (this.is_busy) return;
                this.is_busy = true;
                
                axios.get("/api/account/balance", { params: this.filterForm })
                .then(({ data }) => {
                    this.accounts = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            onFilterSubmit()
            {
                this.loadAccount();
                this.getUser();
                this.$refs.filter.hide();
            },
        },
    };
</script>
