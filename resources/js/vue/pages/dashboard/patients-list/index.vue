<template>
	<div id="patients-list-page-component">
		<div class="row">
			<div class="col-12">
				<div class=" text-right">
					<el-button type="primary" size="small" v-on:click="open_patient_form = true; patient_form_title = 'Add New Pet Owner'; action = 'create'">
						<i class="el-icon-circle-plus-outline"></i> Add New Owner
					</el-button>
				</div>
				<patient-form :show_dialog="open_patient_form" :form_title="patient_form_title" v-on:after_close="handlePatientFormOnClose($event)" :action="action" :passed_data="selected_record"></patient-form>

				<appointment-form 
					v-on:after_close="handleAppointmentFormOnClose($event)"
					v-on:receive_pet="handleNewPet($event)" 
					:patient_data="selected_record"
					:show_dialog="show_appointment_form_modal"
					:action="'create'"
					:doctors="doctors"
				></appointment-form>
			</div>
			<div class="col-12">
				<hr>
				<el-table
				:data="patients.filter(
					data => !table_search || 
					(
						data.first_name.toLowerCase().includes(table_search.toLowerCase()) || 
						data.last_name.toLowerCase().includes(table_search.toLowerCase()) ||
						data.email_address.toLowerCase().includes(table_search.toLowerCase())
					)
				)"
				style="width: 100%">
					<el-table-column
						label="ID #"
						prop="id">
					</el-table-column>
					<el-table-column
						label="First Name"
						prop="first_name">
					</el-table-column>
					<el-table-column
						label="Last Name"
						prop="last_name">
					</el-table-column>
					<el-table-column
						label="Email Address"
						prop="email_address">
					</el-table-column>
					<el-table-column
					align="right">
						<template slot="header" slot-scope="scope">
							<el-input
								v-model="table_search"
								size="mini"
								placeholder="Search Keyword Here..."
							/>
						</template>
						<template slot-scope="scope">
							<el-dropdown @command="handleUpdateRecord($event, scope.$index, scope.row)">
								<el-button size="mini">
									Actions <i class="el-icon-arrow-down"></i>
								</el-button>
								<el-dropdown-menu slot="dropdown">
									<el-dropdown-item icon="el-icon-notebook-1" command="set appointment">
										Set Appointment
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-circle-close" command="remove">
										Remove
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-edit" command="update">
										Update
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-folder" command="view record">
										View Record
									</el-dropdown-item>
								</el-dropdown-menu>
							</el-dropdown>
						</template>
					</el-table-column>
				</el-table>


				<el-dialog title="Record Removal Confirmation" :visible.sync="show_removal_confirmation" width="30%" :show-close="false" :close-on-click-modal="false" :close-on-press-escape="false">
						<span>
							Are you sure you want to remove your client <span class="font-weight-bold">{{ selected_record.first_name }} {{ selected_record.last_name }}</span> from your patient records?
						</span>
						<span slot="footer" class="dialog-footer">
							<el-button size="small" v-on:click="show_removal_confirmation = false">No</el-button>
							<el-button size="small" type="danger" v-on:click="handleRecordRemoval()">Yes</el-button>
						</span>
				</el-dialog>

				<el-dialog 
					title="Record Information" 
					:visible.sync="show_record_modal" 
					width="70%" 
					:show-close="false" 
					:close-on-click-modal="false" 
					:close-on-press-escape="false"
				>
					<record-info :record="selected_record" v-if="show_record_modal" v-on:updatedrecord="handleUpdatedRecord($event)"></record-info>
					<span slot="footer" class="dialog-footer">
						<el-button size="small" type="danger" v-on:click="show_record_modal = false">Close</el-button>
					</span>
				</el-dialog>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		components: {
			'patient-form': require('./components/patient-form.vue').default,
			'appointment-form': require('../appointments/components/set-appointment-form.vue').default,
			'record-info': require('./components/record-info.vue').default
		},
		data(){
			return {
				open_patient_form: false,
				patient_form_title: '',
				action: '',

				doctors: [],

				patients: [],
		        table_search: '',
		        selected_record: {},
		      	selected_record_index: null,

		      	show_removal_confirmation: false,
		      	show_appointment_form_modal: false,
		      	show_record_modal: false,
			}
		},

		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Retrieving Patients List, Please Wait...' });
			this.$axios.get('/api/get-patients', {}).then((response) => {
				let raw_patient_list = response.data.patients;
				for(let counter = 0; counter < raw_patient_list.length; counter++){
					let new_patient = this.setPatientToList(raw_patient_list[counter]);
					this.patients.push(new_patient);
				}

				let raw_doctors_list = response.data.doctors;
				for(let counter = 0; counter < raw_doctors_list.length; counter++){
					let doctor_object = {
						label: raw_doctors_list[counter]['profile']['first_name'] + ' ' + raw_doctors_list[counter]['profile']['last_name'],
						value: raw_doctors_list[counter]['id']
					};

					this.doctors.push(doctor_object);
				}

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},

		methods: {
			setPatientToList(patient_object_parameter){
				let patient_object = {
					'id': patient_object_parameter['id'],
					'first_name': patient_object_parameter['information']['first_name'],
					'last_name': patient_object_parameter['information']['last_name'],
					'email_address': patient_object_parameter['email'],
					'raw_info': patient_object_parameter,
				};

				return patient_object;
			},

			handlePatientFormOnClose(params){
				if(params.action == 'create' && params.current_record){
					let new_patient = this.setPatientToList(params.current_record);
					this.patients.push(new_patient);
				}
				if(params.action == 'update' && params.current_record){
					for(let counter = 0; counter < this.patients.length; counter++){
						if(this.patients[counter]['id'] == params.current_record.id){
							let patient_object_parameter = params.current_record;
							let patient_object = {
								'id': patient_object_parameter['id'],
								'first_name': patient_object_parameter['information']['first_name'],
								'last_name': patient_object_parameter['information']['last_name'],
								'email_address': patient_object_parameter['email'],
								'raw_info': patient_object_parameter,
							};
							this.patients[counter] = patient_object;
							break;
						}
					}
				}

				this.open_patient_form = false;
			},

			handleUpdateRecord(action, index, data){
				this.selected_record = data;
				this.selected_record_index = index;
				this.action = action;

				if(action == 'remove'){
					this.show_removal_confirmation = true;
				}

				if(action == 'update'){
					this.open_patient_form = true; 
					this.patient_form_title = 'Update Patient';
				}

				if(action == 'view record'){
					this.show_record_modal = true;
				}

				if(action == 'set appointment'){
					this.show_appointment_form_modal = true;
				}
			},

			handleRecordRemoval(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Removing Patient From Your Records, Please Wait...' });
				this.$axios.delete('/api/delete-patient', { id: this.selected_record.id }).then((response) => {
					this.patients.splice(this.selected_record_index, 1);
					this.show_removal_confirmation = false;
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Patient record successfully removed from your record.',
			          	type: 'success'
			        });
				}).catch((error) => {
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});					
			},

			handleAppointmentFormOnClose(data){
				console.log("DATA AFTER CLOSE ON APPOINTMENT: ", data);

				if(data.status == true){
					for(let counter = 0; counter < this.patients.length; counter++){
						if(this.patients[counter]['id'] == data.patient.id){
							let updated_patient = this.setPatientToList(data.patient);
							this.patients[counter] = updated_patient;
							break;
						}
					}
				}

				this.selected_record = {};
				this.show_appointment_form_modal = false;
			},

			handleNewPet(petData){
				console.log("HANDLE NEW PET: ", petData);

				for(let counter = 0; counter < this.patients.length; counter++){
					if(this.patients[counter]['id'] == this.selected_record.id){
						console.log("SELECTED RECORD: ", this.selected_record);
						console.log("SELECTED PATIENTS: ", this.patients[counter]);

						this.selected_record.raw_info.pets.push(petData);
						break;
					}
				}
			},

			handleUpdatedRecord(data){
				let updated_record = this.setPatientToList(data);
				for(let counter = 0; counter < this.patients.length; counter++){
					if(this.patients[counter]['id'] == updated_record['id']){
						this.patients[counter] = updated_record;
						break;
					}
				}
			}
		}
	}
</script>