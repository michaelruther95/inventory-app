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
				page_title: 'Dashboard Home'
			}
		},
		{
			path: '/dashboard/user-management',
			component: require('./pages/dashboard/user-management/index.vue').default,
			name: 'user_management',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin'],
				page_title: 'User Management'
			}
		},
		{
			path: '/dashboard/pet-owners-list',
			component: require('./pages/dashboard/patients-list/index.vue').default,
			name: 'patients_list',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Pet Owners List'
			}
		},
		{
			path: '/dashboard/appointments',
			component: require('./pages/dashboard/appointments/index.vue').default,
			name: 'appointments',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Scheduler'
			}
		},
		{
			path: '/dashboard/inventory',
			component: require('./pages/dashboard/inventory/index.vue').default,
			name: 'inventory',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Inventory'
			}
		},
		{
			path: '/dashboard/invoices',
			component: require('./pages/dashboard/invoices/index.vue').default,
			name: 'invoices',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Invoice List'
			}
		},
		{
			path: '/dashboard/suppliers',
			component: require('./pages/dashboard/suppliers/index.vue').default,
			name: 'suppliers',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Suppliers Manifest'
			}
		},
		{
			path: '/dashboard/medical-records',
			component: require('./pages/dashboard/medical-records/index.vue').default,
			name: 'medical_records',
			meta: {
				layout: 'dashboard',
				authorized_users: ['admin', 'assistant'],
				page_title: 'Medical Records'
			}
		},
		// -------------------------------------------------------------------------
	]
});

export default router