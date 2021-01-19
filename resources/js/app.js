require('./bootstrap');

window.Vue = require('vue');

Vue.prototype._ = _;

import App from './views/App'
import router from './router'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrashAlt, faPencilAlt, faPlusCircle, faStar, faSpinner, faCheck, faTimes } from '@fortawesome/free-solid-svg-icons'
library.add(faTrashAlt, faPencilAlt, faPlusCircle, faStar, faSpinner, faCheck, faTimes)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('pagination', require('laravel-vue-pagination'));

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

require('./directives')
require('./filters')

const app = new Vue({
    el: '#app',
    components: { App },
    router: router.router,
});
