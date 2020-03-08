<template>
	<div id="user-form-component">
		<label class="input-label"><small>First Name</small></label>
		<el-input type="text" placeholder="Enter First Name Here..." size="small" v-model="form.first_name"></el-input>
		<p class="text-danger"><small>{{ api_validators.first_name }}</small></p>

		<label class="input-label"><small>Last Name</small></label>
		<el-input type="text" placeholder="Enter Last Name Here..." size="small" v-model="form.last_name"></el-input>
		<p class="text-danger"><small>{{ api_validators.last_name }}</small></p>

		<label class="input-label"><small>Email</small></label>
		<el-input type="text" placeholder="Enter Email Here..." size="small" v-model="form.email"></el-input>
		<p class="text-danger"><small>{{ api_validators.email }}</small></p>

		<label class="input-label"><small>Gender</small></label>
		<el-select size="small" v-model="form.gender" slot="prepend" placeholder="-- Select Gender Here --">
			<el-option label="-- Select Gender Here --" value=""></el-option>
			<el-option label="Male" value="Male"></el-option>
			<el-option label="Female" value="Female"></el-option>
		</el-select>
		<p class="text-danger"><small>{{ api_validators.gender }}</small></p>

		<label class="input-label"><small>Account Type</small></label>
		<el-select size="small" v-model="form.account_type" slot="prepend" placeholder="-- Select Account Type Here --">
			<el-option label="-- Select Account Type Here --" value=""></el-option>
			<el-option label="Doctor" value="1"></el-option>
			<el-option label="Employee" value="2"></el-option>
		</el-select>
		<p class="text-danger"><small>{{ api_validators.account_type }}</small></p>
	</div>	
</template>
<script type="text/javascript">
	export default {
		props: ['user', 'submitting', 'action'],
		watch: {
			'submitting': function(val){
				if(val){
					this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Users List. Please Wait...' });
					this.clearApiValidators();

					let uri = '/api/create-new-user';
					this.form['action'] = 'create';
					if(this.action == 'update'){
						this.form['action'] = 'update';
						uri = '/api/update-user';
					}

					this.$axios.post(uri, this.form).then((response) => {
						this.$store.dispatch('pageLoader', { display: false, message: '' });

						this.$emit('aftersubmit', { submitting: false, data: response.data.user_info, action: this.action });
					}).catch((error) => {
						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									this.api_validators[key] = error.response.data.errors[key][0];
								}
							}
						}
						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$emit('aftersubmit', { submitting: false, data: null, action: this.action });
					});
				}
			}
		},
		data(){
			return {
				form: {
					first_name: '',
					last_name: '',
					gender: '',
					email: '',
					account_type: ''
				},

				api_validators: {
					first_name: '',
					last_name: '',
					gender: '',
					email: '',
					account_type: ''
				},
			}
		},
		created(){

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