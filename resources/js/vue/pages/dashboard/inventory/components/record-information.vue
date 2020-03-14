<template>
	<div id="product-information-component">
		<label class="d-block text-dark"><strong>{{ record.item_name }} ({{ record.generic_name }})</strong></label>
		<label class="d-block text-secondary">{{ record.brand }}</label>
		<hr>

		<el-tabs v-model="selected_tab">
			<el-tab-pane label="Purchase History" name="purchase_history">
				<el-table
					:data="invoices"
					style="width: 100%"
				>
					<el-table-column
						label="Purchase Date"
						prop="purchase_date"
					>
						<template slot-scope="scope">
							{{ scope.row.purchase_date | moment('MMMM Do YYYY') }}
						</template>
					</el-table-column>

					<el-table-column
						label="Stocks Purchased"
						prop="stocks_purchase"
					>
					</el-table-column>

					<el-table-column
						label="Sold To"
						prop="sold_to"
					>
					</el-table-column>

					<el-table-column
						label="Price Per Stock"
						prop="price_per_stock"
					>
						<template slot-scope="scope">
							{{ scope.row.price_per_stock | currency('₱') }}
						</template>
					</el-table-column>

					<el-table-column
						label="Total"
						prop="total"
					>
						<template slot-scope="scope">
							{{ scope.row.total | currency('₱') }}
						</template>
					</el-table-column>
				</el-table>
			</el-tab-pane>
			<el-tab-pane label="Restocking History" name="restocking_history">
				<el-table
					:data="restocking_history"
					style="width: 100%"
				>
					<el-table-column
						label="Stocks Date Received"
						prop="stocks_date_received"
					>
						<template slot-scope="scope">
							{{ scope.row.stocks_date_received | moment('MMMM Do YYYY') }}
						</template>
					</el-table-column>

					<el-table-column
						label="Stocks Added"
						prop="stocks_added"
					>
					</el-table-column>

					<el-table-column
						label="Supplier"
						prop="supplier"
					>
					</el-table-column>

					<el-table-column
						label="Expiration Date"
						prop="expiration_date"
					>
						<template slot-scope="scope">
							{{ scope.row.expiration_date | moment('MMMM Do YYYY') }}
						</template>
					</el-table-column>
				</el-table>
			</el-tab-pane>
		</el-tabs>


				
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['record'],
		data(){
			return {
				selected_tab: 'purchase_history',
				invoices: [],
				restocking_history: []
			}
		},
		created(){
			console.log("RECORD INFORMATION SELECTED RECORD: ", this.record);
			for(let counter = 0; counter < this.record.raw_info.purchases.length; counter++){
				let new_invoice_object = {
					'purchase_date': this.record.raw_info.purchases[counter]['created_at'],
					'stocks_purchase': this.record.raw_info.purchases[counter]['information']['stocks_purchase'],
					'sold_to': this.record.raw_info.purchases[counter]['information']['sold_to'],
					'price_per_stock': this.record.raw_info.purchases[counter]['information']['price_per_stock'],
					'total': this.record.raw_info.purchases[counter]['information']['total']
				};

				this.invoices.push(new_invoice_object);
			}

			for(let counter = 0; counter < this.record.raw_info.batches.length; counter++){
				let new_batch_object = {
					'stocks_date_received': this.record.raw_info.batches[counter]['created_at'],
					'stocks_added': this.record.raw_info.batches[counter]['information']['number_of_stocks'],
					'supplier': this.record.raw_info.batches[counter]['supplier_info']['information']['supplier_name'],
					'expiration_date': this.record.raw_info.batches[counter]['information']['expiration_date']
				};

				this.restocking_history.push(new_batch_object);
			}
		}
	}
</script>