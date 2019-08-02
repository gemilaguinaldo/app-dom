<template>
    <div class="page-content q-pa-xl">
        <h4 class="q-mb-lg"><strong>{{ this.$route.meta.pageTitle }}</strong></h4>
        <q-card style="width: 480px;" class="dialog-role" square>
            <q-card-section style="padding: 0;">
                <form action="#" @submit.prevent="">
                    <div class="q-mb-lg">
                        <div class="q-mb-xs">New Password</div>
                        <q-input
                            type="password"
                            name="password"
                            v-model="password"
                            class="q-pb-none"
                            filled
                            dense
                            square
                            :disable="change_password_submitting"
                            :error="errors.has( 'password' )"
                            v-validate="'required'"
                            data-vv-validate-on="none"
                            ref="password"
                            @keyup.enter="changePassword()"
                        />
                        <div class="form-error text-negative" v-show="errors.has( 'password' )">{{ errors.first( 'password' ) }}</div>
                    </div>
                    <div class="q-mb-lg">
                        <div class="q-mb-xs">Confirm Password</div>
                        <q-input
                            type="password"
                            name="confirm_password"
                            v-model="confirm_password"
                            class="q-pb-none"
                            filled
                            dense
                            square
                            :disable="change_password_submitting"
                            :error="errors.has( 'confirm_password' )"
                            v-validate="'required|confirmed:password'"
                            data-vv-validate-on="none"
                            @keyup.enter="changePassword()"
                        />
                        <div class="form-error text-negative" v-show="errors.has( 'confirm_password' )">{{ errors.first( 'confirm_password' ) }}</div>
                    </div>
                    <div class="btn-actions">
                        <q-btn
                            label="Change Password"
                            color="positive"
                            class="full-width"
                            :loading="change_password_submitting"
                            no-caps
                            unelevated
                            @click.prevent="changePassword()"
                        >
                            <template v-slot:loading>
                                <q-spinner-dots />
                            </template>
                        </q-btn>
                    </div>
                </form>
            </q-card-section>
        </q-card>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                password: '',
                confirm_password: '',
                change_password_submitting: false,
            }
        },
        methods: {
            changePassword() {
                this.change_password_submitting = true
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token

                this.$validator.validateAll().then(( result ) => {
                    if ( result ) {
                        axios({
                            method: 'post',
                            url: 'users/change-password',
                            data: {
                                password: this.password
                            }
                        })
                        .then( response => {
                            this.$expense_manager.notify({
                                message: response.data.message,
                                position: 'top',
                                color: 'positive',
                                icon: 'mdi-check'
                            })
                            this.change_password_submitting = false
                        })
                    } else {
                        this.change_password_submitting = false
                    }
                })
            }
        },
        mounted() {
            const dictionary = {
                en: {
                    attributes: {
                        password: 'Password',
                        confirm_password: 'Confirm Password',
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