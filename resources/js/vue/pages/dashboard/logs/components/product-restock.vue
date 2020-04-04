<template>
	<div id="update-patient-changes-component">
		<div v-if="parsed_data">
			<table class="table">
				<thead>
					<tr>
						<th style="border-bottom: 0px !important;"><small><strong>Expiry Date</strong></small></th>
						<th style="border-bottom: 0px !important;"><small><strong>Delivery Date</strong></small></th>
						<th style="border-bottom: 0px !important;"><small><strong>Batch Count</strong></small></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="batch in parsed_data.record_info.batches">
						<td><small>{{ batch.information.expiration_date | moment('MMMM Do YYYY') }}</small></td>
						<td><small>{{ batch.information.delivery_date | moment('MMMM Do YYYY') }}</small></td>
						<td><small>{{ batch.information.number_of_stocks }} Pieces</small></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['data'],
		data(){
			return {
				parsed_data: null,
			}
		},
		created(){
			this.parsed_data = this.data;
			this.parsed_data.record_info = JSON.parse(this.parsed_data.record_info);

			console.log("PRODUCT RESTOCK LOG DATA: ", this.parsed_data);
		}
	}
</script>