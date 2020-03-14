<template>
	<div id="invoices-page-component">
		<div class="row m-0">
			<div class="col-lg-4">
				<el-date-picker
					v-model="table_date_filter"
					type="date"
					placeholder="Select Date Here To Filter Results..."
					size="small"
					v-on:change="setDate()"

				>
				</el-date-picker>
			</div>
			<div class="col-12">
				<hr>

				<el-table
					:data="invoices.filter(
						data => !table_search || 
						(
							data.purchase_date.includes(table_search)
						)
					)"
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
						label="Item Purchased"
						prop="item_purchased"
					>
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

					<el-table-column
						label="Action"
						prop="action"
					>
						<template slot-scope="scope">
							<el-button type="danger" size="small" plain>
								Void Invoice
							</el-button>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				invoices: [],
				table_search: null,
				table_date_filter: null,
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Invoices. Please Wait...' });

			this.$axios.get('/api/get-invoices', {}).then((response) => {
				for(let counter = 0; counter < response.data.purchases.length; counter++){
					let purchase_object = {
						purchase_date: response.data.purchases[counter]['created_at'],
						item_purchased: response.data.purchases[counter]['item_info']['information']['name'],
						stocks_purchase: response.data.purchases[counter]['information']['stocks_purchase'],
						price_per_stock: response.data.purchases[counter]['information']['price_per_stock'],
						total: response.data.purchases[counter]['information']['total'],
						action: '',
						raw_info: response.data.purchases[counter]
					};

					this.invoices.push(purchase_object);
				}
				
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				console.log(error);

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},
		methods: {
			setDate(){
				if(this.table_date_filter){
					let splitted_date = this.$elementHelper.formatDate(this.table_date_filter).split(' ');
					console.log("SET DATE FILTER: ", splitted_date[0]);
					this.table_search = splitted_date[0];
				}
				else{
					this.table_date_filter = null;
					this.table_search = null;
				}
			}
		}
	}
</script>