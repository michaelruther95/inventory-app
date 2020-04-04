<template>
	<div id="update-patient-changes-component">
		<div v-if="parsed_data">
			<label><strong>Invoice ID: </strong>{{ parsed_data.record_info.id }}</label>

			<table class="table">
				<thead>
					<tr>
						<th style="border-bottom: 0px !important;"><small><strong>Product Name</strong></small></th>
						<th style="border-bottom: 0px !important;"><small><strong>Stocks Purchased</strong></small></th>
						<th style="border-bottom: 0px !important;"><small><strong>Price Per Stock</strong></small></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="invoice_purchase in parsed_data.record_info.invoice_purchases">
						<td><small>{{ invoice_purchase.purchase_info.item_info.information.name }}</small></td>
						<td><small>{{ invoice_purchase.purchase_info.information.stocks_purchase }}</small></td>
						<td><small>{{ invoice_purchase.purchase_info.information.price_per_stock | currency('₱') }}</small></td>
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

			console.log("PURCHASE LOG DATA: ", this.parsed_data);
		}
	}
</script>