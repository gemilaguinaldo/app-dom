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
                        label="Add Role"
                        icon="mdi-plus"
                        color="primary"
                        no-caps
                        unelevated
                        @click.prevent="dialog_add_role = true"
                    />
                </div>
            </div>
            <table class="data-listing">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="( role, index ) in roles">
                        <tr
                            :key="index"
                            :class="role.id == 1 ? 'disable' : ''"
                            @click.prevent="showDialogUpdateRole( role.id, role.name, role.description )"
                        >
                            <td>{{ role.name }}</td>
                            <td>{{ role.description }}</td>
                            <td>{{ role.created_at | formatDate }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
        <q-dialog v-model="dialog_add_role" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Add Role</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="add_role_name"
                                :value="add_role_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_role_submitting"
                                :error="errors.has( 'add_role_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="addRole()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_role_name' )">{{ errors.first( 'add_role_name' ) }}</div>
                            <div class="form-error text-negative" v-show="backend_validation_name">Name has already been taken.</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Description</div>
                            <q-input
                                name="add_role_description"
                                :value="add_role_description"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                autogrow
                                :disable="add_role_submitting"
                                :error="errors.has( 'add_role_description' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_role_description' )">{{ errors.first( 'add_role_description' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                            <div>
                                <q-btn
                                    label="Cancel"
                                    color="negative"
                                    no-caps
                                    unelevated
                                    v-close-popup
                                    :disable="add_role_submitting"
                                />
                            </div>
                            <div>
                                <q-btn
                                    label="Save"
                                    color="positive"
                                    :loading="add_role_submitting"
                                    no-caps
                                    unelevated
                                    @click.prevent="addRole()"
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
        <q-dialog v-model="dialog_update_role" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Update Role</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="update_role_name"
                                v-model="update_role_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_role_submitting"
                                :error="errors.has( 'update_role_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateRole()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_role_name' )">{{ errors.first( 'update_role_name' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Description</div>
                            <q-input
                                name="update_role_description"
                                v-model="update_role_description"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                autogrow
                                :disable="update_role_submitting"
                                :error="errors.has( 'update_role_description' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_role_description' )">{{ errors.first( 'update_role_description' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-between">
                            <div>
                                <q-btn
                                    label="Delete"
                                    color="info"
                                    no-caps
                                    unelevated
                                    :disable="update_role_submitting"
                                    @click.prevent="dialog_delete_role = true"
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
                                        :disable="update_role_submitting"
                                    />
                                </div>
                                <div>
                                    <q-btn
                                        label="Update"
                                        color="positive"
                                        :loading="update_role_submitting"
                                        no-caps
                                        unelevated
                                        @click.prevent="updateRole()"
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
        <q-dialog v-model="dialog_delete_role" persistent>
            <q-card square class="dialog-role">
                <q-card-section class="q-pa-lg">
                    <div class="row items-center q-mb-lg">
                        <q-icon name="mdi-alert-circle" size="40px" color="warning" />
                        <div class="col q-pl-sm">Are you sure want to delete this role?</div>
                    </div>
                    <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                        <div>
                            <q-btn
                                label="No"
                                color="negative"
                                no-caps
                                unelevated
                                v-close-popup
                                :disable="delete_role_submitting"
                            />
                        </div>
                        <div>
                            <q-btn
                                label="Yes"
                                color="positive"
                                no-caps
                                unelevated
                                :loading="delete_role_submitting"
                                @click.prevent="deleteRole()"
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
                roles: [],
                show_loader: true,

                //add role
                add_role_name: '',
                add_role_description: '',
                dialog_add_role: false,
                add_role_submitting: false,
                backend_validation_name: false,

                //update role
                update_role_id: '',
                update_role_name: '',
                update_role_description: '',
                dialog_update_role: false,
                update_role_submitting: false,

                //delete role
                delete_role_id: '',
                dialog_delete_role: false,
                delete_role_submitting: false,
            }
        },
        methods: {
            retrieveRoles() {
                this.show_loader = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'roles/lists?dropdown_list=false'
                })
                .then( response => {
                    this.roles = response.data.roles
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            },
            addRole() {
                this.add_role_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'roles/add',
                            data: {
                                name: this.add_role_name,
                                description: this.add_role_description
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogAddRole()
                            this.retrieveRoles()
                        })
                        .catch( error => {
                            if ( error.response.status == 422 ) {
                                const errors = error.response.data
                                if ( errors.name ) {
                                    this.backend_validation_name = true
                                }
                            } else {
                                this.$q.notify({
                                    message: error.response.data.message,
                                    position: 'top',
                                    color: 'negative',
                                    icon: 'mdi-close'
                                })
                            }
                            this.add_role_submitting = false
                        })
                    } else {
                        this.add_role_submitting = false
                    }
                })
            },
            updateRole() {
                this.update_role_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'roles/update',
                            data: {
                                role_id: this.update_role_id,
                                update_name: this.update_role_name,
                                update_description: this.update_role_description
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogUpdateRole()
                            this.retrieveRoles()
                        })
                        .catch( error => {
                            this.$q.notify({
                                message: error.response.data.message,
                                position: 'top',
                                color: 'negative',
                                icon: 'mdi-check'
                            })
                            this.update_role_submitting = false
                        })
                    } else {
                        this.update_role_submitting = false
                    }
                })
            },
            deleteRole() {
                this.delete_role_submitting = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'post',
                    url: 'roles/delete',
                    data: {
                        role_id: this.delete_role_id
                    }
                })
                .then( response => {
                    this.$q.notify({
                        message: response.data.message,
                        position: 'top',
                        color: 'positive',
                        icon: 'mdi-check'
                    })
                    this.hideDialogUpdateRole()
                    this.dialog_delete_role = false
                    this.retrieveRoles()
                })
                .catch( error => {
                    this.$q.notify({
                        message: error.response.data.message,
                        position: 'top',
                        color: 'negative',
                        icon: 'mdi-close'
                    })
                    this.delete_role_submitting = false
                })
            },
            showDialogUpdateRole( id, name, description ) {
                this.update_role_id = id
                this.delete_role_id = id
                this.update_role_name = name
                this.update_role_description = description
                this.dialog_update_role = true
            },
            hideDialogAddRole() {
                this.add_role_name = ''
                this.add_role_description = ''
                this.add_role_submitting = false
                this.dialog_add_role = false
            },
            hideDialogUpdateRole() {
                this.update_role_name = ''
                this.update_role_description = ''
                this.update_role_submitting = false
                this.delete_role_submitting = false
                this.dialog_update_role = false
            }
        },
        mounted() {
            this.retrieveRoles()
            const dictionary = {
                en: {
                    attributes: {
                        add_role_name: 'Name',
                        add_role_description: 'Description',
                        update_role_name: 'Name',
                        update_role_description: 'Description',
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