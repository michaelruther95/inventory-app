<template>
	<div id="supplier-form-component">
		<label class="input-label"><small>Supplier Name</small></label>
		<el-input placeholder="Enter Supplier's Name Here..." size="small" v-model="form.supplier_name"></el-input>
		<p class="text-danger"><small>{{ api_validators.supplier_name }}</small></p>

		<label class="input-label"><small>Contact Number</small></label>
		<el-input v-mask="'##########'" placeholder="Enter Contact Number Here... (Ex. 9361320927)" size="small" v-model="form.contact_number">
			<template slot="prepend">
				+63
			</template>
		</el-input>
		<p class="text-danger"><small>{{ api_validators.contact_number }}</small></p>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['action', 'record', 'submitting'],
		watch: {
			'submitting': function(val){
				console.log("SUPPLIER FORM SUBMITTING: ", val);
				if(val){
					this.$store.dispatch('pageLoader', { display: true, message: 'Saving Supplier Information, Please Wait...' });
					this.clearApiValidators();

					let uri = '/api/create-new-supplier';
					if(this.action == 'update'){
						uri = '/api/update-supplier';
					}

					this.$axios.post(uri, this.form).then((response) => {
						this.$store.dispatch('pageLoader', { display: false, message: '' });

						let data_to_return = {
							'suppliers': response.data.suppliers,
							'success': true
						};

						this.$emit('closeform', data_to_return);
					}).catch((error) => {
						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									this.api_validators[key] = error.response.data.errors[key][0]
								}
							}
						}

						this.$emit('aftersubmit', false);
						this.$store.dispatch('pageLoader', { display: false, message: '' });
					});
				}
			}
		},
		data(){
			return {
				form: {
					id: '',
					supplier_name: '',
					contact_number: ''
				},
				api_validators: {
					id: '',
					supplier_name: '',
					contact_number: ''
				}
			}
		},
		created(){
			console.log("SUPPLIER FORM RECORD: ", this.record);

			if(this.action == 'update'){
				for(let key in this.form){
					this.form[key] = this.record.information[key];
				}

				this.form.id = this.record.id;

				console.log("UPDATE FORM: ", this.form);
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