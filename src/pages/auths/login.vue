<template>
    <div class="page-content">
        <div class="form-container row items-center justify-center">
            <q-card class="q-pl-xl q-pr-xl q-pt-lg q-pb-xl" style="width: 100%; max-width: 480px;">
                <q-card-section style="padding: 0;">
                    <h1 class="text-center q-mb-lg">Login Your Account</h1>
                    <form action="#" @submit.prevent="">
                        <div class="q-mb-lg">
                            <div class="q-mb-xs">Email Address</div>
                            <q-input
                                type="email"
                                name="email_address"
                                v-model="email_address"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="login_submitting"
                                :error="errors.has( 'email_address' )"
                                v-validate="'required|email'"
                                data-vv-validate-on="none"
                                @keyup.enter="login()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'email_address' )">{{ errors.first( 'email_address' ) }}</div>
                        </div>
                        <div class="q-mb-xl">
                            <div class="q-mb-xs">Password</div>
                            <q-input
                                type="password"
                                name="password"
                                v-model="password"
                                class="q-pb-none"
                                filled
                                dense
                                square
                                clearable
                                :disable="login_submitting"
                                :error="errors.has( 'password' )"
                                v-validate="'required'"
                                data-vv-validate-on="none"
                                @keyup.enter="login()"
                            />
                            <div class="form-error text-negative" v-show="errors.has( 'password' )">{{ errors.first( 'password' ) }}</div>
                        </div>
                        <q-btn
                            label="Log In"
                            class="full-width"
                            color="primary"
                            :loading="login_submitting"
                            no-caps
                            unelevated
                            @click.prevent="login()"
                        >
                            <template v-slot:loading>
                                <q-spinner-dots />
                            </template>
                        </q-btn>
                    </form>
                </q-card-section>
            </q-card>
        </div>
    </div>
</template>
<script>

export default {
    components: {

    },
    data() {
        return {
            email_address: '',
            password: '',
            login_submitting: '',
        }
    },
    methods: {
        login() {
            this.login_submitting = true
            this.errors.clear()

            this.$validator.validateAll(( result ) => {
                if ( result ) {
                    store.dispatch( 'tokens/retrieveToken', {
                        email_address: this.email_address,
                        password: this.password
                    })
                    .then( response => {
                        store.dispatch( 'users/retrieveDetails' )
                        .then( response => {
                            this.$router.push({
                                name: 'home'
                            })
                        })
                    })
                    .catch( error => {
                        this.$q.notify({
                            message: error.response.data.message,
                            color: 'negative',
                            position: 'top',
                            icon: 'mdi-close'
                        })
                        this.login_submitting = false
                    })
                } else {
                    this.login_submitting = false
                }
            })
        }

    },
    mounted() {
        const dictionary = {
            en: {
                attributes: {
                    email_address: 'Email Address',
                    password: 'Password',
                }
            }
        }
        this.$validator.localize( dictionary )
    },

    computed: {

    }
}
</script>
<style lang="stylus" scoped>
    .page-content {
        .form-container {
            height: 100vh;
        }
    }
</style>