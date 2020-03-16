<template>
	<div id="reset-password-page-component" class="w-100">
		<div class="row m-0 justify-content-center">
			<form v-on:submit.prevent="submit()" class="col-lg-4">
				<h2 class="mb-0">
					Inventory App
				</h2>
				<small>RESET PASSWORD</small>
				<hr>
				
				<small>
					Forgot your password? Don't worry, just fill up the form below and we'll send you a reset link.
				</small>

				<hr>

				<label class="input-label"><small>Email Address</small></label>
				<el-input type="email" placeholder="Enter Email Address Here..." size="small" v-model="form.email_address"></el-input>
				<p class="text-danger"><small>{{ api_validators.email_address }}</small></p>

				<label class="input-label"><small>New Password</small></label>
				<el-input type="password" placeholder="Enter New Password Here..." size="small" v-model="form.new_password" :show-password="true"></el-input>
				<p class="text-danger"><small>{{ api_validators.new_password }}</small></p>

				<label class="input-label"><small>Confirm New Password</small></label>
				<el-input type="password" placeholder="Re-enter New Password Password Here..." size="small" v-model="form.confirm_new_password" :show-password="true"></el-input>
				<p class="text-danger"><small>{{ api_validators.confirm_new_password }}</small></p>

				<div class="mt-5">
					<hr>

					<div class="text-right">
						<el-button type="success" native-type="submit" size="small">
							<i class="el-icon-check"></i> Reset Account Password
						</el-button>
					</div>
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
					new_password: '',
					confirm_new_password: '',
					token: ''
				},
				api_validators: {
					email_address: '',
					new_password: '',
					confirm_new_password: '',
					token: '',
				}
			}
		},
		created(){
			this.form.token = this.$route.params.token;
			this.$store.dispatch('pageLoader', { display: false, message: '' });
		},
		methods: {
			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Resetting password. Please wait...' });
				this.clearApiValidator();
				this.$axios.post('/api/reset-password', this.form).then((response) => {
					this.$message({
			          	message: "Account's password successfully changed.",
			          	showClose: true,
			          	type: 'success'
			        });

					this.$router.push({ name: 'login_page' });
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					if(error.response.status == 422){
						for(let key in error.response.data.errors){
							this.api_validators[key] = error.response.data.errors[key][0];
						}
					}
					if(error.response.status == 403){
						this.$message({
				          	message: error.response.data.error,
				          	showClose: true,
				          	type: 'danger'
				        });
					}

					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},
			clearApiValidator(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>