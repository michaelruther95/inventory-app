<template>
	<div id="dashboard-navbar-component">
		<nav class="navbar navbar-expand navbar-dark bg-dark fixed-top">
			<div class="container">
				<a class="navbar-brand" href="#">Inventory App</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav">
						<li class="nav-item pr-2" v-for="navbar_item in navbar_items">
							<router-link :to="{ name: navbar_item.route_name }" class="nav-link" v-bind:class="{
								'active': navbar_item.route_name == $route.name
							}">
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
											<i class="el-icon-user"></i> Michael Ruther
										</small>
									</span>
									<el-dropdown-menu slot="dropdown">
										<el-dropdown-item>My Profile</el-dropdown-item>
										<el-dropdown-item>Account Settings</el-dropdown-item>
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
		data(){
			return {
				show_profile_modal: false,
				show_account_settings_modal: false,

				navbar_items: [
					{
						label: 'Home',
						route_name: 'dashboard_home'
					},
					{
						label: 'User Management',
						route_name: 'user_management'
					},
					{
						label: 'Patients List',
						route_name: 'patients_list'
					},
					{
						label: 'Schedules',
						route_name: 'schedules'
					},
					{
						label: 'Inventory',
						route_name: 'inventory'
					}
				]
			}
		},

		created(){
			console.log("CURRENT ROUTE: ", this.$route);
		},

		methods: {
			handleUserNavDropdown(command){
				if(command == 'logout'){
					this.logout();
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
			}
		}
	}
</script>