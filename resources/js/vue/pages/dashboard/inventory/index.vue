<template>
	<div id="inventory-page-component">
		<div class="row m-0">
			<div class="col-lg-12 px-0">
				<el-button type="info" size="small" v-on:click="show_product_form = true; product_form_action = 'create'">
					<i class="el-icon-circle-plus"></i> Add New Product / Item
				</el-button>
				<hr>


				<el-table
					:row-class-name="tableRowClassName"
					:data="products.filter(
					data => !table_search || 
						(
							data.item_name.toLowerCase().includes(table_search.toLowerCase())
						)
					)"
					style="width: 100%"
				>
					<!-- <el-table-column
						label="ID #"
						prop="id"
					>
					</el-table-column> -->
					<el-table-column
						label="Item Name"
						prop="item_name"
					>
					</el-table-column>
					<el-table-column
						label="Generic Name"
						prop="generic_name"
					>
					</el-table-column>
					<el-table-column
						label="Brand"
						prop="brand"
					>
					</el-table-column>
					<el-table-column
						label="Total Stocks"
						prop="total_stocks"
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
							<el-dropdown @command="handleUpdateRecord($event, scope.$index, scope.row)">
								<el-button size="mini">
									Actions <i class="el-icon-arrow-down"></i>
								</el-button>
								<el-dropdown-menu slot="dropdown">
									<el-dropdown-item icon="el-icon-notebook-1" command="create purchase">
										Create Purchase
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-edit" command="update">
										Update
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-plus" command="restock">
										Restock
									</el-dropdown-item>
									<el-dropdown-item icon="el-icon-folder-opened" command="view record">
										View Record
									</el-dropdown-item>
								</el-dropdown-menu>
							</el-dropdown>
						</template>
					</el-table-column>
				</el-table>

			</div>
		</div>

		<!-- ---------------------------------------------------------------------------------- -->
		<!-- PRODUCT DIALOG FORM -->
		<!-- ---------------------------------------------------------------------------------- -->
		<el-dialog 
			title="Product Form" 
			:visible.sync="show_product_form" 
			width="30%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>
			<product-form 
				:product="selected_product" 
				v-if="show_product_form" 
				:submitting="product_form_submitting"
				v-on:aftersubmit="handleAfterSubmitProductForm($event)"
				:action="product_form_action"
			></product-form>
			
			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" v-on:click="product_form_submitting = true">
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_product_form = false">
					Close
				</el-button>
			</span>
		</el-dialog>
		<!-- ---------------------------------------------------------------------------------- -->



		<!-- ---------------------------------------------------------------------------------- -->
		<!-- RESTOCK DIALOG FORM -->
		<!-- ---------------------------------------------------------------------------------- -->
		<el-dialog 
			title="Product Restock Form" 
			:visible.sync="show_restock_form" 
			width="30%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>	
			<restock-form
				v-if="show_restock_form"
				:submitting="restock_form_submitting"
				:product="selected_product"
				:action="restock_form_action"
				v-on:aftersubmit="handleAfterSubmitRestockForm($event)"
			></restock-form>
			
			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" v-on:click="restock_form_submitting = true">
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_restock_form = false">
					Close
				</el-button>
			</span>
		</el-dialog>
		<!-- ---------------------------------------------------------------------------------- -->



		<!-- ---------------------------------------------------------------------------------- -->
		<!-- PURCHASE DIALOG FORM -->
		<!-- ---------------------------------------------------------------------------------- -->
		<el-dialog 
			title="Product Purchase Form" 
			:visible.sync="show_purchase_form" 
			width="42%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>	
			
			<div class="row m-0" v-if="show_purchase_form">
				<div class="col-12" v-if="selected_product.total_stocks - purchase_form.stocks_to_buy < 0">
					<div class="alert alert-danger font-weight-bold" style="font-size: 13px !important;">
						The number of stocks you want to buy is greater than the available stocks.
					</div>
				</div>

				<div class="col-6">
					<label class="mb-0"><strong>Item Name:</strong></label>
				</div>
				<div class="col-6 text-right">
					<label class="mb-0">{{ selected_product.raw_info.information.name }}</label>
				</div>
				<div class="col-12"><hr></div>
				<div class="col-6">
					<label class="mb-0"><strong>Current Stock(s):</strong></label>
				</div>
				<div class="col-6 text-right">
					<label class="mb-0" v-bind:class="{
						'text-danger': selected_product.total_stocks - purchase_form.stocks_to_buy < 0
					}">
						{{ selected_product.total_stocks - purchase_form.stocks_to_buy }}
					</label>
				</div>
				<div class="col-12"><hr></div>
				<div class="col-6">
					<label class="mb-0"><strong>Item Price:</strong></label>
				</div>
				<div class="col-6 text-right">
					<label class="mb-0">{{ selected_product.raw_info.information.price }}</label>
				</div>
				<div class="col-12">
					<hr>
					<div id="purchase-form-section">
						<label class="input-label"><small>Number Of Stocks</small></label>
						<el-input placeholder="Enter Product Name Here..." size="small" type="number" v-model="purchase_form.stocks_to_buy"></el-input>
						<p class="text-danger"><small>{{ purchase_form_api_validators.stocks_to_buy }}</small></p>

						<label class="input-label"><small>Sold To</small></label>
						<el-input placeholder="Enter Buyer's Name Here..." size="small" v-model="purchase_form.sold_to"></el-input>
						<p class="text-danger"><small>{{ purchase_form_api_validators.sold_to }}</small></p>
					</div>
					<hr>
				</div>
				<div class="col-6">
					<label class="mb-0"><strong>Total:</strong></label>
				</div>
				<div class="col-6 text-right">
					<label class="mb-0">{{ selected_product.raw_info.information.price * purchase_form.stocks_to_buy | currency('₱') }}</label>
				</div>
			</div>

					

			<span slot="footer" class="dialog-footer">
				<el-button 
					size="small" 
					type="success" 
					v-on:click="submitPurchase()" 
					:disabled="selected_product.total_stocks - purchase_form.stocks_to_buy < 0"
				>
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_purchase_form = false">
					Close
				</el-button>
			</span>
		</el-dialog>
		<!-- ---------------------------------------------------------------------------------- -->



		<!-- ---------------------------------------------------------------------------------- -->
		<!-- PRODUCT INFO DIALOG FORM -->
		<!-- ---------------------------------------------------------------------------------- -->
		<el-dialog 
			title="Product Information" 
			:visible.sync="show_record_information" 
			width="70%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>
			<record-information
				v-if="show_record_information"
				:record="selected_product"
			></record-information>

			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="danger" v-on:click="show_record_information = false">
					Close
				</el-button>
			</span>
		</el-dialog>
		<!-- ---------------------------------------------------------------------------------- -->
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				table_search: '',
				products: [],

				show_product_form: false,
				product_form_submitting: false,
				product_form_action: '',
				selected_product: {},
			
				show_restock_form: false,
				restock_form_submitting: false,
				restock_form_action: '',

				show_purchase_form: false,
				purchase_form_submitting: false,
				purchase_form: {
					product_id: '',
					stocks_to_buy: '',
					sold_to: ''
				},
				purchase_form_api_validators: {
					product_id: '',
					stocks_to_buy: '',
					sold_to: ''
				},

				show_record_information: false
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Retrieving Products List, Please Wait...' });
			this.$axios.get('/api/get-products', {}).then((response) => {
				for(let counter = 0; counter < response.data.products.length; counter++){
					this.products.push(this.setProductObject(response.data.products[counter]));
				}
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},
		components: {
			'product-form': require('./components/product-form.vue').default,
			'restock-form': require('./components/restock-form.vue').default,
			'record-information': require('./components/record-information.vue').default
		},
		methods: {
			setProductObject(param){
				console.log("SET PRODUCT OBJECT: ", param);

				return {
					id: param.id,
					item_name: param.information.name,
					generic_name: param.information.generic_name,
					brand: param.information.brand,
					total_stocks: this.addAllBatches(param),
					raw_info: param
				};
			},

			handleAfterSubmitProductForm(param){
				if(param.data){
					if(this.product_form_action == 'create'){
						this.products.push(this.setProductObject(param.data));
					}
					if(this.product_form_action == 'update'){
						for(let counter = 0; counter < this.products.length; counter++){
							if(this.products[counter]['id'] == this.selected_product.id){
								this.products[counter] = this.setProductObject(param.data);
								break;
							}
						}
					}

					this.$message({
			          	message: 'Product information successfully saved.',
			          	showClose: true,
			          	type: 'success'
			        });
				}

				this.product_form_submitting = param.submitting;
				this.show_product_form = param.show_form;
			},

			handleUpdateRecord(action, index, data){
				console.log("HANDLE UPDATE RECORD: ");
				this.selected_product = data;
				if(action == 'update'){
					this.show_product_form = true; 
					this.product_form_action = 'update';
				}
				if(action == 'restock'){
					this.show_restock_form = true;
					this.restock_form_action = 'create';
				}
				if(action == 'create purchase'){
					this.show_purchase_form = true;
					this.clearApiValidators();
					for(let key in this.purchase_form){
						this.purchase_form[key] = '';
					}
				}
				if(action == 'view record'){
					console.log("VIEW RECORD!");
					this.show_record_information = true;
				}

				console.log("SELECTED PRODUCT: ", this.selected_product);
			},

			handleAfterSubmitRestockForm(param){
				if(param.product){
					for(let counter = 0; counter < this.products.length; counter++){
						if(this.products[counter]['id'] == this.selected_product.id){
							this.products[counter] = this.setProductObject(param.product);
							break;
						}
					}

					this.$message({
			          	message: 'Product successfully has a new batch of stocks saved.',
			          	showClose: true,
			          	type: 'success'
			        });
				}

				this.show_restock_form = param.show_form;
				this.restock_form_submitting = param.submitting;
			},

			addAllBatches(param){
				let stock_count = 0;
				for(let counter = 0; counter < param.batches.length; counter++){
					if(!param.batches[counter]['information']['is_expired']){
						stock_count += param.batches[counter]['information']['remaining_stocks'];
					}
				}

				return stock_count;
			},

			tableRowClassName({row, rowIndex}){
				if(row.total_stocks < 20 && row.total_stocks > 0){
					return 'nullify-el-hover bg-opacity-warning';
				}
				if(row.total_stocks <= 0){
					return 'nullify-el-hover bg-opacity-danger';
				}

				return '';
			},


			submitPurchase(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Creating A Product Purchase, Please Wait...' });
				this.clearApiValidators();

				this.purchase_form['product_id'] = this.selected_product.id;
				this.$axios.post('/api/create-product-purchase', this.purchase_form).then((response) => {
					for(let counter = 0; counter < this.products.length; counter++){
						if(this.products[counter]['id'] == this.selected_product.id){
							this.products[counter] = this.setProductObject(response.data.product);
							this.$message({
					          	message: 'Purchase successfully created.',
					          	showClose: true,
					          	type: 'success'
					        });
					        this.show_purchase_form = false;
							break;
						}
					}

					this.$store.dispatch('pageLoader', { display: false, message: '' });
				}).catch((error) => {
					if(error.response){
						if(error.response.status == 422){
							for(let key in error.response.data.errors){
								this.purchase_form_api_validators[key] = error.response.data.errors[key][0];
							}
						}
					}
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			},

			clearApiValidators(){
				for(let key in this.purchase_form_api_validators){
					this.purchase_form_api_validators[key] = '';
				}
			}
		}
	}
</script>