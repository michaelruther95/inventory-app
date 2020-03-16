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
			<div class="col-12" :key="'invoice-container-'+change_count">
				<hr>

				<el-table
					:row-class-name="tableRowClassName"
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
						label="Invoiced To"
						prop="invoiced_to"
						width="300"
					>
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
						label="Status"
						prop="status"
					>
						<template slot-scope="scope">
							{{ scope.row.status | capitalize }}
						</template>
					</el-table-column>

					<el-table-column
						label=""
						prop="action"
						width="300"
					>
						<template slot-scope="scope">
							<div class="w-100 text-right">
								<el-button type="info" size="small" plain v-on:click="show_invoice_dialog = true; selected_invoice = scope.row;">
									Invoice Info
								</el-button>
								<el-button type="danger" size="small" plain v-on:click="voidInvoice(scope.row)" v-if="scope.row.status != 'void'">
									Void Invoice
								</el-button>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>


		<el-dialog
			title="Invoice Information"
			:visible.sync="show_invoice_dialog"
			width="70%"
		>
			
			<div v-if="selected_invoice">
				<div class="row m-0">
					<div class="col-12">
						<hr>
					</div>
					<div class="col-12" style="font-size: 15px !important;">
						<strong>Invoice Date:</strong> {{ selected_invoice.purchase_date | moment('MMMM Do YYYY') }}
						<hr>
					</div>
					<div class="col-12" style="font-size: 15px !important;">
						<strong>Invoiced To:</strong> {{ selected_invoice.invoiced_to }}
						<hr>
					</div>
					<div class="col-12" style="font-size: 15px !important;">
						<span class="badge" v-bind:class="{
							'badge-danger': selected_invoice.status == 'void',
							'badge-success': selected_invoice.status != 'void'
						}">
							{{ selected_invoice.status | uppercase }}
						</span>
						<hr>
					</div>
				</div>

				<div class="row m-0">
					<div class="col-3">
						<strong>Product Name</strong>
					</div>
					<div class="col-3">
						<strong>Stocks Purchased</strong>
					</div>
					<div class="col-3">
						<strong>Price Per Stock</strong>
					</div>
					<div class="col-3">
						<strong>Total</strong>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>

				<div class="row m-0 py-2" v-for="invoice_purchase in selected_invoice.raw_info.invoice_purchases">
					<div class="col-3">
						{{ invoice_purchase.purchase_info.item_info.information.name }}
					</div>
					<div class="col-3">
						{{ invoice_purchase.purchase_info.information.stocks_purchase }}
					</div>
					<div class="col-3">
						{{ invoice_purchase.purchase_info.information.price_per_stock | currency('₱') }}
					</div>
					<div class="col-3">
						{{ invoice_purchase.purchase_info.information.stocks_purchase * invoice_purchase.purchase_info.information.price_per_stock | currency('₱') }}
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>

				<div class="row m-0 py-2">
					<div class="col-6">
						<strong>TOTAL:</strong>
					</div>
					<div class="col-6 text-right">
						{{ selected_invoice.total | currency('₱') }}
					</div>
				</div>
			</div>
				

			<span slot="footer" class="dialog-footer">
				<el-button size="small" @click="show_invoice_dialog = false">
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
				invoices: [],
				table_search: null,
				table_date_filter: null,
				change_count: 0,
				show_invoice_dialog: false,
				selected_invoice: null
			}
		},
		watch: {
			'selected_invoice': function(val){
				console.log("SELECTED INVOICE: ", val);
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Invoices. Please Wait...' });

			this.$axios.get('/api/get-invoices', {}).then((response) => {
				for(let counter = 0; counter < response.data.invoices.length; counter++){
					let invoice_object = this.setInvoiceObject(response.data.invoices[counter]);
					this.invoices.push(invoice_object);
				}

				
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				console.log(error);

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},
		methods: {
			setInvoiceObject(parameter){
				let invoice_object = {};
				let invoice_total = 0;
				let invoiced_to = null;

				for(let purchase_counter = 0; purchase_counter < parameter['invoice_purchases'].length; purchase_counter++){
					invoice_total += parameter['invoice_purchases'][purchase_counter]['purchase_info']['information']['total'];
					invoiced_to = parameter['invoice_purchases'][purchase_counter]['purchase_info']['information']['sold_to'];
				}

				invoice_object['purchase_date'] = parameter['created_at'];
				invoice_object['total'] = invoice_total;
				invoice_object['invoiced_to'] = invoiced_to;
				invoice_object['status'] =  parameter['information']['status'];
				invoice_object['action'] = '';
				invoice_object['raw_info'] = parameter;

				return invoice_object;
			},

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
			},

			voidInvoice(params){
				console.log("VOID INVOICE PARAMS: ", params);

				this.$store.dispatch('pageLoader', { display: true, message: 'Voiding Invoice. Please Wait...' });

				this.$axios.post('/api/void-invoice', { invoice_id: params.raw_info.id }).then((response) => {
					for(let counter = 0; counter < this.invoices.length; counter++){
						if(params.raw_info.id == this.invoices[counter]['raw_info']['id']){
							let invoice_object = this.setInvoiceObject(response.data.invoice_info);
							this.invoices[counter] = invoice_object;

							console.log("YAWA!");
							this.change_count++;

							this.$message({
					          	message: 'Invoice successfully voided.',
					          	showClose: true,
					          	type: 'success'
					        });

							break;
						}
					}
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					console.log(error);
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},

			tableRowClassName({row, rowIndex}){
				console.log("ROW CLASS NAME: ", row);
				if(row.status == 'void'){
					return 'nullify-el-hover bg-opacity-danger';
				}
				else{
					return 'nullify-el-hover bg-opacity-success';
				}
			},
		}
	}
</script>