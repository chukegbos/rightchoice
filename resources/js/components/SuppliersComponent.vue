<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <b-button variant="outline-primary" size="sm" v-b-modal.filter-modal><i class="fa fa-filter"></i> Filter</b-button>
            <b-modal id="filter-modal" ref="filter" title="Report Filter" hide-footer>
                <b-form @submit.stop.prevent="onFilterSubmit">
                    <b-form-group label="Keyword:" label-for="keyword">
                        <b-form-input
                            id="keyword"
                            v-model="filterForm.keyword"
                            type="text"
                        ></b-form-input>
                    </b-form-group>
                    <b-button type="submit" variant="primary">Search</b-button>
                </b-form>
            </b-modal>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Suppliers
                    </div>
                    <div class="card-tools" v-if="">
                        <button class="btn btn-success" @click="newModal">
                            Add New
                            <i class="fa fa-user-plus fa-fw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <tr>
                            <th>Supplier</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <!--<th>Bank Name</th>
                            <th>Account Number</th>
                            <th>Account Name</th>-->
                            <th>Modify</th>
                        </tr>

                        <tr v-for="user in users.data" :key="user.id">
                            <td>{{ user.supplier_name }}</td>
                            <td>{{ user.contact_person }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone }}</td>
                            <td>{{ user.address }}</td>
                            <!--<td>
                                <span v-for="bank in banks" :key="bank.id">
                                  <span v-if="bank.code==user.bank_name">
                                    {{ bank.name }}
                                  </span>
                                </span>
                            </td>
                            <td>{{ user.bank_account }}</td>
                            <td>{{ user.account_name }}</td>-->
                            <td>
                                <a href="#" @click="editModal(user)">
                                    <i class="fa fa-edit"></i>
                                </a>
                                |
                                <a
                                    href="#"
                                    @click="deleteUser(user.id)"
                                >
                                    <i class="fa fa-trash text-red"></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination :data="users" @pagination-change-page="getResults"></pagination>
                </div>
            </div>

            <div
                class="modal fade"
                id="addNewUser"
                tabindex="-1"
                role="dialog"
                aria-labelledby="addNewUserLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="addNewUserLabel"
                                v-show="!editMode"
                            >
                                Add New
                            </h5>
                            <h5
                                class="modal-title"
                                id="addNewUserLabel"
                                v-show="editMode"
                            >
                                Update Supplier
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form
                            @submit.prevent="
                                editMode ? updateUser() : createUser()
                            "
                        >
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name of Supplier</label>
                                    <input
                                        v-model="form.supplier_name"
                                        type="text"
                                        name="supplier_name"
                                        required
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'supplier_name'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="supplier_name"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input
                                        v-model="form.contact_person"
                                        type="text"
                                        name="contact_person"
                                        required
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'contact_person'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="contact_person"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        name="email"
                                        required
                                        class="form-control"
                                        placeholder="Email Address"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'email'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="email"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="form-control"
                                        placeholder="Phone Number"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'phone'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="phone"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        required
                                        class="form-control"
                                        placeholder="Email Address"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'address'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="address"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input
                                        v-model="form.bank_account"
                                        class="form-control"
                                        @change="onType()"
                                    />
                                </div>

                                <div class="form-group">
                                    <label>Select Bank</label>
                                    <b-form-select
                                        v-model="form.bank_name"
                                        :options="banks"
                                        value-field="code"
                                        text-field="name"
                                        label="Select Bank"
                                        v-on:change="onChange($event)"
                                    >
                                        <template v-slot:first>
                                            <b-form-select-option
                                                :value="null"
                                                disabled
                                                >-- Please select your
                                                bank--</b-form-select-option
                                            >
                                        </template>
                                    </b-form-select>
                                </div>

                                <div class="form-group">
                                    <label>Account Name</label>
                                    <input
                                        v-model="form.account_name"
                                        readonly="true"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button
                                    v-show="editMode"
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    Update
                                </button>
                                <button
                                    v-show="!editMode"
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Create
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
        this.loadUsers();
        this.getBank();
    },

    data() {
        return {
            is_busy: false,
            editMode: false,
            model: {},
            users: {},
            user: "",
            usersss: '',
            form: new Form({
                id: "",
                supplier_name: "",
                contact_person: "",
                email: "",
                phone: "",
                address: "",
                bank_name: null,
                account_name: "",
                bank_account: ""
            }),

            filterForm: {
                keyword: ""
            },

            bank_detail: {
                bank_id: "",
                bank_account: ""
            },

            user: {
                data: "",
            },

            banks: {},
            errors: []
        };
    },

    methods: {
        getUser() {
            axios.get("api/user").then(({ data }) => {
                this.user = data.user;
                this.usersss = data.user;
            });
        },
        getResults(page = 1) {
            axios.get("api/suppliers?page=" + page).then(response => {
                this.users = response.data;
            });
        },

        onFilterSubmit() {
            this.loadUsers();
            this.getBank();
            this.$refs.filter.hide();
        },

        loadUsers() {
            if (this.is_busy) return;
            this.is_busy = true;
            axios
                .get("/api/suppliers", { params: this.filterForm })
                .then(({ data }) => {
                    this.users = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
        },

        getBank() {
            axios.get("/api/user/bank")
            .then(({ data }) => {
                console.log(this.banks);
                this.banks = data;
            });
        },

        onType() {
            this.bank_detail.bank_account = this.form.bank_account;
        },

        onChange(event) {
            this.bank_detail.bank_id = event;
            if (this.is_busy) return;
            this.is_busy = true;
            axios
                .post("/api/user/fetchbank", this.bank_detail)
                .then(({ data }) => {
                    console.log(data);

                    if (data.data.account_name == "error") {
                        Swal.fire(
                            "Failed!",
                            "Such account do not exist, check to see if you are correct",
                            "error"
                        );
                    } else {
                        this.form.account_name = data.data.account_name;
                    }
                })
                .catch(err => {
                    Swal.fire(
                        "Failed!",
                        "Such account do not exist, check to see if you are correct",
                        "error"
                    );
                })

                .finally(() => {
                    this.is_busy = false;
                });
        },

        editModal(user) {
            (this.editMode = true), this.form.reset();
            $("#addNewUser").modal("show");
            this.form.fill(user);
        },

        newModal() {
            (this.editMode = false), this.form.reset();
            $("#addNewUser").modal("show");
        },

        createUser() {
            this.$Progress.start();

            this.form
                .post("api/suppliers")
                .then(() => {
                    $("#addNewUser").modal("hide");

                    Swal.fire(
                        "Created!",
                        "Supplier Created Successfully.",
                        "success"
                    );
                    this.loadUsers();
                    this.getBank();
                })

                .catch(() => {
                    this.$Progress.fail();
                });
        },

        updateUser() {
            this.$Progress.start();

            this.form
                .put("api/suppliers/" + this.form.id)
                .then(() => {
                    $("#addNewUser").modal("hide");

                    Swal.fire(
                        "Updated!",
                        "Supplier Updated Successfully.",
                        "success"
                    );
                    this.$Progress.finish();
                    this.loadUsers();
                    this.getBank();
                })

                .catch(() => {
                    this.$Progress.fail();
                });
        },

        deleteUser(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    this.$Progress.start();
                    this.form
                        .delete("api/suppliers/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Supplier info has been deleted.",
                                "success"
                            );
                            this.$Progress.finish();
                            this.loadUsers();
                            this.getBank();
                        })

                        .catch(() => {
                            Swal(
                                "Failed!",
                                "Ops, Something went wrong, try again.",
                                "Warning"
                            );
                        });
                }
            });
        }
    }
};
</script>
