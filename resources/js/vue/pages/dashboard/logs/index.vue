<template>
	<div id="logs-page-component">
		<div class="row m-0 mt-4">
			<div class="col-12">
				<ul>
					<li v-for="(log, log_index) in logs">
						<span class="d-block mb-0" v-html="log.information.new_message" style="font-size: 13px !important;"></span>
						<small>
							{{ log.created_at | moment('MMMM Do YYYY') }}
							<a :href="'#collapse-log-'+ log_index" data-toggle="collapse" role="button" aria-expanded="false" :aria-controls="'collapse-log-'+ log_index"
							v-if="
								log.information.action == 'update_patient' ||
								log.information.action == 'update_product'
							"
							>
								<small>View Changes</small>
							</a>

							<a :href="'#collapse-log-'+ log_index" data-toggle="collapse" role="button" aria-expanded="false" :aria-controls="'collapse-log-'+ log_index"
							v-if="
								log.information.action == 'product_restock' ||
								log.information.action == 'create_purchase'
							"
							>
								<small>View Record</small>
							</a>
						</small>

						<div class="changes-wrapper">
							<div class="collapse" :id="'collapse-log-'+ log_index">
								<div class="py-2">
									<update-patient-changes :data="log.information" v-if="log.information.action == 'update_patient'"></update-patient-changes>

									<update-product-changes :data="log.information" v-if="log.information.action == 'update_product'"></update-product-changes>

									<product-restock :data="log.information" v-if="log.information.action == 'product_restock'"></product-restock>

									<create-purchase :data="log.information" v-if="log.information.action == 'create_purchase'"></create-purchase>
								</div>
							</div>
						</div>

							

					</li>
				</ul>
			</div>
		</div>
	</div>
</template>
<style type="text/css">
	.changes-wrapper {
		padding-left: 30px;
		border-left: 2px dashed gray;
		margin-left: 0px;
		margin-top: 10px;
	}
</style>
<script type="text/javascript">
	export default {
		data(){
			return {
				logs: []
			}
		},
		components: {
			'update-patient-changes': require('./components/update-patient.vue').default,
			'update-product-changes': require('./components/update-product.vue').default,
			'product-restock': require('./components/product-restock.vue').default,
			'create-purchase': require('./components/create-purchase.vue').default,
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Getting Medical Records. Please Wait...' });
			this.$axios.get('/api/get-log-records', {}).then((response) => {
				this.logs = response.data.logs;
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				console.log("ERROR: ", error);
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		}
	}
</script>