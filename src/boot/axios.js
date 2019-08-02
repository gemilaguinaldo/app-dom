import axios from 'axios'

export default async ({ Vue }) => {
    Vue.prototype.$axios = axios
    window.axios = axios

    /* dev */
    axios.defaults.baseURL = 'http://localhost/quasar/expense-manager/system/api/'
}
