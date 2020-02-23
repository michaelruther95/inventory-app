<template>
	<el-dialog
	  	:title="form_title"
	  	:visible.sync="dialog_is_visible"
	  	:show-close="false"
	  	:close-on-click-modal="false"
	  	:close-on-press-escape="false"
	 	width="30%"
	>	
		<form v-on:submit.prevent="submit()">
			<label class="input-label"><small>First Name</small></label>
			<el-input placeholder="Enter First Name Here..." size="small" v-model="form.first_name"></el-input>
			<p class="text-danger"><small>{{ api_validators.first_name }}</small></p>

			<label class="input-label"><small>Last Name</small></label>
			<el-input placeholder="Enter Last Name Here..." size="small" v-model="form.last_name"></el-input>
			<p class="text-danger"><small>{{ api_validators.last_name }}</small></p>

			<label class="input-label"><small>Email Address</small></label>
			<el-input type="email" placeholder="Enter Email Address Here..." size="small" v-model="form.email_address"></el-input>
			<p class="text-danger"><small>{{ api_validators.email_address }}</small></p>

			<label class="input-label"><small>Phone Number</small></label>
			<el-input type="text" v-mask="'09#########'" placeholder="Enter Phone Number Here..." size="small" v-model="form.phone_number"></el-input>
			<p class="text-danger"><small>{{ api_validators.phone_number }}</small></p>

			<label class="input-label"><small>Address</small></label>
			<el-input type="text" placeholder="Enter Address Here..." size="small" v-model="form.address"></el-input>
			<p class="text-danger"><small>{{ api_validators.address }}</small></p>

		  	<div slot="footer" class="dialog-footer text-right mt-5">
		    	<el-button size="small" native-type="button" type="danger" v-on:click="dialog_is_visible = false; current_record = null;">Cancel</el-button>
		    	<el-button size="small" native-type="submit" type="success">Submit</el-button>
		  	</div>
		</form>
	</el-dialog>
</template>
<script type="text/javascript">
	export default {
		props: ['show_dialog', 'form_title', 'action', 'passed_data'],
		watch: {
			show_dialog(val) {
				this.dialog_is_visible = val;

				for(let key in this.form){
					this.form[key] = '';
				}

				this.clearApiValidators();

				if(this.action == 'update'){
					console.log("PASSED DATA: ", this.passed_data);
					for(let key in this.form){
						this.form[key] = this.passed_data['raw_info']['information'][key];
					}

					this.form['id'] = this.passed_data['id'];

					console.log("UPDATE FORM: ", this.form);
				}
			},
			dialog_is_visible(val){
				if(!val){
					this.$emit('after_close', { current_record: this.current_record, action: this.action });
				}	
			}
		},
		data(){
			return {
				dialog_is_visible: false,
				current_record: null,

				form: {
					id: '',
					first_name: '',
					last_name: '',
					email_address: '',
					phone_number: '',
					address: '',
					action: ''
				},

				api_validators: {
					id: '',
					first_name: '',
					last_name: '',
					email_address: '',
					phone_number: '',
					address: '',
					action: ''
				},
			}
		},

		methods: {
			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Submitting Patient Form, Please Wait...' });
				this.clearApiValidators();

				this.form.action = this.action;

				let api_url = null;
				if(this.action == 'create'){
					api_url = '/api/create-new-patient';
				}
				if(this.action == 'update'){
					api_url = '/api/update-patient';
				}

				if(api_url != null){
					this.$axios.post(api_url, this.form).then((response) => {
						this.current_record = response.data.patient_info;
						this.dialog_is_visible = false;

						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$message({
				          	message: 'Patient successfully saved.',
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

						this.$store.dispatch('pageLoader', { display: false, message: '' });
					});
				}
			},

			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		},
	}
</script>