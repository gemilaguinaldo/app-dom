export default ({ router }) => {
    router.beforeEach(( to, from, next ) => {
        if ( to.matched.some( record => record.meta.requiresAuth ) ) {
            if ( !store.getters[ 'tokens/isLoggedIn' ] ) {
                next({
                    name: 'login'
                })
            } else {
                next()
            }
        } else if ( to.matched.some( record => record.meta.requiresIfLoggedIn ) ) {
            if ( store.getters[ 'tokens/isLoggedIn' ] ) {
                next({
                    name: 'home'
                })
            } else {
                next()
            }
        } else {
            next()
        }
    })
}
