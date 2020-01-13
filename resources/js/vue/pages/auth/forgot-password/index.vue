<template>
	<div id="forgot-password-page-component" class="w-100">
		<div class="row m-0 justify-content-center">
			<form v-on:submit.prevent="submit()" class="col-lg-4">
				<h2 class="mb-0">
					Inventory App
				</h2>
				<small>FORGOT PASSWORD</small>
				<hr>
				
				<small>
					Forgot your password? Don't worry, just fill up the form below and we'll send you a reset link.
				</small>

				<hr>

				<label class="input-label"><small>Email Address</small></label>
				<el-input type="email" placeholder="Enter Email Address Here..." size="small" v-model="form.email_address" required></el-input>
				<p class="text-danger"><small>{{ api_validators.email_address }}</small></p>

				<div class="mt-4">
					<hr>

					<div class="row m-0">
						<div class="col-6 px-0">
							<small><router-link :to="{ name: 'login_page' }">Remembered Your Password?</router-link></small>
						</div>
						<div class="col-6 px-0 text-right">
							<el-button type="success" native-type="submit" size="small">
								<i class="el-icon-check"></i> Send Reset Link
							</el-button>
						</div>
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
					action: 'forgot-password'
				},

				api_validators: {
					email_address: '',
					action : ''
				}
			}
		},

		methods: {
			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Sending Reset Link, Please Wait...' });
				this.clearApiValdators();

				this.$axios.post('/api/forgot-password', this.form).then((response) => {
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Reset link has been successfully sent to your email.',
			          	type: 'success'
			        });
				}).catch((error) => {
					if(error.response){
						if(error.response.status == 422){
							for(let key in error.response.data.errors){
								this.api_validators[key] = error.response.data.errors[key][0];
							}
						}
					}

					this.$message({
			          	message: 'Something went wrong. Please try again.',
			          	type: 'error'
			        });

					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},

			clearApiValdators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>