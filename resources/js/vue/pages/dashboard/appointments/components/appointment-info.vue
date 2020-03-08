<template>
	<div id="appointment-info-component">
		<div v-if="!editable_record" class="text-center py-5">
			<h4>No Record Selected</h4>
		</div>
		<div v-else class="row m-0">
			<div class="col-12">
				<hr>
				<div class="row m-0">
					<div class="col-4">
						<span class="d-block text-dark">{{ editable_record.appointment_date | moment('MMMM Do YYYY, h:mm A') }}</span>
						<small class="text-secondary">Appointment Date</small>
					</div>
					<div class="col-4">
						<span class="d-block text-dark">{{ editable_record.patient }}</span>
						<small class="text-secondary">Patient</small>
					</div>
					<div class="col-4">
						<span class="d-block text-dark">{{ editable_record.status }}</span>
						<small class="text-secondary">Appointment Status</small>
					</div>
				</div>
				<hr>
			</div>

			<div class="col-12 pr-0">
				<div class="alert alert-danger" v-if="appointment_finish_error">
					{{ appointment_finish_error }}
				</div>
			</div>

			<div class="col-7" v-if="editable_record.raw_info.status != 'finished'">
				<el-input placeholder="Please input" v-model="editable_record.raw_info.doctor.consultation_fee.fee" size="small">
					<template slot="prepend">CONSULTATION FEE:</template>
				</el-input>
				<p class="text-danger mt-1 mb-0">{{ consultation_fee_error }}</p>
			</div>
			<div class="col-5 text-right" v-if="editable_record.raw_info.status != 'finished'">
				<el-button size="small" type="primary" v-on:click="finishAppointment()">
					Change Appointment Status To Finished
				</el-button>
			</div>

			<div class="col-12" v-if="editable_record.raw_info.status == 'finished'">
				CONSULTATION FEE: {{ editable_record.raw_info.information.fees.consultation_fee | currency('₱') }}
			</div>

			<div class="col-12"><hr></div>

			<div class="col-4">
				<el-select v-model="selected_pet" size="small" filterable placeholder="Select Pet Here..." v-on:change="new_disease = ''">
					<el-option
						v-for="(pet, pet_index) in editable_record.raw_info.pet_appointments"
						:key="'pet-dropdown-'+pet_index"
						:label="pet.pet_info.information.name"
						:value="pet.pet_info.id"
					>
					</el-option>
				</el-select>
			</div>
			<div class="col-8"></div>

			<div class="col-12">
				<div class="alert alert-danger mt-3" v-if="has_api_validator_errors">
					Some findings or prescription fields are not filled up. Please review pets' findings and prescription fields.
				</div>
				<hr>

				<div class="text-center py-5" v-if="!selected_pet">
					<h5>Please Select A Pet At The Dropdown To View Pet's Appointment.</h5>
				</div>
				<div v-else>
					<div 
						v-for="(pet_appointment, pet_index) in editable_record.raw_info.pet_appointments"
						:key="'appointment-record-'+pet_index+'-'+change_counter"
					>
						<div class="shadow-sm card card-body px-0" style="overflow-y: auto !important;" v-if="pet_appointment.pet_info.id == selected_pet">
							<div class="row m-0">
								<div class="col-12">
									<h5>
										{{ pet_appointment.pet_info.information.name }}
									</h5>
									<h6>
										<strong>Birth Year: </strong>{{ pet_appointment.pet_info.information.birth_year }} | 
										<strong>Species</strong>: {{ pet_appointment.pet_info.information.type }}
									</h6>

								</div>
							</div>

							<div>
								<div class="row m-0">
									<div class="col-12">
										<hr>
									</div>
									<div class="col-6">
										<label class="d-block m-0"><strong>Reason Of Appointment</strong></label>
										<label class="d-block m-0">{{ pet_appointment.information.reason }}</label>
										<hr>
										<el-checkbox v-model="pet_appointment.no_disease_found" :key="'disease-checkbox-'+pet_index+'-'+change_counter" v-on:change="change_counter++;" style="margin-bottom: 0px !important;">
											<strong>No Disease Found During Diagnostics</strong>
										</el-checkbox>
										<hr>

										<div v-if="!pet_appointment.no_disease_found">
											<label class="d-block"><strong>Disease Options</strong></label>
											<el-select v-model="pet_appointment.disease_dropdown" size="small" filterable placeholder="Select Pet' Disease Here..." v-on:change="handleDiseaseSelect($event, pet_index)">
												<el-option
													v-for="(disease, disease_index) in diseases"
													:key="'disease-dropdown-'+disease_index"
													:label="disease.disease_name | capitalize"
													:value="disease.disease_name"
												>
												</el-option>
											</el-select>
											<p class="text-danger"><small></small></p>


											<label class="d-block"><strong>New Disease To Add</strong></label>
											<el-input size="small" v-model="new_disease" placeholder="Enter A New Disease Here...">
												<template slot="append">
													<el-button 
														type="secondary" 
														size="small" 
														v-on:click="handleDiseaseSelect(new_disease, pet_index)"
													>
														Add To List
													</el-button>
												</template>
											</el-input>
											<p class="text-danger"><small></small></p>

											<div class="alert alert-danger" v-if="pet_appointment.disease_list_validator">
												{{ pet_appointment.disease_list_validator }}
											</div>

											<div v-if="pet_appointment.disease_list.length > 0">
												<hr>
												<label class="d-block"><strong>Disease List:</strong></label>
												<ul class="mb-0" style="list-style: none;">
													<li v-for="(disease, disease_list_index) in pet_appointment.disease_list" style="margin-bottom: 5px;">
														<a 
															href="javascript:void(0)" 
															class="mr-2 text-danger"
															v-on:click="pet_appointment.disease_list.splice(disease_list_index, 1); change_counter++;"
														>
															<i class="el-icon-close"></i>
														</a> {{ disease | capitalize }}
													</li>
												</ul>
												<hr>
											</div>
										</div>
											
											


										<label class="d-block"><strong>Doctor's Findings Description</strong></label>
										<trumbowyg v-model="pet_appointment.information.findings"></trumbowyg>
										<p class="text-danger mt-1">{{ pet_appointment.findings_validator }}</p>

										<label class="d-block"><strong>Prescription</strong></label>
										<trumbowyg v-model="pet_appointment.information.prescription"></trumbowyg>
										<p class="text-danger mt-1">{{ pet_appointment.prescription_validator }}</p>
									</div>
									<div class="col-6">
										<label class="d-block mb-3"><strong>Appointments History</strong></label>

										<div class="py-3" v-if="pet_appointment.pet_info.pet_appointments.length <= 1">
											<h5>Pet has no previous appointments...</h5>
										</div>
										<div v-else>
											<div v-for="(prev_appointment, prev_index) in pet_appointment.pet_info.pet_appointments">
												<div class="shadow-sm card card-body mb-3" v-if="pet_appointment.id != prev_appointment.id">
													<a 
														class="d-block m-0 text-dark" 
														data-toggle="collapse" 
														:href="'#prev-appointment-info-collapse-'+prev_index" 
														role="button" 
														aria-expanded="false" 
														:aria-controls="'prev-appointment-info-collapse-'+prev_index"
														style="text-decoration: none;" 
													>
														<label>
															<strong>
																{{ prev_appointment.appointment_info.information.appointment_date_time | moment('MMMM Do YYYY, h:mm A') }} - 
																{{ prev_appointment.appointment_info.status | capitalize }}
															</strong>
														</label>
													</a>

													<div class="collapse" :id="'prev-appointment-info-collapse-'+prev_index">
														<hr>
														<label class="d-block m-0"><strong>Reason Of Appointment</strong></label>
														<label class="d-block m-0">{{ prev_appointment.information.reason }}</label>
														<hr>

														<label class="d-block m-0"><strong>Diseases Found</strong></label>
														<div v-if="prev_appointment.disease_findings.length > 0">
															<ul>
																<li v-for="disease in prev_appointment.disease_findings">
																	{{ disease.disease.disease_name | capitalize }}
																</li>
															</ul>
														</div>
														<div v-else>
															<label class="d-block m-0">No Diseases Found...</label>	
														</div>
														<hr>

														<label class="d-block"><strong>Doctor's Findings</strong></label>
														<div 
															v-if="prev_appointment.information.findings" 
															v-html="prev_appointment.information.findings"
														></div>

														<div v-else>
															<label class="d-block m-0">No Doctor Findings Found...</label>	
														</div>
														<hr>

														<label class="d-block"><strong>Prescription</strong></label>
														<div 
															v-if="prev_appointment.information.prescription"
															v-html="prev_appointment.information.prescription"
														></div>
														<div v-else>
															<label class="d-block m-0">No Prescriptions Found...</label>	
														</div>
													</div>
												</div>
											</div>
										</div>					
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['record', 'submitting', 'diseases'],
		watch: {
			'submitting': function(val){

				if(val){
					this.$store.dispatch('pageLoader', { display: true, message: 'Submitting Findings. Please Wait...' });
					this.clearApiValidators();

					let pet_appointment_array = [];
					for(let counter = 0; counter < this.editable_record.raw_info.pet_appointments.length; counter++){
						let current_pet_appointment = this.editable_record.raw_info.pet_appointments[counter];

						let new_pet_appointment_object = {
							'appointment_id': current_pet_appointment.appointment_id,
							'pet_appointment_id': current_pet_appointment.id,
							'findings': current_pet_appointment.information.findings,
							'prescription': current_pet_appointment.information.prescription,
							'disease_list': current_pet_appointment.disease_list,
							'no_disease_found': current_pet_appointment.no_disease_found
						};

						pet_appointment_array.push(new_pet_appointment_object);
					}

					console.log("DATA TO SEND: ", pet_appointment_array);

					let data_to_send = {
						'pet_appointments': pet_appointment_array,
						'appointment_id': this.editable_record.raw_info.id
					};

					this.$axios.post('/api/submit-doctor-findings', data_to_send).then((response) => {
						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$emit('aftersubmit', { submitting: false, data: response });
						this.$emit('update_disease_list', response.data.new_disease_list);

						this.$message({
				          	message: 'Findings submitted successfully.',
				          	showClose: true,
				          	type: 'success'
				        });
					}).catch((error) => {
						console.log("ERROR: ", error);

						let message = 'Something went wrong. Please refresh the page and try again...';


						if(error.response){
							if(error.response.status == 422){
								this.has_api_validator_errors = true;
								for(let key in error.response.data.errors){
									let splitted_key = key.split('.');
									console.log("SPLITTED KEY: ", splitted_key);
									if(splitted_key[0] == 'pet_appointments'){
										let new_message = error.response.data.errors[key][0].replace('The '+key, 'This');

										this.editable_record.raw_info.pet_appointments[parseInt(splitted_key[1])][splitted_key[2] + '_validator'] = new_message;
									}
									if(splitted_key[0] == 'pet_appointments'){
										this.editable_record.raw_info.pet_appointments[parseInt(splitted_key[1])]['disease_list_validator'] = 'No disease selected. Please put atleast one disease found in your diagnostics.';
										
									}
								}

								message = "Some findings or prescription fields are not filled up. Please review pets' findings and prescription fields.";
							}
						}

						this.$message({
				          	message: message,
				          	showClose: true,
				          	type: 'error'
				        });

						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$emit('aftersubmit', { submitting: false, data: null });
					});
				}
					
			}
		},
		data(){
			return {
				new_disease: '',
				editable_record: null,
				selected_pet: null,
				has_api_validator_errors: false,
				change_counter: 0,

				appointment_finish_error: '',
				consultation_fee_error: ''
			}
		},
		created(){
			this.editable_record = this.record;
			console.log("APPOINTMENT INFO RECORD VALUE: ", this.record);
		},

		methods: {
			clearApiValidators(){
				this.has_api_validator_errors = false;
				// ----------------------------------------------------------------------
				// CLEAR API VALIDATORS IN THE PET APPOINTMENT LIST
				// ----------------------------------------------------------------------
				for(let counter = 0; counter < this.editable_record.raw_info.pet_appointments.length; counter++){
					this.editable_record.raw_info.pet_appointments[counter]['findings_validator'] = '';
					this.editable_record.raw_info.pet_appointments[counter]['prescription_validator'] = '';
					this.editable_record.raw_info.pet_appointments[counter]['disease_list_validator'] = '';
				}
				// ----------------------------------------------------------------------
			},

			handleDiseaseSelect(param, pet_index){
				if(param){
					let value = param.toLowerCase();
					let reformatted_array = this.editable_record.raw_info.pet_appointments.map((obj, index) => {
						if(index == pet_index){
							if(!obj.disease_list.includes(value)){
								obj.disease_list.push(value);
							}
							else{
								this.$message({
						          	message: 'Disease already on the list.',
						          	showClose: true,
						          	type: 'warning'
						        });
							}
						}
						return obj;
					});
				}

				this.new_disease = '';
				this.editable_record.raw_info.pet_appointments[pet_index]['disease_dropdown'] = '';
				this.change_counter++;
			},

			finishAppointment(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Finishing Appointment. Please Wait...' });
				this.appointment_finish_error = '';

				this.$axios.post('/api/finish-appointment', { 
					id: this.editable_record.id,
					consultation_fee: this.editable_record.raw_info.doctor.consultation_fee.fee
				}).then((response) => {
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Appointment successfully finished.',
			          	showClose: true,
			          	type: 'success'
			        });

			        this.$emit('appointmentfinished', response.data.appointment_info);
				}).catch((error) => {
					console.log(error);

					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Something went wrong. Please refresh the page and try again...',
			          	showClose: true,
			          	type: 'error'
			        });

					if(error.response){
						if(error.response.status == 403){
							this.appointment_finish_error = error.response.data.error_message;
							this.consultation_fee_error = error.response.data.consultation_fee_error;
						}
						
					}	
			        
				});
			}
		}
	}
</script>