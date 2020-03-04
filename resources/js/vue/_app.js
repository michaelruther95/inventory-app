// -----------------------------------------------------
// Require Bootstrap Dependencies
// -----------------------------------------------------
require('bootstrap');
require('jquery');
// -----------------------------------------------------


// -----------------------------------------------------
// Main Imports
// -----------------------------------------------------
import Vue from 'vue';
import App from './app.vue';
import Router from './_routes.js';
// -----------------------------------------------------


// -----------------------------------------------------
// V-Mask Package Import
// -----------------------------------------------------
import VueMask from 'v-mask';
Vue.use(VueMask);
// -----------------------------------------------------


// -----------------------------------------------------
// Element UI Initiation
// -----------------------------------------------------
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en';
import ElementUIHelper from './packages/_element-ui-helper.js';
Vue.use(ElementUI, { locale });
Vue.use(ElementUIHelper);
// -----------------------------------------------------


// -----------------------------------------------------
// Axios Custom Package Initiation
// -----------------------------------------------------
import AxiosPackage from './packages/_axios.js';
Vue.use(AxiosPackage);
// -----------------------------------------------------


// -----------------------------------------------------
// Vue Moment Package Initiation
// -----------------------------------------------------
import VueMoment from 'vue-moment';
Vue.use(VueMoment);
// -----------------------------------------------------


// -----------------------------------------------------
// Vue2 Filter Package Initiation
// -----------------------------------------------------
import Vue2Filters from 'vue2-filters';
Vue.use(Vue2Filters);
// -----------------------------------------------------


// -----------------------------------------------------
// State Management Initiation
// -----------------------------------------------------
import StoreIndex from './store/_index.js';
import Vuex from 'vuex';
Vue.use(Vuex);
const store = new Vuex.Store(StoreIndex);
// -----------------------------------------------------


// -----------------------------------------------------
// Vueditor Package Import
// -----------------------------------------------------
import Trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';
Vue.use(Trumbowyg);
// -----------------------------------------------------

Router.beforeEach(
	(to, from, next) => {
		console.log("TO INFO: ", to);

		store.dispatch('pageLoader', { display: true, message: 'Redirecting, Please Wait...' });

		if(to.meta.authorized_users.includes('guest')){
			if(store.getters.authToken){
				next({ name: 'dashboard_home' });
			}
			else{
				next();
			}
			// store.dispatch('pageLoader', { display: false, message: '' });
		}
		else{
			console.log("STORE GETTERS: ", store.getters);

			if(!store.getters.authToken){
				console.log("NO TOKEN FOUND!");
				next({ name: 'login_page' });
				// store.dispatch('pageLoader', { display: false, message: '' });
			}
			else{
				console.log("TOKEN FOUND!");
				Vue.axios.get('/api/fetch-auth-info', {}).then((response) => {
					next();
					// store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					store.dispatch('setToken', null);
					store.dispatch('setUserInfo', {});
					localStorage.removeItem('user_info');
					localStorage.removeItem('token');

					next({ name: 'login_page' });
					// store.dispatch('pageLoader', { display: false, message: '' });
				});
			}
		}
	}
);


let app = new Vue({
	el: '#vue-app',
	render: h => h(App),
	router: Router,
	store,
	created(){
		console.log("VueJS Initiated!");
	}
});