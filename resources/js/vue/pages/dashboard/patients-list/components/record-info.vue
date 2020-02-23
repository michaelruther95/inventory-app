<template>
	<div id="pet-owner-record-info-component">
		<el-tabs v-model="current_tab">
			<!-- -------------------------------------------------------------------------------- -->
			<!-- APPOINTMENT RECORDS TAB -->
			<!-- -------------------------------------------------------------------------------- -->
			<el-tab-pane label="Appointment Records" name="first">
				<div class="row m-0">
					<div class="col-lg-12 px-0">
						<el-collapse v-model="active_appointment_record">
							<div v-if="init_record.raw_info.appointments.length > 0">
								<el-collapse-item 
									v-for="(appointment, appointment_index) in init_record.raw_info.appointments" 
									:title="appointment.information.appointment_date_time | moment('MMMM Do YYYY, h:mm a')" 
									:name="appointment_index"
									:key="appointment_index + '-collapsable-item'"
								>
									<div class="ml-4 pl-4 py-3" style="border-left: 2px dashed #c8c8c8;">
										<div class="bg-light card card-body mb-3">
											<span class="d-block"><strong>Doctor Incharge:</strong></span>
											<span>
												{{ appointment.doctor.profile.first_name }} {{ appointment.doctor.profile.last_name }} | 
												{{ appointment.doctor.email }}
											</span>
										</div>

										<div v-for="(pet_appointment, pet_appointment_index) in appointment.pet_appointments" class="bg-light card card-body mb-3">
											<label class="mb-0">{{ pet_appointment.pet_info.information.name }} | <span style="font-style: italic;">{{ pet_appointment.pet_info.information.type }}</span>
											</label>
											<hr>
											<div class="mb-2">
												<span class="d-block"><strong>Reason of Appointment:</strong></span>
												<span>{{ pet_appointment.information.reason }}</span>
												<hr>
											</div>
											<div>
												<span class="d-block"><strong>Doctor's Findings:</strong></span>
												<span>
													<span v-if="pet_appointment.information.findings">{{ pet_appointment.information.findings }}</span>
													<span v-else>No Findings Record Available...</span>
												</span>
												<hr>
											</div>
										</div>

									</div>

										
								</el-collapse-item>
							</div>
							<div v-else class="py-3">
								<h5 class="m-0">No Appointment Records Found For This Pet Owner...</h5>
							</div>
						</el-collapse>
					</div>
				</div>
			</el-tab-pane>



			<!-- -------------------------------------------------------------------------------- -->
			<!-- PETS RECORDS TAB -->
			<!-- -------------------------------------------------------------------------------- -->
			<el-tab-pane label="Pet Records" name="second">
				<div class="row m-0">
					<div class="col-lg-12 px-0">
						<el-collapse v-model="active_appointment_record">
							<div v-if="init_record.raw_info.pets.length > 0">
								<el-collapse-item  
									v-for="(pet, pet_index) in init_record.raw_info.pets"
									:title="pet.information.name" 
									:name="pet.information.name"
									:key="pet.id + '-collapsable-item'"
								>
									<div class="ml-4 pl-4 py-3" style="border-left: 2px dashed #c8c8c8;">
										<label class="mb-0"><strong>Species:</strong></label>
										<span>{{ pet.information.type }}</span>
										<hr>
										<label class="mb-0"><strong>Birth Year:</strong></label>
										<span>{{ pet.information.birth_year }}</span>
										<hr>
										<label class="mb-0 d-block"><strong>Description:</strong></label>
										<span v-if="pet.information.description">{{ pet.information.description }}</span>
										<span v-else>No Given Description About This Pet...</span>
										<hr>
										<div class="mb-3">
											<el-button type="primary" size="small" v-on:click="setPetForm('update', pet)">
												<i class="el-icon-edit"></i> Update Pet
											</el-button>
										</div>

										<div v-if="pet.pet_appointments.length > 0">
											<div class="bg-light card card-body mb-3" v-for="(pet_appointment, pet_appointment_index) in pet.pet_appointments">
												<div>
													<h5 class="d-block"><strong>Appointment Schedule: </strong></h5>
													<span>
														{{ pet_appointment.appointment_info.information.appointment_date_time | moment('dddd, MMMM Do YYYY, h:mm:ss a') }}
													</span>
													<hr>

													<span class="d-block"><strong>Doctor's Findings:</strong></span>
													<span>
														<span v-if="pet_appointment.information.findings">{{ pet_appointment.information.findings }}</span>
														<span v-else>No Findings Record Available...</span>
													</span>
													<hr>

													<span class="d-block"><strong>Doctor Incharge:</strong></span>
													<span>
														{{ pet_appointment.appointment_info.doctor.profile.first_name }} 
														{{ pet_appointment.appointment_info.doctor.profile.last_name }} | 
														{{ pet_appointment.appointment_info.doctor.email }}
													</span>
												</div>
											</div>
										</div>
										<div v-else>
											<h5 class="m-0">No Pet Record Found...</h5>
										</div>
									</div>
								</el-collapse-item>
							</div>
							<div v-else class="py-3">
								<h5 class="m-0">No Pets Found For This Pet Owner...</h5>
							</div>
						</el-collapse>
					</div>
				</div>
			</el-tab-pane>
		</el-tabs>

		<el-dialog 
			:title="dialog_title" 
			:visible.sync="display_pet_form" 
			width="50%" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false" 
			:show-close="false"
			:append-to-body="true"
		>
			<pet-form 
				v-if="display_pet_form" 
				v-on:close="handlePetFormOnClose($event)" 
				:action="pet_form_action" 
				:patient="init_record"
				:pet="selected_pet"
			></pet-form>
		</el-dialog>
		
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['record'],
		watch: {
			'record': function(val){
				console.log("RECORD INFO SELECTED: ", val);
			},
			'current_tab': function(val){
				console.log("CURRENT TAB CHANGED: ", val);
			}
		},

		components: {
			'pet-form': require('../../appointments/components/pet-form.vue').default
		},

		data(){
			return {
				init_record: {},
				current_tab: 'first',
				active_appointment_record: '',
				display_pet_form: false,
				pet_form_action: '',
				dialog_title: '',
				selected_pet: {}
			}
		},

		created(){
			this.init_record = this.record;
			console.log("RECORD INFO COMPONENT CREATED: ", this.init_record);
		},

		methods: {
			setPetForm(action, pet){
				this.dialog_title = action.charAt(0).toUpperCase() + action.slice(1) + ' Pet';
				this.pet_form_action = action;
				this.selected_pet = pet;
				this.display_pet_form = true;
			},

			handlePetFormOnClose(param){
				console.log("PET FORM CLOSE: ", param);
				if(param.data != null){
					if(param.patient){
						this.$emit('updatedrecord', param.patient);
					}
				}
				this.display_pet_form = false;
			}
		}
	}
</script>