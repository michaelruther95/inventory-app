<template>
	<div id="pet-form-component">
		<form v-on:submit.prevent="submit()">
			<label class="input-label"><small>Name</small></label>
			<el-input type="text" placeholder="Enter Pet's Name Here..." size="small" v-model="form.name"></el-input>
			<p class="text-danger"><small>{{ api_validators.name }}</small></p>

			<label class="input-label"><small>Type</small></label>
			<el-input type="text" placeholder="Enter What Kind Of Pet Here... (Ex. Cat, Dog, Mouse, Etc.)" size="small" v-model="form.type"></el-input>
			<p class="text-danger"><small>{{ api_validators.type }}</small></p>

			<label class="input-label"><small>Birth Year</small></label>
			<el-input type="number" placeholder="Enter The Pet's Birth Year Here..." size="small" v-model="form.birth_year"></el-input>
			<p class="text-danger"><small>{{ api_validators.birth_year }}</small></p>

			<label class="input-label"><small>Description (Optional)</small></label>
			<el-input type="textarea" placeholder="Enter A Short Description About Your Pet Here..." size="small" v-model="form.description" :rows="3"></el-input>
			<p class="text-danger"><small>{{ api_validators.description }}</small></p>

			<div slot="footer" class="dialog-footer text-right mt-5">
		    	<el-button size="small" native-type="button" type="danger" v-on:click="$emit('close', { data: null })">Cancel</el-button>
		    	<el-button size="small" native-type="submit" type="success">Submit</el-button>
		  	</div>
		</form>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['action', 'patient', 'pet'],
		data(){
			return {
				form: {
					id: '',
					name: '',
					type: '',
					birth_year: '',
					description: ''
				},

				api_validators: {
					id: '',
					name: '',
					type: '',
					birth_year: '',
					description: ''
				}
			}
		},

		created(){
			console.log("PET FORM ACTION: ", this.action);
			if(this.action == 'update'){
				console.log("SELECTED PET: ", this.pet);
				for(let key in this.pet.information){
					this.form[key] = this.pet.information[key];
				}
				this.form['id'] = this.pet.id;
			}
		},

		methods: {
			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Saving pet information, Please Wait...' });
				console.log("ACTION IS: ", this.action);
				console.log("PATIENT: ", this.patient);
				this.clearApiValidators();

				let uri = '/api/create-pet';
				if(this.action == 'update'){
					uri = '/api/update-pet';
				}

				this.form['action'] = this.action;
				this.form['patient_id'] = this.patient.id;
				this.$axios.post(uri, this.form).then((response) => {
					let data_to_send = { data: response.data.pet };
					if(this.action == 'update'){
						data_to_send['patient'] = response.data.patient;
					}
					this.$emit('close', data_to_send)
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Pet has been '+ this.action +'d successfully.',
			          	showClose: true,
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
			},

			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>