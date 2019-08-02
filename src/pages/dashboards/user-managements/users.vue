<template>
    <div class="page-content q-pa-xl">
        <template v-if="show_loader">
            <q-inner-loading :showing="show_loader">
                <q-spinner-dots size="50px" />
            </q-inner-loading>
        </template>
        <template v-else>
            <div class="row justify-between items-center q-mb-lg">
                <div>
                    <h4 class="q-mb-none"><strong>{{ this.$route.meta.pageTitle }}</strong></h4>
                </div>
                <div>
                    <q-btn
                        label="Add User"
                        icon="mdi-plus"
                        color="primary"
                        no-caps
                        unelevated
                        @click.prevent="dialog_add_user = true"
                    />
                </div>
            </div>
            <table class="data-listing">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="( user, index ) in users">
                        <tr
                            :key="index"
                            :class="user.role == 'Administrator' ? 'disable' : ''"
                            @click.prevent="showDialogUpdateUser( user.id, user.name, user.email_address, user.role )"
                        >
                            <td>{{ user.name }}</td>
                            <td>{{ user.email_address }}</td>
                            <td>{{ user.role }}</td>
                            <td>{{ user.created_at | formatDate }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
        <q-dialog v-model="dialog_add_user" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Add User</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="add_user_name"
                                v-model="add_user_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_user_submitting"
                                :error="errors.has( 'add_user_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="addUser()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_user_name' )">{{ errors.first( 'add_user_name' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Email Address</div>
                            <q-input
                                type="email"
                                name="add_user_email_address"
                                v-model="add_user_email_address"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_user_submitting"
                                :error="errors.has( 'add_user_email_address' )"
                                v-validate="'required|email'"
                                data-vv-validate-on="none"
                                @keyup.enter="addUser()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_user_email_address' )">{{ errors.first( 'add_user_email_address' ) }}</div>
                            <div class="form-error text-negative" v-show="backend_validation_email">Email address has already been taken.</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Role</div>
                            <q-select
                                name="add_user_role"
                                v-model="add_user_role"
                                :options="user_roles"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_user_submitting"
                                :error="errors.has( 'add_user_role' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_user_role' )">{{ errors.first( 'add_user_role' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                            <div>
                                <q-btn
                                    label="Cancel"
                                    color="negative"
                                    no-caps
                                    unelevated
                                    v-close-popup
                                    :disable="add_user_submitting"
                                    @click.prevent="hideDialogAddUser()"
                                />
                            </div>
                            <div>
                                <q-btn
                                    label="Save"
                                    color="positive"
                                    :loading="add_user_submitting"
                                    no-caps
                                    unelevated
                                    @click.prevent="addUser()"
                                >
                                    <template v-slot:loading>
                                        <q-spinner-dots />
                                    </template>
                                </q-btn>
                            </div>
                        </div>
                    </form>
                </q-card-section>
            </q-card>
        </q-dialog>
        <q-dialog v-model="dialog_update_user" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Update User</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="update_user_name"
                                v-model="update_user_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_user_submitting"
                                :error="errors.has( 'update_user_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateUser()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_user_name' )">{{ errors.first( 'update_user_name' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Email Address</div>
                            <q-input
                                type="email"
                                name="update_user_email_address"
                                v-model="update_user_email_address"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_user_submitting"
                                :error="errors.has( 'update_user_email_address' )"
                                v-validate="'required|email'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateUser()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_user_email_address' )">{{ errors.first( 'update_user_email_address' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Role</div>
                            <q-select
                                name="update_user_role"
                                v-model="update_user_role"
                                :options="user_roles"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_user_submitting"
                                :error="errors.has( 'update_user_role' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_user_role' )">{{ errors.first( 'update_user_role' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-between">
                            <div>
                                <q-btn
                                    label="Delete"
                                    color="info"
                                    no-caps
                                    unelevated
                                    :disable="update_user_submitting"
                                    @click.prevent="dialog_delete_user = true"
                                />
                            </div>
                            <div class="row items-center justify-end q-col-gutter-sm">
                                <div>
                                    <q-btn
                                        label="Cancel"
                                        color="negative"
                                        no-caps
                                        unelevated
                                        v-close-popup
                                        :disable="update_user_submitting"
                                    />
                                </div>
                                <div>
                                    <q-btn
                                        label="Update"
                                        color="positive"
                                        :loading="update_user_submitting"
                                        no-caps
                                        unelevated
                                        @click.prevent="updateUser()"
                                    >
                                        <template v-slot:loading>
                                            <q-spinner-dots />
                                        </template>
                                    </q-btn>
                                </div>
                            </div>
                        </div>
                    </form>
                </q-card-section>
            </q-card>
        </q-dialog>
        <q-dialog v-model="dialog_delete_user" persistent>
            <q-card square class="dialog-role">
                <q-card-section class="q-pa-lg">
                    <div class="row items-center q-mb-lg">
                        <q-icon name="mdi-alert-circle" size="40px" color="warning" />
                        <div class="col q-pl-sm">Are you sure want to delete this user?</div>
                    </div>
                    <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                        <div>
                            <q-btn
                                label="No"
                                color="negative"
                                no-caps
                                unelevated
                                v-close-popup
                                :disable="delete_user_submitting"
                            />
                        </div>
                        <div>
                            <q-btn
                                label="Yes"
                                color="positive"
                                no-caps
                                unelevated
                                :loading="delete_user_submitting"
                                @click.prevent="deleteUser()"
                            >
                                <template v-slot:loading>
                                    <q-spinner-dots />
                                </template>
                            </q-btn>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                users: [],
                show_loader: true,

                //add user
                dialog_add_user: false,
                add_user_name: '',
                add_user_email_address: '',
                add_user_role: '',
                user_roles: [],
                add_user_submitting: false,
                backend_validation_email: false,

                //update user
                dialog_update_user: false,
                update_user_id: '',
                update_user_name: '',
                update_user_email_address: '',
                update_user_role: '',
                update_user_submitting: false,

                //delete user
                delete_user_id: '',
                delete_user_submitting: false,
                dialog_delete_user: false,
            }
        },
        methods: {
            retrieveUsers() {
                this.show_loader = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'users/lists'
                })
                .then( response => {
                    this.users = response.data.users
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            },
            retrieveRoles() {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'roles/lists?dropdown_list=true'
                })
                .then( response => {
                    this.user_roles = response.data.roles
                })
                .catch( error => {
                    console.log( error )
                })
            },
            addUser() {
                this.add_user_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'users/add',
                            data: {
                                name: this.add_user_name,
                                email_address: this.add_user_email_address,
                                role: this.add_user_role
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogAddUser()
                            this.retrieveUsers()
                        })
                        .catch( error => {
                            if ( error.response.status == 422 ) {
                                const errors = error.response.data
                                if ( errors.email_address ) {
                                    this.backend_validation_email = true
                                }
                            } else {
                                this.$q.notify({
                                    message: error.response.data.message,
                                    position: 'top',
                                    color: 'negative',
                                    icon: 'mdi-close'
                                })
                            }
                            this.add_user_submitting = false
                        })
                    } else {
                        this.add_user_submitting = false
                    }
                })
            },
            updateUser() {
                this.update_user_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'users/update',
                            data: {
                                user_id: this.update_user_id,
                                name: this.update_user_name,
                                email_address: this.update_user_email_address,
                                role: this.update_user_role,
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogUpdateUser()
                            this.retrieveUsers()
                        })
                        .catch( error => {
                            this.$q.notify({
                                message: error.response.data.message,
                                position: 'top',
                                color: 'negative',
                                icon: 'mdi-check'
                            })
                            this.update_user_submitting = false
                        })
                    } else {
                        this.update_user_submitting = false
                    }
                })
            },
            deleteUser() {
                this.delete_user_submitting = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'post',
                    url: 'users/delete',
                    data: {
                        user_id: this.delete_user_id
                    }
                })
                .then( response => {
                    this.$q.notify({
                        message: response.data.message,
                        position: 'top',
                        color: 'positive',
                        icon: 'mdi-check'
                    })
                    this.hideDialogUpdateUser()
                    this.dialog_delete_user = false
                    this.retrieveUsers()
                })
                .catch( error => {
                    this.$q.notify({
                        message: error.response.data.message,
                        position: 'top',
                        color: 'negative',
                        icon: 'mdi-close'
                    })
                    this.delete_user_submitting = false
                })
            },
            showDialogUpdateUser( id, name, email_address, role ) {
                this.update_user_id = id
                this.delete_user_id = id
                this.update_user_name = name
                this.update_user_email_address = email_address
                this.update_user_role = role
                this.dialog_update_user = true
            },
            hideDialogAddUser() {
                this.add_user_name = ''
                this.add_user_email_address = ''
                this.add_user_role = ''
                this.add_user_submitting = false
                this.dialog_add_user = false
                this.backend_validation_email = false
            },
            hideDialogUpdateUser() {
                this.update_user_name = ''
                this.update_user_email_address = ''
                this.update_user_role = ''
                this.update_user_submitting = false
                this.delete_user_submitting = false
                this.dialog_update_user = false
            }
        },
        mounted() {
            this.retrieveUsers()
            this.retrieveRoles()
            const dictionary = {
                en: {
                    attributes: {
                        add_user_name: 'Name',
                        add_user_email_address: 'Email Address',
                        add_user_role: 'Role',
                        update_user_name: 'Name',
                        update_user_email_address: 'Email Address',
                        update_user_role: 'Role',
                    }
                }
            }
            this.$validator.localize( dictionary )
        }
    }
</script>
<style lang="stylus" scoped>
    .dialog-role {
        .dialog-title {
            font-size: 1.6rem;
            background-color: $grey-9;
            color: $white;
            padding: 15px 20px;
        }

        form {
            padding: 30px 50px;
        }
    }
</style>