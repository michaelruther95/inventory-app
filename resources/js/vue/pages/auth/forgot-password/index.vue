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
				<el-input type="email" placeholder="Enter Email Address Here..." size="small" v-model="form.email_address"></el-input>
				<p class="text-right">
					<small><router-link :to="{ name: 'login_page' }">Remembered Your Password?</router-link></small>
				</p>

				<hr>

				<div class="text-right">
					<el-button type="success" native-type="submit" size="small" :disabled="form.submitting">
						<i class="el-icon-check"></i> Send Reset Link
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
					submitting: false
				}
			}
		},

		methods: {
			submit(){
				alert("SUBMITTING FORM!");
				this.form.submitting = true;
			}
		}
	}
</script>