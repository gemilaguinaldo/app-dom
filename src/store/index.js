import Vue from 'vue'
import Vuex from 'vuex'

import tokens from './module-tokens'
import users from './module-users'

Vue.use(Vuex)

export default function () {
    const Store = new Vuex.Store({
        modules: {
            tokens,
            users,
        },

        // enable strict mode (adds overhead!)
        // for dev mode only
        strict: process.env.DEV,
        plugins: [
            createPersistedState({
                key: 'expense_manager'
            })
        ]
    })

    window.store = Store
    return Store
}
