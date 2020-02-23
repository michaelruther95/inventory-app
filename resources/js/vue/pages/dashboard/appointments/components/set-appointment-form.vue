<template>
	<div id="set-appointment-form-component">
		<el-dialog :title="dialog_title" :visible.sync="display_dialog" width="50%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">

			<pet-form v-if="display_pet_form" v-on:close="handlePetFormOnClose($event)" :action="pet_form_action" :patient="patient_data"></pet-form>

			<form v-on:submit.prevent="submit()" v-else>
				<div class="row m-0">
					<div class="col-3 h-100">
						<div style="height: 300px;">
							<el-steps direction="vertical" :active="current_section">
								<el-step title="Step 1"></el-step>
								<el-step title="Step 2"></el-step>
								<el-step title="Step 3"></el-step>
							</el-steps>
						</div>
					</div>
					<div class="col-9">
						<div v-if="current_section == 1">
							<h5 class="d-block">Step 1 - Select Pets</h5>
							<small>Please select the pets that you wish to be checked by our doctors.</small>
							<hr>
							<el-button type="primary" size="small" native-type="button" v-on:click="setPetForm('create')">
								<i class="el-icon-circle-plus-outline"></i> Add New Pet
							</el-button>
							<hr>

							<div class="row m-0" v-if="patient_data.raw_info">
								<div class="col-lg-12 px-0 pb-3" v-for="(pet, index) in patient_data.raw_info.pets">
									<a href="javascript:void(0)" class="card card-body" v-on:click="handlePetSelect(pet)" style="text-decoration: none; color: unset;" v-bind:class="{
										'alert-success': pre_selected_pets.includes(pet.id)
									}">
										<h6>{{ pet.information.name }}</h6>
										<small>{{ pet.information.type }} | {{ pet.information.birth_year }}</small>
										<hr>
										<small v-if="pet.information.description">{{ pet.information.description }}</small>
										<small v-else>Pet Description Not Available.</small>
									</a>
								</div>
							</div>
						</div>

						<div v-if="current_section == 2">
							<h5>Step 2 - Appointment Reason</h5>
							<small>Enter the reason why these pets need medical attention.</small>
							<hr>

							<div class="row m-0">
								<div class="col-lg-12 px-0 pb-3" v-for="(pet, index) in form.selected_pets">
									<div class="card card-body">
										<h6>{{ pet.information.name }}</h6>
										<small>{{ pet.information.type }} | {{ pet.information.birth_year }}</small>
										<hr>
										<small v-if="pet.information.description">{{ pet.information.description }}</small>
										<small v-else>Pet Description Not Available.</small>
										<hr>
										<label><small>Reason For Checkup</small></label>
										<el-input type="textarea" :rows="3" v-model="pet.reason" size="small"></el-input>
										<p class="text-danger"><small>{{ pet.validation_message }}</small></p>
									</div>
								</div>
							</div>
						</div>

						<div v-if="current_section == 3">
							<h5>Step 3 - Setup the appointment.</h5>
							<small>Please fill up the following fields below in order to set an appointment for the selected pets.</small>
							<hr>
							<label class="input-label"><small>Doctor</small></label>
							<el-select v-model="form.doctor" placeholder="Select" size="small">
								<el-option
									v-for="item in doctors"
									:key="item.value"
									:label="item.label"
									:value="item.value"
								>
								</el-option>
							</el-select>
							<p class="text-danger"><small>{{ api_validators.doctor }}</small></p>

							<label class="input-label"><small>Appointment Date & Time</small></label>
							<el-date-picker v-model="form.appointment_date_time_display" type="datetime" placeholder="Select Date & Time Here..." size="small"> </el-date-picker>
							<p class="text-danger"><small>{{ api_validators.appointment_date_time }}</small></p>
						</div>
							
					</div>
				</div>

				<div slot="footer" class="dialog-footer mt-5">
					<div class="row m-0">
						<div class="col-6">
							<el-button size="small" native-type="button" type="danger" v-on:click="$emit('after_close', { 'status': false }); display_dialog = false;">Cancel</el-button>
						</div>
						<div class="col-6 text-right">
							<el-button size="small" native-type="button" type="info" v-on:click="current_section--;" :disabled="current_section == 1">Previous</el-button>
					    	<el-button size="small" native-type="submit" type="success">
					    		<span v-if="current_section < 3">
					    			Next
					    		</span>
					    		<span v-else>
					    			Submit
					    		</span>
					    	</el-button>
						</div>
					</div>
					    	
			  	</div>
			</form>
		</el-dialog>
	</div>	
</template>
<script type="text/javascript">
	export default {
		props: ['action', 'patient_data', 'show_dialog', 'doctors'],
		components: {
			'pet-form': require('./pet-form.vue').default
		},
		watch: {
			'show_dialog': function(val){
				console.log("SHOW DIALOG: ", val);
				this.display_dialog = val;
				if(val){
					for(let key in this.form){
						if(key != 'selected_pets'){
							this.form[key] = '';
						}
						else {
							this.form[key] = [];
						}
					}
					this.current_section = 1;
					this.pre_selected_pets = [];

					this.clearApiValidators();
				}
			},

			'patient_data': function(val){
				console.log("PATIENT DATA CHANGED: ", val);
			}
		},
		data(){
			return {
				current_section: 1,
				display_dialog: false,
				dialog_title: 'Create Appointment',

				display_pet_form: false,
				pet_form_action: '',
				pre_selected_pets: [],

				form: {
					doctor: '',
					appointment_date_time: '',
					appointment_date_time_display: '',
					appointment_time: '',
					number_of_pets: '',
					patient_id: '',
					action: '',
					selected_pets: []
				},

				api_validators: {
					doctor: '',
					appointment_date_time: '',
					appointment_date_time_display: '',
					appointment_time: '',
					number_of_pets: '',
					patient_id: '',
					action: '',
					selected_pets: ''
				}
			}
		},

		methods: {
			handlePetFormOnClose(data){
				this.display_pet_form = false;
				console.log("HANDLE PET FORM ON CLOSE: ", data);
				if(data.data){
					this.$emit('receive_pet', data.data);

					this.$message({
			          	message: 'Pet information successfully saved.',
			          	type: 'success'
			        });
				}
			},

			setPetForm(action){
				this.dialog_title = action.charAt(0).toUpperCase() + action.slice(1) + ' New Pet';
				this.pet_form_action = action;
				this.display_pet_form = true;
			},

			submit(){
				if(this.current_section < 3){
					if(this.current_section == 1){
						if(this.pre_selected_pets.length <= 0){
							this.$message({
					          	message: 'Please select a pet first before proceeding to the next step.',
					          	type: 'error'
					        });
						}
						else{
							if(this.current_section == 1){
								for(let counter = 0; counter < this.pre_selected_pets.length; counter++){
									for(let inner_counter = 0; inner_counter < this.patient_data.raw_info.pets.length; inner_counter++){
										if(this.patient_data.raw_info.pets[inner_counter]['id'] == this.pre_selected_pets[counter]){
											let new_pet_object = {};
											for(let key in this.patient_data.raw_info.pets[inner_counter]){
												new_pet_object[key] = this.patient_data.raw_info.pets[inner_counter][key];
											}

											new_pet_object['reason'] = '';
											new_pet_object['validation_message'] = '';


											let pet_exists_on_list = false;
											for(let sel_pet_counter = 0; sel_pet_counter < this.form.selected_pets.length; sel_pet_counter++){
												if(this.form.selected_pets[sel_pet_counter]['id'] == new_pet_object['id']){
													pet_exists_on_list = true;
													break;
												}
											}

											if(!pet_exists_on_list){
												this.form.selected_pets.push(new_pet_object);
											}
											break;
										}
									}
								}
							}
							this.current_section++;
						}
					}
					else{
						this.current_section++;
					}
				}
				else{
					this.$store.dispatch('pageLoader', { display: true, message: 'Saving Appointment, Please Wait...' });
					this.clearApiValidators();

					console.log("FORM TO SEND: ", this.form);

					this.form.patient_id = this.patient_data.id;
					this.form.action = this.action;
					this.form.appointment_date_time = this.$elementHelper.formatDate(this.form.appointment_date_time_display);

					console.log("RAW DATE: ", this.form.appointment_date_time);
					console.log("APPOINTMENT DATE TIME: ", this.form.appointment_date_time);

					this.$axios.post('/api/save-appointment', this.form).then((response) => {
						this.display_dialog = false;
						this.$emit('after_close', { 
							'status': true, 
							'appointment_info': response.data.appointment, 
							'patient': response.data.patient 
						});

						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$message({
				          	message: 'Appointment successfully saved.',
				          	type: 'success'
				        });
					}).catch((error) => {
						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									let exploded_key = key.split('.');
									console.log("EXPLODED KEY: ", exploded_key);

									// -----------------------------------------------------------------------
									// SET VALIDATION FOR THE PET LIST
									// -----------------------------------------------------------------------
									if(exploded_key[0] == 'selected_pets'){
										let new_message = error.response.data.errors[key][0].replace(key, exploded_key[exploded_key.length - 1]);
										this.form.selected_pets[parseInt(exploded_key[1])]['validation_message'] = new_message;
									}
									// -----------------------------------------------------------------------


									this.api_validators[key] = error.response.data.errors[key][0];
								}

								this.$message({
						          	message: 'Some of the fields are not properly filled up. Please check your form from Step 1 - 3.',
						          	type: 'error'
						        });
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

				// -----------------------------------------------------------------------
				// CLEAR VALIDATION FOR THE PET LIST
				// -----------------------------------------------------------------------
				for(let counter = 0; counter < this.form.selected_pets.length; counter++){
					this.form.selected_pets[counter]['validation_message'] = '';
				}
				// -----------------------------------------------------------------------
			},

			handlePetSelect(petInfo){
				if(!this.pre_selected_pets.includes(petInfo.id)){
					this.pre_selected_pets.push(petInfo.id);
				}
				else{
					this.pre_selected_pets.splice(this.pre_selected_pets.indexOf(petInfo.id), 1);
					for(let counter = 0; counter < this.form.selected_pets.length; counter++){
						if(this.form.selected_pets[counter]['id'] == petInfo.id){
							this.form.selected_pets.splice(counter, 1);
							break;
						}
					}
				}
			}
		}
	}
</script>