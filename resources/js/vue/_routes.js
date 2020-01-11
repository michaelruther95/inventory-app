import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes: [
		// -------------------------------------------------------------------------
		// Authentication Routes
		// -------------------------------------------------------------------------
		{
			path: '/',
			component: require('./pages/auth/login/index.vue').default,
			name: 'login_page',
			meta: {
				layout: 'default',
				authorized_users: ['guest']
			}
		},
		{
			path: '/forgot-password',
			component: require('./pages/auth/forgot-password/index.vue').default,
			name: 'forgot_password',
			meta: {
				layout: 'default',
				authorized_users: ['guest']
			}
		},
		{
			path: '/reset-password/:token',
			component: require('./pages/auth/reset-password/index.vue').default,
			name: 'reset_password',
			meta: {
				layout: 'default',
				authorized_users: ['guest']
			}
		},
		// -------------------------------------------------------------------------


		// -------------------------------------------------------------------------
		// Dashboard Routes
		// -------------------------------------------------------------------------
		{
			path: '/dashboard',
			component: require('./pages/dashboard/home/index.vue').default,
			name: 'dashboard_home',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
			}
		}
		// -------------------------------------------------------------------------
	]
});

export default router