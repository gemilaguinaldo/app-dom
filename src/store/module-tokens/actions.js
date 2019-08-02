export function retrieveToken( context, data ) {
    return new Promise(( resolve, reject ) => {
        axios({
            method: 'post',
            url: 'users/login',
            data: {
                email_address: data.email_address,
                password: data.password
            }
        })
        .then( response => {
            const token = response.data.token
            context.commit( 'retrieveToken', token )
            resolve( response )
        })
        .catch( error => {
            reject( error )
        })
    })
}

export function destroyToken( context ) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + context.state.token

    if ( store.getters[ 'tokens/isLoggedIn' ] ) {
        return new Promise(( resolve, reject ) => {
            axios({
                method: 'post',
                url: 'users/logout'
            })
            .then( response => {
                context.commit( 'destroyToken' )
                resolve( response )
            })
            .catch( error => {
                context.commit( 'destroyToken' )
                reject( error )
            })
        })
    }
}