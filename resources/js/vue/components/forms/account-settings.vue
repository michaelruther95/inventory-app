<template>
	<div id="account-settings-component">
		<div class="mb-5">
			<h6 class="font-weight-bold">Profile Information</h6>
			<hr>

			<label class="input-label"><small>First Name</small></label>
			<el-input size="small" v-model="form.first_name"></el-input>
			<p class="text-danger"><small>{{ api_validators.first_name }}</small></p>

			<label class="input-label"><small>Last Name</small></label>
			<el-input size="small" v-model="form.last_name"></el-input>
			<p class="text-danger"><small>{{ api_validators.last_name }}</small></p>

			<label class="input-label"><small>Gender</small></label>
			<el-select size="small" v-model="form.gender" slot="prepend" placeholder="-- Select Gender Here --">
				<el-option label="-- Select Gender Here --" value=""></el-option>
				<el-option label="Male" value="Male"></el-option>
				<el-option label="Female" value="Female"></el-option>
			</el-select>
			<p class="text-danger"><small>{{ api_validators.gender }}</small></p>

			<label class="input-label"><small>Email</small></label>
			<el-input size="small" v-model="form.email"></el-input>
			<p class="text-danger"><small>{{ api_validators.email }}</small></p>
		</div>

		<div class="mb-5" v-if="is_doctor">
			<h6 class="font-weight-bold">Consultation Fee</h6>
			<hr>

			<label class="input-label"><small>Consultation Fee</small></label>
			<el-input size="small" type="number" v-model="form.consultation_fee"></el-input>
			<p class="text-danger"><small>{{ api_validators.consultation_fee }}</small></p>
		</div>

		<div class="mb-5">
			<h6 class="font-weight-bold">Security</h6>
			<hr>

			<label class="input-label"><small>Old Password</small></label>
			<el-input type="password" size="small" v-model="form.old_password"></el-input>
			<p class="text-danger"><small>{{ api_validators.old_password }}</small></p>

			<label class="input-label"><small>New Password</small></label>
			<el-input type="password" size="small" v-model="form.new_password"></el-input>
			<p class="text-danger"><small>{{ api_validators.new_password }}</small></p>

			<label class="input-label"><small>Confirm New Password</small></label>
			<el-input type="password" size="small" v-model="form.confirm_new_password"></el-input>
			<p class="text-danger"><small>{{ api_validators.confirm_new_password }}</small></p>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['submitting'],
		data(){
			return {
				form: {
					first_name: '',
					last_name: '',
					gender: '',
					email: '',
					consultation_fee: '',
					old_password: '',
					new_password: '',
					confirm_new_password: ''
				},

				is_doctor: true,

				api_validators: {
					first_name: '',
					last_name: '',
					gender: '',
					email: '',
					consultation_fee: '',
					old_password: '',
					new_password: '',
					confirm_new_password: ''
				}
			}
		},

		watch: {
			'submitting': function(val){
				if(val){
					this.$store.dispatch('pageLoader', { display: true, message: 'Saving Information. Please Wait...' });
					this.clearApiValidators();

					this.$axios.post('/api/update-account', this.form).then((response) => {
						localStorage.setItem('user_info', JSON.stringify(response.data.user_info));
						this.$emit('aftersubmit', { success: true });
						this.$store.dispatch('pageLoader', { display: false, message: '' });
					}).catch((error) => {
						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									this.api_validators[key] = error.response.data.errors[key][0];
								}
							}
						}

						this.$emit('aftersubmit', { success: false });
						this.$store.dispatch('pageLoader', { display: false, message: '' });
					});
				}
			}
		},

		created(){
			let user_info = JSON.parse(localStorage.getItem('user_info'));
			console.log("ACCOUNT SETTINGS USER INFO: ", user_info);

			this.form['first_name'] = user_info.profile.first_name;
			this.form['last_name'] = user_info.profile.last_name;
			this.form['gender'] = user_info.profile.gender;
			this.form['email'] = user_info.email;

			if(!user_info.consultation_fee){
				this.is_doctor = false;
			}
			else{
				this.form['consultation_fee'] = user_info.consultation_fee.fee;
			}
		},

		methods: {
			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>