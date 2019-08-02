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
                        label="Add Category"
                        icon="mdi-plus"
                        color="primary"
                        no-caps
                        unelevated
                        @click.prevent="dialog_add_category"
                    />
                </div>
            </div>
            <template v-if="categories == ''">
                <q-banner class="bg-negative text-white" dense>Sorry no categories found.</q-banner>
            </template>
            <template v-else>
                <table class="data-listing">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="( category, index ) in categories">
                            <tr
                                :key="index"
                                @click.prevent="showDialogUpdateCategory( category.id, category.name, category.description )"
                            >
                                <td>{{ category.name }}</td>
                                <td>{{ category.description }}</td>
                                <td>{{ category.created_at | formatDate }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </template>
        <q-dialog v-model="dialog_add_category" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Add Category</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="add_category_name"
                                v-model="add_category_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_category_submitting"
                                :error="errors.has( 'add_category_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="addCategory()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_category_name' )">{{ errors.first( 'add_category_name' ) }}</div>
                            <div class="form-error text-negative" v-show="backend_validation_name">Name has already been taken.</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Description</div>
                            <q-input
                                name="add_category_description"
                                v-model="add_category_description"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                autogrow
                                :disable="add_category_submitting"
                                :error="errors.has( 'add_category_description' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_category_description' )">{{ errors.first( 'add_category_description' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                            <div>
                                <q-btn
                                    label="Cancel"
                                    color="negative"
                                    no-caps
                                    unelevated
                                    v-close-popup
                                    :disable="add_category_submitting"
                                    @click.prevent="hideDialogAddCategory()"
                                />
                            </div>
                            <div>
                                <q-btn
                                    label="Save"
                                    color="positive"
                                    :loading="add_category_submitting"
                                    no-caps
                                    unelevated
                                    @click.prevent="addCategory()"
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
        <q-dialog v-model="dialog_update_category" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Update Category</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Name</div>
                            <q-input
                                type="text"
                                name="update_category_name"
                                v-model="update_category_name"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_category_submitting"
                                :error="errors.has( 'update_category_name' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateCategory()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_category_name' )">{{ errors.first( 'update_category_name' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Description</div>
                            <q-input
                                name="update_category_description"
                                v-model="update_category_description"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                autogrow
                                :disable="update_category_submitting"
                                :error="errors.has( 'update_category_description' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_category_description' )">{{ errors.first( 'update_category_description' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-between">
                            <div>
                                <q-btn
                                    label="Delete"
                                    color="info"
                                    no-caps
                                    unelevated
                                    :disable="update_category_submitting"
                                    @click.prevent="dialog_delete_category = true"
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
                                        :disable="update_category_submitting"
                                    />
                                </div>
                                <div>
                                    <q-btn
                                        label="Update"
                                        color="positive"
                                        :loading="update_category_submitting"
                                        no-caps
                                        unelevated
                                        @click.prevent="updateCategory()"
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
        <q-dialog v-model="dialog_delete_category" persistent>
            <q-card square class="dialog-role">
                <q-card-section class="q-pa-lg">
                    <div class="row items-center q-mb-lg">
                        <q-icon name="mdi-alert-circle" size="40px" color="warning" />
                        <div class="col q-pl-sm">Are you sure want to delete this category?</div>
                    </div>
                    <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                        <div>
                            <q-btn
                                label="No"
                                color="negative"
                                no-caps
                                unelevated
                                v-close-popup
                                :disable="delete_category_submitting"
                            />
                        </div>
                        <div>
                            <q-btn
                                label="Yes"
                                color="positive"
                                no-caps
                                unelevated
                                :loading="delete_category_submitting"
                                @click.prevent="deleteCategory()"
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
                categories: [],
                show_loader: true,

                //add category
                dialog_add_category: false,
                add_category_name: '',
                add_category_description: '',
                add_category_submitting: false,
                backend_validation_name: false,

                //update category
                dialog_update_category: false,
                update_category_id: '',
                update_category_name: '',
                update_category_description: '',
                update_category_submitting: false,

                //delete category
                dialog_delete_category: false,
                delete_category_id: '',
                delete_category_submitting: false,
            }
        },
        methods: {
            retrieveCategories() {
                this.show_loader = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'expenses/categories/lists?dropdown_list=false'
                })
                .then( response => {
                    this.categories = response.data.categories
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            },

            addCategory() {
                this.add_category_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'expenses/categories/add',
                            data: {
                                name: this.add_category_name,
                                description: this.add_category_description,
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogAddCategory()
                            this.retrieveCategories()
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
                            this.add_category_submitting = false
                        })
                    } else {
                        this.add_category_submitting = false
                    }
                })
            },
            updateCategory() {
                this.update_category_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'expenses/categories/update',
                            data: {
                                category_id: this.update_category_id,
                                name: this.update_category_name,
                                description: this.update_category_description
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogUpdateCategory()
                            this.retrieveCategories()
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
            deleteCategory() {
                this.delete_category_submitting = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'post',
                    url: 'expenses/categories/delete',
                    data: {
                        category_id: this.delete_category_id
                    }
                })
                .then( response => {
                    this.$q.notify({
                        message: response.data.message,
                        position: 'top',
                        color: 'positive',
                        icon: 'mdi-check'
                    })
                    this.hideDialogUpdateCategory()
                    this.dialog_delete_category = false
                    this.retrieveCategories()
                })
                .catch( error => {
                    this.$q.notify({
                        message: error.response.data.message,
                        position: 'top',
                        color: 'negative',
                        icon: 'mdi-close'
                    })
                    this.delete_category_submitting = false
                })
            },

            showDialogUpdateCategory( name,  id, description ) {
                this.update_category_id = id
                this.delete_category_id = id
                this.update_category_name = name
                this.update_category_description = description
                this.dialog_update_category = true
            },

            hideDialogAddCategory() {
                this.add_category_name = ''
                this.add_category_description = ''
                this.dialog_add_category = false
                this.add_category_submitting = false
                this.backend_validation_name = false
            },
            hideDialogUpdateCategory() {
                this.update_category_name = ''
                this.update_category_description = ''
                this.delete_category_submitting = false
                this.update_category_submitting = false
                this.dialog_update_category = false
            },
        },
        mounted() {
            this.retrieveCategories()
            const dictionary = {
                en: {
                    attributes: {
                        add_category_name: 'Name',
                        add_category_description: 'Description',
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