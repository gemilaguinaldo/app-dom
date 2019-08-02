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
                        label="Add Expense"
                        icon="mdi-plus"
                        color="primary"
                        no-caps
                        unelevated
                        @click.prevent="dialog_add_expense = true"
                    />
                </div>
            </div>
            <template v-if="expenses == ''">
                <q-banner class="bg-negative text-white" dense>Sorry no expenses found.</q-banner>
            </template>
            <template v-else>
                <table class="data-listing">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Entry Date</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="( expense, index ) in expenses">
                            <tr
                                :key="index"
                                @click.prevent="showDialogUpdateExpense( expense.id, expense.category, expense.amount, expense.entry_date )"
                            >
                                <td>{{ expense.category }}</td>
                                <td>{{ expense.amount | formatPrice }}</td>
                                <td>{{ expense.entry_date }}</td>
                                <td>{{ expense.created_at | formatDate }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </template>
        <q-dialog v-model="dialog_add_expense" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Add Expense</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Category</div>
                            <q-select
                                name="add_expense_category"
                                v-model="add_expense_category"
                                :options="expense_categories"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_expense_submitting"
                                :error="errors.has( 'add_expense_category' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_expense_category' )">{{ errors.first( 'add_expense_category' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Amount</div>
                            <q-input
                                name="add_expense_amount"
                                v-model="add_expense_amount"
                                class="q-pb-none"
                                mask="#.##"
                                fill-mask="0"
                                reverse-fill-mask
                                filled
                                dense
                                square
                                clearable
                                :disable="add_expense_submitting"
                                :error="errors.has( 'add_expense_amount' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="addExpense()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'add_expense_amount' )">{{ errors.first( 'add_expense_amount' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Entry Date</div>
                            <q-input
                                name="add_expense_date"
                                v-model="add_expense_date"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="add_expense_submitting"
                                :error="errors.has( 'add_expense_date' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="addExpense()"
                            >
                                <q-popup-proxy ref="qDateProxy">
                                    <q-date v-model="add_expense_date" @input="() => $refs.qDateProxy.hide()" :options="disableFutureDates" />
                                </q-popup-proxy>
                            </q-input>
                            <div class="form-error text-negative" v-show="errors.has( 'add_expense_date' )">{{ errors.first( 'add_expense_date' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                            <div>
                                <q-btn
                                    label="Cancel"
                                    color="negative"
                                    no-caps
                                    unelevated
                                    v-close-popup
                                    :disable="add_expense_submitting"
                                    @click.prevent="hideDialogAddExpense()"
                                />
                            </div>
                            <div>
                                <q-btn
                                    label="Save"
                                    color="positive"
                                    :loading="add_expense_submitting"
                                    no-caps
                                    unelevated
                                    @click.prevent="addExpense()"
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
        <q-dialog v-model="dialog_update_expense" persistent>
            <q-card square style="width: 480px;" class="dialog-role">
                <q-card-section style="padding: 0;">
                    <div class="dialog-title">Update Expense</div>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Category</div>
                            <q-select
                                name="update_expense_category"
                                v-model="update_expense_category"
                                :options="expense_categories"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_expense_submitting"
                                :error="errors.has( 'update_expense_category' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_expense_category' )">{{ errors.first( 'update_expense_category' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Amount</div>
                            <q-input
                                name="update_expense_amount"
                                v-model="update_expense_amount"
                                class="q-pb-none"
                                mask="#.##"
                                fill-mask="0"
                                reverse-fill-mask
                                filled
                                dense
                                square
                                clearable
                                :disable="update_expense_submitting"
                                :error="errors.has( 'update_expense_amount' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateExpense()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'update_expense_amount' )">{{ errors.first( 'update_expense_amount' ) }}</div>
                        </div>
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Entry Date</div>
                            <q-input
                                name="update_expense_date"
                                v-model="update_expense_date"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="update_expense_submitting"
                                :error="errors.has( 'update_expense_date' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="updateExpense()"
                            >
                                <q-popup-proxy ref="qDateProxy">
                                    <q-date v-model="update_expense_date" @input="() => $refs.qDateProxy.hide()" :options="disableFutureDates" />
                                </q-popup-proxy>
                            </q-input>
                            <div class="form-error text-negative" v-show="errors.has( 'update_expense_date' )">{{ errors.first( 'update_expense_date' ) }}</div>
                        </div>
                        <div class="btn-actions row items-center justify-between q-col-gutter-x-sm">
                            <div>
                                <q-btn
                                    label="Delete"
                                    color="info"
                                    no-caps
                                    unelevated
                                    :disable="update_expense_submitting"
                                    @click.prevent="dialog_delete_expense = true"
                                />
                            </div>
                            <div class="row items-center justify-end q-col-gutter-x-sm">
                                <div>
                                    <q-btn
                                        label="Cancel"
                                        color="negative"
                                        no-caps
                                        unelevated
                                        v-close-popup
                                        :disable="update_expense_submitting"
                                        @click.prevent="hideDialogAddExpense()"
                                    />
                                </div>
                                <div>
                                    <q-btn
                                        label="Update"
                                        color="positive"
                                        :loading="update_expense_submitting"
                                        no-caps
                                        unelevated
                                        @click.prevent="updateExpense()"
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
        <q-dialog v-model="dialog_delete_expense" persistent>
            <q-card square class="dialog-role">
                <q-card-section class="q-pa-lg">
                    <div class="row items-center q-mb-lg">
                        <q-icon name="mdi-alert-circle" size="40px" color="warning" />
                        <div class="col q-pl-sm">Are you sure want to delete this expense?</div>
                    </div>
                    <div class="btn-actions row items-center justify-end q-col-gutter-sm">
                        <div>
                            <q-btn
                                label="No"
                                color="negative"
                                no-caps
                                unelevated
                                v-close-popup
                                :disable="delete_expense_submitting"
                            />
                        </div>
                        <div>
                            <q-btn
                                label="Yes"
                                color="positive"
                                no-caps
                                unelevated
                                :loading="delete_expense_submitting"
                                @click.prevent="deleteExpense()"
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
    import { date } from 'quasar'

    export default {
        data() {
            return {
                expenses: [],
                show_loader: true,

                //add expense
                dialog_add_expense: false,
                add_expense_category: '',
                add_expense_amount: '',
                add_expense_date: date.formatDate( new Date(), 'YYYY/MM/DD' ),
                expense_categories: [],
                add_expense_submitting: false,

                //update expense
                dialog_update_expense: false,
                update_expense_submitting: false,
                update_expense_category: '',
                update_expense_date: '',
                update_expense_amount: '',

                //delete expense
                dialog_delete_expense: false, 
                delete_expense_id: '',
                delete_expense_submitting: false,
            }
        },
        methods: {
            retrieveExpenses() {
                this.show_loader = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'expenses/lists'
                })
                .then( response => {
                    this.expenses = response.data.expenses
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            },
            retrieveCategories() {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'expenses/categories/lists?dropdown_list=true'
                })
                .then( response => {
                    this.expense_categories = response.data.categories
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            },
            addExpense() {
                this.add_expense_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'expenses/add',
                            data: {
                                category: this.add_expense_category,
                                amount: this.add_expense_amount,
                                entry_date: this.add_expense_date
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogAddExpense()
                            this.retrieveExpenses()
                        })
                        .catch( error => {
                            console.log( error )
                            this.add_expense_submitting = false
                        })
                    } else {
                        this.add_expense_submitting = false
                    }
                })
            },
            updateExpense() {
                this.update_expense_submitting = true
                this.errors.clear()

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'expenses/update',
                            data: {
                                expense_id: this.update_expense_id,
                                category: this.update_expense_category,
                                amount: this.update_expense_amount,
                                date: this.update_expense_date,
                            }
                        })
                        .then( response => {
                            this.$q.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.hideDialogUpdateExpense()
                            this.retrieveExpenses()
                        })
                        .catch( error => {
                            this.$q.notify({
                                message: error.response.data.message,
                                position: 'top',
                                color: 'negative',
                                icon: 'mdi-check'
                            })
                            this.update_expense_submitting = false
                        })
                    } else {
                        this.update_expense_submitting = false
                    }
                })
            },
            deleteExpense() {
                this.delete_expense_submitting = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'post',
                    url: 'expenses/delete',
                    data: {
                        expense_id: this.delete_expense_id
                    }
                })
                .then( response => {
                    this.$q.notify({
                        message: response.data.message,
                        position: 'top',
                        color: 'positive',
                        icon: 'mdi-check'
                    })
                    this.hideDialogUpdateExpense()
                    this.dialog_delete_expense = false
                    this.retrieveExpenses()
                })
                .catch( error => {
                    this.$q.notify({
                        message: error.response.data.message,
                        position: 'top',
                        color: 'negative',
                        icon: 'mdi-close'
                    })
                    this.delete_expense_submitting = false
                })
            },
            showDialogUpdateExpense( id, category, amount, date ) {
                this.dialog_update_expense = true
                this.update_expense_id = id
                this.delete_expense_id = id
                this.update_expense_category = category
                this.update_expense_amount = amount
                this.update_expense_date = date
            },
            hideDialogAddExpense() {
                this.dialog_add_expense = false
                this.add_expense_category = '',
                this.add_expense_amount = ''
                this.add_expense_date = date.formatDate( new Date(), 'YYYY/MM/DD' )
                this.add_expense_submitting = false
            },
            hideDialogUpdateExpense() {
                this.dialog_update_expense = false
                this.update_expense_category = ''
                this.update_expense_amount = ''
                this.update_expense_date = ''
                this.update_expense_submitting = false
            },
            disableFutureDates( date ) {
                return date <= this.add_expense_date
            }
        },
        mounted() {
            this.retrieveExpenses()
            this.retrieveCategories()
            const dictionary = {
                en: {
                    attributes: {
                        add_expense_category: 'Category',
                        add_expense_amount: 'Amount',
                        add_expense_date: 'Date',
                        update_expense_category: 'Category',
                        update_expense_amount: 'Amount',
                        update_expense_date: 'Date',
                    }
                }
            }
            this.$validator.localize( dictionary )
        },
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