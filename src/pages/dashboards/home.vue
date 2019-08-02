<template>
    <div class="page-content q-pa-xl">
        <template v-if="show_loader">
            <q-inner-loading :showing="show_loader">
                <q-spinner-dots size="50px" />
            </q-inner-loading>
        </template>
        <template v-else>
            <h4 class="q-mb-lg"><strong>{{ this.$route.meta.pageTitle }}</strong></h4>
            <table class="data-listing q-mb-xl">
                <thead>
                    <tr>
                        <th>Expense Categories</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="( expense, index ) in expenses">
                        <tr :key="index">
                            <td>{{ expense.category }}</td>
                            <td>{{ expense.total | formatPrice }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                expenses: [],
                show_loader: true,
                chartdata: null,
                options: null
            }
        },
        methods: {
            retrieveExpenses() {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                axios({
                    method: 'get',
                    url: 'expenses/total'
                })
                .then( response => {
                    this.expenses = response.expenses
                    this.show_loader = false
                })
                .catch( error => {
                    console.log( error )
                })
            }
        },
       mounted() {
           this.retrieveExpenses()
       }
    }
</script>
<style lang="stylus" scoped>

</style>