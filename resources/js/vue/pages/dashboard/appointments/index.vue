<template>
	<div id="appointments-page-component">
		<div class="row m-0">
			<div class="col-12">
				<div class="mb-3 row m-0">
					<div class="col-12">
						<label class="mb-0"><strong>TABLE ROW'S COLOR LEGEND:</strong></label>
						<hr>

						<div class="d-block">
							<div class="color-legend" style="background: #EE9AA3;"></div> - Expired
						</div>
						<div class="d-block">
							<div class="color-legend" style="background: #dca9354a;"></div> - 2 Days Near To Schedule
						</div>
						<div class="d-block">
							<div class="color-legend" style="background: #35dc424a;"></div> - Finished
						</div>
						<div class="d-block">
							<div class="color-legend" style="background: #423c2f4a;"></div> - Cancelled
						</div>

						<hr>
						<label>Want to send SMS reminder to all the schedules who are already near to their schedule? Click the button below.</label>
						<el-button type="info" size="small">
							Send SMS Reminder
						</el-button>
					</div>
				</div>
				
				<!-- FILTERS SECTION -->
				<!-- <div class="mb-3 row m-0">
					<div class="col-lg-12">
						<hr>
						<label class="mb-0"><strong>FILTERS</strong></label>
						<hr>
					</div>
					<div class="col-lg-3">
						<label class="input-label"><small>Doctor</small></label>
					</div>
					<div class="col-lg-3">
						
					</div>
					<div class="col-lg-3">
						
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div> -->


				<div>
					<div>
						<el-table
							:row-class-name="tableRowClassName"
							:data="appointments.filter(
								data => !table_search || 
								(
									data.patient.toLowerCase().includes(table_search.toLowerCase()) || 
									data.status.toLowerCase().includes(table_search.toLowerCase())
								)
							)"
							style="width: 100%"
						>
							<el-table-column
								label="Appointment Date"
								prop="appointment_date"
								fixed="left"
								width="250"
							>
								<template slot-scope="scope">
							        <span>{{ scope.row.appointment_date | moment('MMMM Do YYYY, h:mm A') }}</span>
							    </template>
							</el-table-column>
							<el-table-column
								label="Patient"
								prop="patient"
							>
							</el-table-column>
							<el-table-column
								label="# of Pets"
								prop="number_of_pets"
							>
							</el-table-column>
							<el-table-column
								label="Doctor"
								prop="doctor"
							>
							</el-table-column>
							<el-table-column
								label="Status"
								prop="status"
							>
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
											<el-dropdown-item icon="el-icon-notebook-1" command="reschedule" v-if="scope.row.raw_info.status != 'expired' && scope.row.raw_info.status != 'cancelled'">
												Reschedule
											</el-dropdown-item>
											<el-dropdown-item icon="el-icon-circle-close" command="cancel" v-if="scope.row.raw_info.status != 'expired' && scope.row.raw_info.status != 'cancelled'">
												Cancel
											</el-dropdown-item>
											<el-dropdown-item icon="el-icon-folder" command="view record">
												View Record
											</el-dropdown-item>
										</el-dropdown-menu>
									</el-dropdown>
								</template>
							</el-table-column>
						</el-table>
					</div>
				</div>
			</div>
		</div>

		<el-dialog 
			:title="dialog_title" 
			:visible.sync="show_record_dialog" 
			width="40%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>	
			<div v-if="record_action == 'cancel'">
				<label>Are you sure you want to cancel this appointment?</label>
				<span slot="footer" class="dialog-footer text-right d-block">
					<hr>
					<el-button size="small" type="danger" v-on:click="show_record_dialog = false">No</el-button>
					<el-button size="small" type="success" v-on:click="handleRecordRequest()">Yes</el-button>
				</span>
			</div>
			<div v-if="record_action == 'reschedule'">
				<label>
					<strong class="d-block">Current Appointment Schedule</strong>
					{{ selected_record.appointment_date | moment('MMMM Do YYYY, h:mm A') }}
				</label>
				<label class="input-label"><small>New Appointment Date & Time</small></label>
				<el-date-picker v-model="appointment_date_time" type="datetime" placeholder="Select Date & Time Here..." size="small"> </el-date-picker>
				<p class="text-danger"><small>{{ api_validators.appointment_date_time }}</small></p>

				<span slot="footer" class="dialog-footer text-right d-block">
					<hr>
					<el-button size="small" type="danger" v-on:click="show_record_dialog = false">Cancel</el-button>
					<el-button size="small" type="success" v-on:click="handleRecordRequest()">Reschedule</el-button>
				</span>
			</div>
		</el-dialog>
	</div>
</template>
<style type="text/css" scoped="">
	.color-legend {
		display: inline-block;
		width: 17px;
		height: 17px;
		border-radius: 100%;
		float: left;
		margin-top: 3px;
		margin-right: 5px;
	}
</style>
<script type="text/javascript">
	export default {
		data(){
			return {
				appointments: [],
				table_search: '',
				selected_record: {},
				record_filter: {

				},

				appointment_date_time: '',
				record_action: '',
				show_record_dialog: false,
				dialog_title: '',

				api_validators: {
					appointment_date_time: ''
				}
			}
		},
		created(){
			this.getAppointments();
		},
		methods: {
			getAppointments(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Retrieving Appointments, Please Wait...' });
				this.$axios.get('/api/get-appointments', {}).then((response) => {
					for(let counter = 0; counter < response.data.appointments.length; counter++){
						this.appointments.push(this.setAppointmentObject(response.data.appointments[counter]));
					}

					console.log("APPOINTMENTS: ", this.appointments);

					this.$store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					console.log(error);
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},


			setAppointmentObject(appointment_info){
				let patient_object = {
					id: appointment_info['id'],
					patient: appointment_info['patient']['information']['first_name'] + ' ' + appointment_info['patient']['information']['last_name'],
					appointment_date: appointment_info['information']['appointment_date_time'],
					number_of_pets: appointment_info['pet_appointments'].length,
					doctor: appointment_info['doctor']['profile']['first_name'] + ' ' + appointment_info['doctor']['profile']['last_name'],
					status: appointment_info['status'].charAt(0).toUpperCase() + appointment_info['status'].slice(1),
					raw_info: appointment_info
				};

				return patient_object;
			},

			handleUpdateRecord(action, index, data){
				this.clearApiValidators();

				this.record_action = action;
				if(action == 'reschedule'){
					this.dialog_title = 'Reschedule Appointment';
				}
				if(action == 'cancel'){
					this.dialog_title = 'Cancel Appointment';
				}
				if(action == 'view record'){
					this.dialog_title = 'Appointment Information';
				}

				this.show_record_dialog = true;
				this.selected_record = data;
			},

			async handleRecordRequest(){
				let uri = null;
				let data_to_send = {};
				if(this.record_action == 'cancel'){
					this.$store.dispatch('pageLoader', { display: true, message: 'Cancelling Appointment, Please Wait...' });
					uri = '/api/cancel-appointment';
					data_to_send = { record_id: this.selected_record.id };
				}
				if(this.record_action == 'reschedule'){
					this.$store.dispatch('pageLoader', { display: true, message: 'Rescheduling Appointment, Please Wait...' });

					uri = '/api/reschedule-appointment';
					data_to_send = {
						action: 'update',
						record_id: this.selected_record.id,
						appointment_date_time: this.$elementHelper.formatDate(this.appointment_date_time)
					};
				}

				this.clearApiValidators();
				console.log("URI: ", uri);
				if(uri != null){
					this.$axios.post(uri, data_to_send).then((response) => {
						this.$store.dispatch('pageLoader', { display: false, message: '' });
						for(let counter = 0; counter < this.appointments.length; counter++){
							if(this.appointments[counter]['id'] == this.selected_record.id){
								let success_message = 'Message N/A';
								if(this.record_action == 'cancel'){
									this.appointments[counter]['status'] = 'Cancelled';
									this.appointments[counter]['raw_info']['status'] = 'cancelled';
									success_message = 'Appointment successfully cancelled';
								}
								if(this.record_action == 'reschedule'){
									let updated_appointment = this.setAppointmentObject(response.data.appointment_info);
									for(let counter = 0; counter < this.appointments.length; counter++){
										if(this.appointments[counter]['id'] == this.selected_record.id){
											this.appointments[counter] = updated_appointment;
											break;
										}
									}

									success_message = 'Appointment successfully rescheduled.';
								}
								
								this.$message({
						          	message: success_message,
						          	type: 'success'
						        });

								this.show_record_dialog = false;
								break;
							}
						}
					}).catch((error) => {
						console.log("ERROR IS: ", error);

						if(error.response){
							if(this.record_action == 'cancel'){
								if(error.response.status == 404){
									this.$message({
							          	message: error.response.data.error,
							          	type: 'error'
							        });
								}
							}
							if(this.record_action == 'reschedule'){
								if(error.response.status == 422){
									for(let key in error.response.data.errors){
										console.log("KEY: ", key);
										console.log("ERROR: ", error.response.data.errors[key][0]);
										this.api_validators[key] = error.response.data.errors[key][0];
									}
								}
							}
						}

						this.$store.dispatch('pageLoader', { display: false, message: '' });
					});
				}
			},

			tableRowClassName({row, rowIndex}){
				let near_to_expire = false;
				if(!near_to_expire){
					if(row.raw_info.status == 'expired'){
						return 'nullify-el-hover bg-opacity-danger';
					}
					if(row.raw_info.status == 'finished'){
						return 'nullify-el-hover bg-opacity-success';
					}
					if(row.raw_info.status == 'cancelled'){
						return 'nullify-el-hover bg-opacity-dark';
					}
				}

				return '';
			},

			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>