<template>
	<div id="dashboard-navbar-component">
		<nav class="navbar navbar-expand navbar-dark bg-dark fixed-top">
			<div class="container">
				<span class="navbar-brand">VETMASYS</span>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav">
						<li class="nav-item pr-2" v-for="navbar_item in navbar_items">
							<router-link :to="{ name: navbar_item.route_name }" class="nav-link" v-bind:class="{
								'active': navbar_item.route_name == $route.name
							}" v-if="navbar_item.allowed_types.includes(current_user_type)">
								<small>{{ navbar_item.label }}</small>
							</router-link>
						</li>
					</ul>
					<ul class="navbar-nav ml-auto"> 
						<li class="nav-item dropdown">
							<a href="javascript:void(0)" class="nav-link">
								<el-dropdown @command="handleUserNavDropdown">
									<span class="el-dropdown-link">
										<small>
											<i class="el-icon-user"></i> Current User
										</small>
									</span>
									<el-dropdown-menu slot="dropdown">
										<el-dropdown-item>My Profile</el-dropdown-item>
										<el-dropdown-item command="account_settings">Account Settings</el-dropdown-item>
										<el-dropdown-item command="logout" divided>
											Logout
										</el-dropdown-item>
									</el-dropdown-menu>
								</el-dropdown>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<el-dialog 
			title="Account Settings" 
			:visible.sync="show_account_settings_modal" 
			width="50%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>
			<account-settings
				:submitting="account_settings_submitting"
				v-if="show_account_settings_modal"
				v-on:aftersubmit="handleAfterSubmit($event)"
			></account-settings>
			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" v-on:click="account_settings_submitting = true">
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_account_settings_modal = false">
					Close
				</el-button>
			</span>
		</el-dialog>

	</div>
</template>
<style type="text/css">
	#dashboard-navbar-component .el-dropdown {
		color: unset !important;
		font-size: unset !important;
	}
</style>
<script type="text/javascript">
	export default {
		components: {
			'account-settings': require('../forms/account-settings.vue').default
		},
		data(){
			return {
				show_profile_modal: false,
				show_account_settings_modal: false,
				account_settings_submitting: false,

				current_user_type: '',

				navbar_items: [
					{
						label: 'Home',
						route_name: 'dashboard_home',
						allowed_types: ['doctor', 'employee']
					},
					{
						label: 'User Management',
						route_name: 'user_management',
						allowed_types: ['doctor']
					},
					{
						label: 'Pet Owners List',
						route_name: 'patients_list',
						allowed_types: ['doctor', 'employee']
					},
					{
						label: 'Appointments',
						route_name: 'appointments',
						allowed_types: ['doctor', 'employee']
					},
					{
						label: 'Inventory',
						route_name: 'inventory',
						allowed_types: ['doctor', 'employee']
					},
					{
						label: 'Invoices',
						route_name: 'invoices',
						allowed_types: ['doctor', 'employee']
					}
				]
			}
		},

		created(){
			console.log("CURRENT ROUTE: ", this.$route);
			console.log("CURRENT USER INFO: ", JSON.parse(localStorage.getItem('user_info')));

			let current_user_info = JSON.parse(localStorage.getItem('user_info'));

			this.current_user_type = current_user_info.user_role.role.name;
		},

		methods: {
			handleUserNavDropdown(command){
				if(command == 'logout'){
					this.logout();
				}
				if(command == 'account_settings'){
					this.show_account_settings_modal = true;
				}
			},
			logout(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Logging Out, Please Wait...' });

				this.$axios.delete('/api/logout', {}).then(async (response) => {
					localStorage.removeItem('token');
					await this.$store.dispatch('setToken', null);

					localStorage.removeItem('user_info');
					await this.$store.dispatch('setUserInfo', null);

					this.$router.push({ name: 'login_page' });
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},
			handleAfterSubmit(param){
				if(param.success){
					this.$message({
			          	message: 'Account information successfully saved.',
			          	showClose: true,
			          	type: 'success'
			        });
					this.show_account_settings_modal = false;
				}

				this.account_settings_submitting = false;
			}
		}
	}
</script>