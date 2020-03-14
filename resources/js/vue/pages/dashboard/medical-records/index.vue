<template>
	<div id="medical-records-page-component">
		<div class="row m-0">
			<div class="col-12">
				<el-table
					:data="pets.filter(
						data => !table_search || 
						(
							data.pet_name.toLowerCase().includes(table_search.toLowerCase()) ||
							data.owner_name.toLowerCase().includes(table_search.toLowerCase())
						)
					)"
					style="width: 100%"
				>
					<el-table-column
						label="Pet Name"
						prop="pet_name"
					>
					</el-table-column>

					<el-table-column
						label="Owner Name"
						prop="owner_name"
					>
					</el-table-column>

					<el-table-column
						label="Species"
						prop="species"
					>
					</el-table-column>

					<el-table-column
						label="Birth Year"
						prop="birth_year"
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
							<div style="width: 100%; text-align: right;">
								<el-button type="primary" size="small" plain v-on:click="show_record_modal = true; selected_record = scope.row;">
									View Records
								</el-button>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>



		<el-dialog
			title="Pet's Medical Record"
			:visible.sync="show_record_modal"
			width="50%"
		>
			<div v-if="selected_record != null">
				<div class="row m-0">
					<div class="col-4">
						<small style="font-size: 13px !important;">
							<label><strong>Pet's Name:</strong> <span>{{ selected_record.raw_info.information.name }}</span></label>
						</small>
					</div>
					<div class="col-4">
						<small style="font-size: 13px !important;">
							<label><strong>Pet's Type:</strong> <span>{{ selected_record.raw_info.information.type }}</span></label>
						</small>
					</div>

					<div class="col-4">
						<small style="font-size: 13px !important;">
							<label><strong>Birth Year:</strong> <span>{{ selected_record.raw_info.information.birth_year }}</span></label>
						</small>
					</div>

					<div class="col-12">
						<hr>
						<small style="font-size: 13px !important;">
							<label class="d-block"><strong>Description: </strong> <span v-if="!selected_record.raw_info.information.description"> N/A</span></label>
							<span v-if="selected_record.raw_info.information.description">
								{{ selected_record.raw_info.information.description }}
							</span>
						</small>
						<hr>
					</div>


					<div class="col-12">
						<label class="d-block"><strong>AVAILABLE RECORDS:</strong></label>
					</div>
				</div>

				<div class="row m-0" v-for="pet_appointment in selected_record.raw_info.pet_appointments">
					<div class="col-12" v-if="pet_appointment.information.findings">
						
						<el-collapse>
							<el-collapse-item 
								:title="pet_appointment.appointment_info.information.appointment_date_time | moment('MMMM Do YYYY') + ' Appointment Findings'" 
								:name="'pet-appointment-'+pet_appointment.id"
							>
								<div style="padding-left: 20px;border-left: 1px dashed gray;margin-left: 10px;padding-top: 10px;padding-bottom: 10px;">
									<label class="d-block" v-if="pet_appointment.information.reason">
										<span class="d-block font-weight-bold">Reason:</span>
										<span>{{ pet_appointment.information.reason }}</span>
										<hr>
									</label>

									<label class="d-block" v-if="pet_appointment.information.findings">
										<span class="d-block font-weight-bold">Finding(s):</span>
										<div v-html="pet_appointment.information.findings"></div>
									</label>

									<label class="d-block" v-if="pet_appointment.information.prescription">
										<span class="d-block font-weight-bold">Prescription(s):</span>
										<div v-html="pet_appointment.information.prescription"></div>
									</label>

									<div v-if="pet_appointment.disease_findings.length > 0">
										<ul>
											<li v-for="disease_findings in pet_appointment.disease_findings">
												{{ disease_findings.disease.disease_name | capitalize }}
											</li>
										</ul>
									</div>
								</div>
							</el-collapse-item>
						</el-collapse>
					</div>
				</div>
			</div>
			
			<span slot="footer" class="dialog-footer">
				<el-button @click="show_record_modal = false; selected_record = null;" type="danger" size="small">
					Close
				</el-button>
			</span>
		</el-dialog>

	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				pets: [],
				table_search: '',
				show_record_modal: false,
				selected_record: null
			}
		},
		watch: {
			'selected_record': function(val){
				console.log("SELECTED RECORD CHANGED: ", val);
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Getting Medical Records. Please Wait...' });
			this.$axios.get('/api/get-medical-records', {}).then((response) => {
				for(let counter = 0; counter < response.data.pets.length; counter++){
					let pet_object = {
						'pet_name': response.data.pets[counter]['information']['name'],
						'owner_name': response.data.pets[counter]['pet_owner']['information']['first_name'] + ' ' + response.data.pets[counter]['pet_owner']['information']['last_name'],
						'species': response.data.pets[counter]['information']['type'],
						'birth_year': response.data.pets[counter]['information']['birth_year'],
						'raw_info': response.data.pets[counter]
					}

					this.pets.push(pet_object);
				}

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				console.log("ERROR: ", error);
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		}
	}
</script>