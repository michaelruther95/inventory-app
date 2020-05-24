<template>
	<div id="disease-statistics-main-component">
		<div class="row m-0">
			<div class="col-4 px-0">
				<el-select v-model="month_filter" size="small" filterable placeholder="Analytics Filter" v-on:change="setDataByFilter()">
					<el-option v-for="(month, month_index) in monthly_data"
						:label="month.label"
						:value="month.month_count"
						:key="'month-filter-'+month_index"
					>
					</el-option>
				</el-select>
			</div>
			<div class="col-3">
				<el-select v-model="year_filter" size="small" filterable placeholder="Analytics Filter" v-on:change="setDataByFilter()">
					<el-option v-for="year in year_option_end"
						:label="year + year_option_start"
						:value="year + year_option_start"
						:key="'year-filter-'+ year"
						v-if="year + year_option_start <= year_option_end"
					>
					</el-option>
				</el-select>
			</div>

			<div class="col-12 px-0">
				<hr>
				<div v-if="data_to_display.length <= 0">
					<h5>No Data To Display...</h5>
				</div>
				<div v-else>
					<div class="mb-4" v-for="(data, data_index) in data_to_display">
						<label class="d-block" style="font-size: 14px;">
							{{ data.name | capitalize }} - {{ data.value }} Case(s) Found
						</label>
						<div class="d-block px-3 py-2 mb-3 text-white rounded" :style="'width: '+ data.percentage + '% !important; background: '+ data.bg_color +' !important; height: 10px; opacity: 0.8;'"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">


	export default {
		props: ['record', 'colorschemes'],
		data(){
			return {
                data_to_display: [],

				month_filter: 1,
				year_filter: null,
				year_option_limit: null,

				year_option_start: null,
				year_option_end: null,

				monthly_data: [
					{ label: 'January', month_count: 1, data: [] },
					{ label: 'February', month_count: 2, data: [] },
					{ label: 'March', month_count: 3, data: [] },
					{ label: 'April', month_count: 4, data: [] },
					{ label: 'May', month_count: 5, data: [] },
					{ label: 'June', month_count: 6, data: [] },
					{ label: 'July', month_count: 7, data: [] },
					{ label: 'August', month_count: 8, data: [] },
					{ label: 'September', month_count: 9, data: [] },
					{ label: 'October', month_count: 10, data: [] },
					{ label: 'November', month_count: 11, data: [] },
					{ label: 'December', month_count: 12, data: [] },
				],

				item_labels: [],
			}
		},	
		created(){
			let current_date = new Date();
			this.year_filter = current_date.getFullYear();
			this.month_filter = current_date.getMonth() + 1;

			this.year_option_start = this.year_filter - 20;
			this.year_option_end = this.year_filter;

			this.setDataByFilter();
		},
		methods: {
			setDataByFilter(){
				this.item_labels = [];

				for(let counter = 0; counter < this.monthly_data.length; counter++){
					this.monthly_data[counter]['data'] = [];
				}

				for(let df_counter = 0; df_counter < this.record.disease_findings.length; df_counter++){
					let splitted_datetime_created = this.record.disease_findings[df_counter]['created_at'].split(' ');
					let splitted_date_created = splitted_datetime_created[0].split('-');
					let month_count = parseInt(splitted_date_created[1]);
					let year_count = parseInt(splitted_date_created[0]);

					if(this.year_filter == year_count){
						let monthly_data_index = month_count - 1;
						let disease_exists = false;
						for(let md_counter = 0; md_counter < this.monthly_data[monthly_data_index]['data'].length; md_counter++){
							if(this.monthly_data[monthly_data_index]['data'][md_counter]['id'] == this.record.disease_findings[df_counter]['disease_id']){
								this.monthly_data[monthly_data_index]['data'][md_counter]['counter'] += 1;
								disease_exists = true;
								break;
							}
						}

						if(!disease_exists){
							let disease_object = {
								id: this.record.disease_findings[df_counter]['disease_id'],
								disease_name: this.record.disease_findings[df_counter]['disease']['disease_name'],
								counter: 1
							};

							this.monthly_data[monthly_data_index]['data'].push(disease_object);
						}
					}
				}

                let data_to_display = [];

				for(let counter = 0; counter < this.monthly_data.length; counter++){
					let current_selected_month_index = this.month_filter - 1;

					if(counter == current_selected_month_index){
						for(let inner_counter = 0; inner_counter < this.monthly_data[counter]['data'].length; inner_counter++){
							this.item_labels.push(this.monthly_data[counter]['data'][inner_counter]['disease_name']);

							let disease_chart_obj = {
								name: this.monthly_data[counter]['data'][inner_counter]['disease_name'],
								value: this.monthly_data[counter]['data'][inner_counter]['counter']
							}

							data_to_display.push(disease_chart_obj);
						}

						break;
					}
				}


				let highest_value = 0;
				let limiter = 0;

				for(let counter = 0; counter < data_to_display.length; counter++){
					if(data_to_display[counter]['value'] > highest_value){
						highest_value = data_to_display[counter]['value'];
					}
				}

				limiter = highest_value + 1;

				for(let counter = 0; counter < data_to_display.length; counter++){
					let pre_percentage =  data_to_display[counter]['value'] / limiter;
					data_to_display[counter]['percentage'] = pre_percentage * 100;
					data_to_display[counter]['bg_color'] = this.colorschemes[Math.floor(Math.random() * this.colorschemes.length)];
				}

				this.data_to_display = data_to_display;
			}
		}
	}
</script>