<template>
	<div id="inventory-page-component">
		<div class="row m-0">
			<div class="col-6 px-0">
				<el-button type="info" size="small" v-on:click="show_product_form = true; product_form_action = 'create'">
					<i class="el-icon-circle-plus"></i> Add New Product / Item
				</el-button>
			</div>
			<div class="col-6 px-0 text-right">
				<el-button type="primary" size="small" plain v-on:click="show_multiple_purchase_dialog = true;">
					View Products On Purchase List ({{ selected_product_id_list.length }})
				</el-button>
			</div>
			<div class="col-lg-12 px-0">
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
					<el-table-column
						label="Item Name"
						prop="item_name"
					>
					</el-table-column>
					<el-table-column
						label="Type"
						prop="product_type"
					>
						<template slot-scope="scope">
							{{ scope.row.product_type | capitalize }}
						</template>
					</el-table-column>
					<el-table-column
						label="Expiry Status"
						prop="is_near_to_expire"
						width="250"
					>
						<template slot-scope="scope">
							<span style="font-size: 10px !important;">
								<span v-if="scope.row.is_near_to_expire" class="text-danger font-weight-bold">
									HAS BATCHES NEAR TO EXPIRE
								</span>
								<span v-else class="text-success font-weight-bold">
									NO BATCHES NEAR TO EXPIRE
								</span>
							</span>
						</template>
					</el-table-column>
					<el-table-column
						label="Brand"
						prop="brand"
					>
					</el-table-column>
					<!-- <el-table-column
						label="Remaining Stocks"
						prop="total_stocks"
					>
					</el-table-column> -->
					<el-table-column
						label="Stocks Purchased"
						prop="stocks_purchased"
					>
						<template slot-scope="scope">
							{{ scope.row.stocks_purchased }} 
							out of {{ scope.row.total_stocks }}
						</template>
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
									<!-- <el-dropdown-item icon="el-icon-notebook-1" command="create purchase">
										Create Single Purchase
									</el-dropdown-item> -->
									<el-dropdown-item icon="el-icon-sell" command="add to purchase list">
										Add To Purchase List
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
				:suppliers="suppliers"
				v-on:aftersubmit="handleAfterSubmitRestockForm($event)"
				v-on:resetsupplierlist="suppliers = $event"
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
			width="90%" 
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



		<!-- ---------------------------------------------------------------------------------- -->
		<!-- MULTIPLE PRODUCTS TO BUY DIALOG -->
		<!-- ---------------------------------------------------------------------------------- -->
		<el-dialog 
			title="Purchase List" 
			:visible.sync="show_multiple_purchase_dialog" 
			width="90%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>
			<div>
				<div class="alert alert-danger mb-3" v-if="checkIfStockToPurchaseIsValid()">
					Please make sure your purchase list has valid values of stocks to purchase.
				</div>

				<div class="row m-0">
					<div class="col-5">
						<el-input v-model="sold_to" size="small">
							<template slot="prepend">
								Sold To
							</template>
						</el-input>
						<p class="text-danger"><small>{{ purchase_form_api_validators.sold_to }}</small></p>
					</div>

					<div class="col-12">
						<hr>
					</div>

					<div class="col-2">
						Item Name
					</div>
					<div class="col-2">
						Current Price
					</div>
					<div class="col-2">
						Remaining Stocks
					</div>
					<div class="col-2">
						Stocks To Purchase
					</div>
					<div class="col-2">
						Payable Amount
					</div>
					<div class="col-2"></div>
				</div>

				<div v-if="selected_product_id_list.length > 0">
					<div v-for="(product, product_index) in products">
						<div v-bind:class="{
							'alert alert-danger p-0 my-3': product.raw_info.stocks_to_buy > product.total_stocks
						}">
							<div class="row m-0 py-3" v-if="product.raw_info.is_selected">
								<div class="col-2 pt-2">
									{{ product.raw_info.information.name }}
								</div>
								<div class="col-2 pt-2">
									{{ product.raw_info.information.price | currency('₱') }}
								</div>
								<div class="col-2 pt-2">
									{{ product.total_stocks }}
								</div>
								<div class="col-2">
									<el-input size="small" type="number" v-model="product.raw_info.stocks_to_buy" v-on:change="checkIfStockToPurchaseIsValid()"></el-input>
								</div>
								<div class="col-2 pt-2">
									{{ product.raw_info.information.price * product.raw_info.stocks_to_buy | currency('₱') }}
								</div>
								<div class="col-2 text-right">
									<a href="javascript:void(0)" class="text-danger" style="font-size: 25px; float: right; margin-top: -3px;">
										<i class="fa fa-fw el-icon-delete"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div v-else class="text-center py-3">
					<label>No Selected Product(s) To Purchase...</label>
				</div>
			</div>

			<!-- <h1>SELECTED PRODUCT ID LIST LENGTH: {{ selected_product_id_list.length }}</h1>
			<hr>
			<h1>CHECK IF STOCK PURCHASE IS VALID: {{ checkIfStockToPurchaseIsValid() }}</h1> -->

			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" :disabled="selected_product_id_list.length <= 0 || checkIfStockToPurchaseIsValid()" v-on:click="submitMultiplePurchase()">
					Purchase Items
				</el-button>

				<el-button size="small" type="danger" v-on:click="show_multiple_purchase_dialog = false">
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
				table_search: '',
				products: [],
				suppliers: [],

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

				show_record_information: false,

				selected_product_id_list: [],
				show_multiple_purchase_dialog: false,
				stock_has_negative_value: false,
				sold_to: ''
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Retrieving Products List, Please Wait...' });
			this.$axios.get('/api/get-products', {}).then((response) => {
				this.suppliers = response.data.suppliers;
				this.handleResponse(response);
				

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
			handleResponse(response){
				let product_list = [];

				for(let counter = 0; counter < response.data.products.length; counter++){
					product_list.push(this.setProductObject(response.data.products[counter]));
				}

				this.products = product_list;
			},

			setProductObject(param){
				console.log("SET PRODUCT OBJECT: ", param);

				return {
					id: param.id,
					product_type: param.information.product_type,
					item_name: param.information.name,
					generic_name: param.information.generic_name,
					is_near_to_expire: param.is_near_to_expire,
					brand: param.information.brand,
					total_stocks: this.addAllBatches(param),
					stocks_purchased: this.addAllPurchases(param),
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
				if(action == 'add to purchase list'){
					// console.log("SELECTED PRODUCT ROW: ", this.products[index]['']);
					if(this.selected_product_id_list.includes(this.products[index]['raw_info']['id'])){
						this.$message({
				          	message: 'Product already on the purchase list.',
				          	showClose: true,
				          	type: 'warning'
				        });
					}
					else{
						if(this.products[index]['total_stocks'] <= 0){
							this.$message({
					          	message: "Cannot add products that has no stocks left available.",
					          	showClose: true,
					          	type: 'warning'
					        });
						}
						else{
							this.products[index]['raw_info']['is_selected'] = true;
							this.selected_product_id_list.push(this.products[index]['raw_info']['id']);
						}
							
					}
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
				console.log("ADD ALL BATCHES: ", param.batches);

				let stock_count = 0;
				for(let counter = 0; counter < param.batches.length; counter++){
					if(!param.batches[counter]['information']['is_expired']){
						// stock_count += param.batches[counter]['information']['remaining_stocks'];
						stock_count += param.batches[counter]['information']['number_of_stocks'];
					}
				}

				return stock_count;
			},

			addAllPurchases(param){
				let stock_count = 0;
				for(let counter = 0; counter < param.batches.length; counter++){
					stock_count += param.batches[counter]['information']['stocks_purchased'];
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
			},

			checkIfStockToPurchaseIsValid(){
				let boolean_to_return = false;

				for(let counter = 0; counter < this.products.length; counter++){
					let stocks_to_buy = this.products[counter]['raw_info']['stocks_to_buy'];
					let remaining_stocks = this.products[counter]['total_stocks'];

					if(!isNaN(stocks_to_buy)){
						if(this.products[counter]['raw_info']['is_selected']){
							if(!stocks_to_buy || stocks_to_buy <= 0){
								boolean_to_return = true;
								break;
							}
							else{
								if(stocks_to_buy > remaining_stocks){
									boolean_to_return = true;
									break;
								}
							}	
						}
					}
					else{
						boolean_to_return = true;
					}
				}

				console.log("BOOLEAN TO RETURN", boolean_to_return);
				return boolean_to_return;
			},

			submitMultiplePurchase(){
				this.$store.dispatch('pageLoader', { display: true, message: 'Submitting Purchase List, Please Wait...' });

				this.clearApiValidators();

				let data_to_send = {};
				let product_list = [];

				for(let counter = 0; counter < this.products.length; counter++){
					if(this.products[counter]['raw_info']['is_selected']){
						let product_object = {
							product_id: this.products[counter]['raw_info']['id'],
							stocks_to_buy: this.products[counter]['raw_info']['stocks_to_buy']
						};

						product_list.push(product_object);
					}
				}

				data_to_send['product_list'] = product_list;
				data_to_send['sold_to'] = this.sold_to;

				this.$axios.post('/api/purchase-multiple-product', data_to_send).then((response) => {
					this.handleResponse(response);

					this.selected_product_id_list = [];
					this.show_multiple_purchase_dialog = false;
					this.stock_has_negative_value = false;
					this.sold_to = '';

					this.$message({
			          	message: 'Purchase success!',
			          	showClose: true,
			          	type: 'success'
			        });
					this.$store.dispatch('pageLoader', { display: false, message: '' });						
				}).catch((error) => {
					if(error.response){
						if(error.response.status == 422){
							if(error.response.data.reason == 'validator'){
								for(let key in error.response.data.errors){
									this.purchase_form_api_validators[key] = error.response.data.errors[key][0];
								}
							}
							if(error.response.data.reason == 'stock_checking'){
								this.handleResponse(response);
							}
						}
					}
					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
				// purchase-multiple-product
			}
		}
	}
</script>