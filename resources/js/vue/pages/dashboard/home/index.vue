<template>
	<div id="dashboard-home-page-component">
		<el-tabs v-model="active_tab">
			<el-tab-pane label="Disease Analytics" name="disease_analytics">
				<div v-if="record != null">
					<disease-statistics 
						:record="record"
						:colorschemes="color_schemes"
					></disease-statistics>
				</div>
			</el-tab-pane>
			<el-tab-pane label="Inventory Analytics" name="inventory_analytics">
				<div v-if="record != null">
					<inventory-statistics
						:record="record"
						:colorschemes="color_schemes"
					></inventory-statistics>
				</div>
			</el-tab-pane>
		</el-tabs>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				active_tab: 'disease_analytics',
				record: null,
				color_schemes: [
					'#69A197',
					'#BF211E',
					'#FFC857',
					'#02182B',
					'#0197F6',
					'#481D24',
					'#E9724C',
					'#57467B',
					'#524948'
				]
			}
		},
		components: {
			'disease-statistics': require('./components/disease-statistics/main.vue').default,
			'inventory-statistics': require('./components/inventory-statistics/main.vue').default
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Fetching resources. Please wait...' });
			this.$axios.get('/api/get-dashboard-resources', {}).then((response) => {
				this.record = response.data;
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				console.log(error);
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		}
	}
</script>