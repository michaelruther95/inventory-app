<template>
	<div id="product-information-component">
		<label class="d-block text-dark"><strong>{{ record.item_name }} ({{ record.generic_name }})</strong></label>
		<label class="d-block text-secondary">{{ record.brand }}</label>
		<hr>
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
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['record'],
		data(){
			return {
				invoices: [],
			}
		},
		created(){
			console.log("SELECTED RECORD: ", this.record);
			for(let counter = 0; counter < this.record.raw_info.purchases.length; counter++){
				let new_invoice_object = {
					'purchase_date': this.record.raw_info.purchases[counter]['created_at'],
					'stocks_purchase': this.record.raw_info.purchases[counter]['information']['stocks_purchase'],
					'price_per_stock': this.record.raw_info.purchases[counter]['information']['price_per_stock'],
					'total': this.record.raw_info.purchases[counter]['information']['total']
				};

				this.invoices.push(new_invoice_object);
			}
		}
	}
</script>