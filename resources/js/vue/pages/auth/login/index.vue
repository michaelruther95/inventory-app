<template>
	<div id="index-page-component" class="w-100">
		<div class="row m-0 justify-content-center">
			<form v-on:submit.prevent="submit()" class="col-lg-4">
				<h2 class="mb-0">
					VETMASYS
				</h2>
				<small>LOGIN PAGE</small>
				<hr>

				<div class="mb-4" v-if="error_message">
					<el-alert
						title="Authentication Failed"
						type="warning"
						effect="dark"
						:closable="false"
						:description="error_message"
						show-icon
					>
					</el-alert>
				</div>

				<label class="input-label"><small>Email Address</small></label>
				<el-input type="email" placeholder="Enter Email Address Here..." size="small" v-model="form.email_address"></el-input>
				<p><small></small></p>

				<label class="input-label"><small>Password</small></label>
				<el-input type="password" placeholder="Enter Password Here..." size="small" v-model="form.password" :show-password="true"></el-input>
				<p class="text-right">
					<small><router-link :to="{ name: 'forgot_password' }">Forgot Password?</router-link></small>
				</p>

				<hr>

				<div class="text-right">
					<el-button type="success" native-type="submit" size="small">
						<i class="el-icon-check"></i> Login Account
					</el-button>
				</div>
			</form>	
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				form: {
					email_address: '',
					password: '',
					action: 'login'
				},

				error_message: ''
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: false, message: '' });
		},
		methods: {
			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Logging In, Please Wait...' });
				this.error_message = '';

				this.$axios.post('/api/login', this.form).then(async (response) => {
					localStorage.setItem('token', response.data.token);
					await this.$store.dispatch('setToken', response.data.token);

					localStorage.setItem('user_info', JSON.stringify(response.data.user_info));
					await this.$store.dispatch('setUserInfo', response.data.user_info);
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$router.push({ name: 'dashboard_home' });
				}).catch((error) => {
					if(error.response){
						if(error.response.status == 403){
							this.error_message = error.response.data.error_message;
						}
					}
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			}
		}
	}
</script>