import { date } from 'quasar'

export default async ({ Vue }) => {
    Vue.filter( 'formatDate', function( value ) {
        return date.formatDate( value, 'YYYY-MM-DD' )
    })

    Vue.filter( 'formatPrice', function( value ) {
        let val = ( value / 1 ).toFixed( 2 ).replace( ',', '.' )
        return val.toString().replace( /\B(?=(\d{3})+(?!\d))/g, ',' )
    })
}
