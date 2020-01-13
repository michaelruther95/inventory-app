<template>
	<div id="set-appointment-form-component">
		<el-dialog title="Create Appointment" :visible.sync="display_dialog" width="40%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
			<form v-on:submit.prevent="submit()">
				<div class="row m-0">
					<div class="col-12">
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

						<label class="input-label"><small>Appointment Date</small></label>
						<el-date-picker v-model="form.appointment_date" type="date" placeholder="Select Date Here..." size="small"> </el-date-picker>
						<p class="text-danger"><small>{{ api_validators.appointment_date }}</small></p>

						<label class="input-label"><small>Appointment Time</small></label>
						<el-time-picker
							v-model="form.appointment_time"
							:picker-options="{ selectableRange: '00:00:00 - 23:59:59' }"
							size="small"
							placeholder="Select Time Here...."
						>
						</el-time-picker>
						<p class="text-danger"><small>{{ api_validators.appointment_time }}</small></p>

						<label class="input-label"><small>Number of Pets</small></label>
						<el-input type="number" placeholder="# of Pets You'll Bring At This Appointment Here..." size="small" v-model="form.number_of_pets"></el-input>
						<p class="text-danger"><small>{{ api_validators.number_of_pets }}</small></p>
					</div>
				</div>

				<div slot="footer" class="dialog-footer text-right mt-5">
			    	<el-button size="small" native-type="button" type="danger" v-on:click="$emit('after_close', { 'status': false }); display_dialog = false;">Cancel</el-button>
			    	<el-button size="small" native-type="submit" type="success">Submit</el-button>
			  	</div>
			</form>
		</el-dialog>
	</div>	
</template>
<script type="text/javascript">
	export default {
		props: ['action', 'patient_data', 'show_dialog', 'doctors'],
		watch: {
			'show_dialog': function(val){
				this.display_dialog = val;
				console.log("PATIENT DATA: ", this.patient_data);
				if(val){
					for(let key in this.form){
						this.form[key] = '';
					}
					this.clearApiValidators();
					// this.getDoctorsList();
				}
			}
		},
		data(){
			return {
				current_section: 1,
				display_dialog: false,

				form: {
					doctor: '',
					appointment_date: '',
					appointment_time: '',
					number_of_pets: '',
					patient_id: '',
					action: ''
				},

				api_validators: {
					appointment_date: '',
					appointment_time: '',
					number_of_pets: '',
					patient_id: '',
					action: ''
				}
			}
		},

		methods: {
			// getDoctorsList(){
			// 	this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Form Resources, Please Wait...' });
			// 	this.$axios.get('/api/get-users/doctor', {}).then((response) => {
			// 		for(let counter = 0; counter < response.data.users.length; counter++){
			// 			let doctor_object = {
			// 				label: response.data.users[counter]['profile']['first_name'] + ' ' + response.data.users[counter]['profile']['last_name'],
			// 				value: response.data.users[counter]['id']
			// 			};

			// 			this.doctors.push(doctor_object);
			// 		}

			// 		this.$store.dispatch('pageLoader', { display: false, message: '' });
			// 	}).catch((error) => {
			// 		this.$store.dispatch('pageLoader', { display: false, message: '' });
			// 	});
			// },

			submit(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Saving Appointment, Please Wait...' });
				this.clearApiValidators();

				this.form.patient_id = this.patient_data.id;
				this.form.action = this.action;
				this.form.appointment_date = this.$elementHelper.formatDate(this.form.appointment_date);

				this.$axios.post('/api/save-appointment', this.form).then((response) => {
					this.display_dialog = false;
					this.$emit('after_close', { 'status': true });
					this.$store.dispatch('pageLoader', { display: false, message: '' });
					this.$message({
			          	message: 'Appointment successfully saved.',
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
			},
		}
	}
</script>