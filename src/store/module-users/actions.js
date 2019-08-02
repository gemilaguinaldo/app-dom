export function retrieveDetails( context, data ) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.tokens.token
    
    return new Promise(( resolve, reject ) => {
        axios({
            method: 'get',
            url: 'users/details'
        })
        .then( response => {
            context.commit( 'retrieveDetails', response.data )
            resolve( response )
        })
        .catch( error => {
            reject( error )
        })
    })
}