<template>
	<div id="restock-form-component">
		<label class="input-label"><small>Number Of Stocks</small></label>
		<el-input placeholder="Enter Number Of Stocks Here..." size="small" v-model="form.number_of_stocks"></el-input>
		<p class="text-danger"><small>{{ api_validators.number_of_stocks }}</small></p>

		<label class="input-label"><small>Expiration Date</small></label>
		<el-date-picker placeholder="Select Date Here Here..." size="small" v-model="form.expiration_date_display"></el-date-picker>
		<p class="text-danger"><small>{{ api_validators.expiration_date }}</small></p>

		<label class="input-label">
			<small>
				Supplier
				<a href="javascript:void(0)" style="float: right; margin-top: 2px;" v-on:click="show_supplier_form = true;">
					Click Here To Add New Supplier
				</a>
			</small>
		</label>
		<el-select v-model="form.supplier" size="small" placeholder="-- Select Supplier Here --">
			<el-option
				v-for="(supplier, supplier_index) in suppliers"
				:label="supplier.information.supplier_name"
				:value="supplier.id"
				:key="'supplier-option-'+supplier_index"
			></el-option>
		</el-select>
		<!-- <el-input placeholder="Enter Supplier Here..." size="small" v-model="form.supplier"></el-input> -->
		<p class="text-danger"><small>{{ api_validators.supplier }}</small></p>

		<label class="input-label"><small>Delivery Date</small></label>
		<el-date-picker placeholder="Select Delivery Date Here..." size="small" v-model="form.delivery_date_display"></el-date-picker>
		<p class="text-danger"><small>{{ api_validators.delivery_date }}</small></p>


		<el-dialog
			title="Create New Supplier"
			:visible.sync="show_supplier_form"
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
			:append-to-body="true"
			width="40%"
		>
			<supplier-form 
				:submitting="supplier_form_submitting"
				:action="supplier_form_action"
				v-if="show_supplier_form"
				v-on:closeform="handleSupplierFormOnClose($event)"
				v-on:aftersubmit="supplier_form_submitting = $event"
			></supplier-form>

			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" v-on:click="supplier_form_submitting = true">
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_supplier_form = false; supplier_form_submitting = false;">
					Close
				</el-button>
			</span>
		</el-dialog>
		
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['submitting', 'product', 'action', 'suppliers'],
		components: {
			'supplier-form': require('../../suppliers/components/supplier-form.vue').default
		},
		watch: {
			'submitting': function(val){
				if(val){
					this.form['action'] = this.action;
					this.form['product_id'] = this.product.id;

					let uri = '/api/create-product-batch';
					if(this.action == 'update'){
						uri = '/api/update-product-batch';
					}

					this.form.expiration_date = this.$elementHelper.addOneDayToDate(this.form.expiration_date_display);
					this.form.delivery_date = this.$elementHelper.addOneDayToDate(this.form.delivery_date_display);

					this.$store.dispatch('pageLoader', { display: true, message: 'Restocking Product, Please Wait...' });
					this.$axios.post(uri, this.form).then((response) => {
						this.$emit('aftersubmit', { show_form: false, product: response.data.product, submitting: false });
						this.$store.dispatch('pageLoader', { display: false, message: '' });
					}).catch((error) => {
						console.log(error);

						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									this.api_validators[key] = error.response.data.errors[key][0];
								}
							}
						}

						this.$emit('aftersubmit', { show_form: true, submitting: false });
						this.$store.dispatch('pageLoader', { display: false, message: '' });
					});
				}
			}
		},
		data(){
			return {
				form: {
					id: '',
					product_id: '',
					number_of_stocks: '',
					expiration_date_display: '',
					expiration_date: '',
					supplier: '',
					delivery_date_display: '',
					delivery_date: '',
				},

				api_validators: {
					id: '',
					product_id: '',
					number_of_stocks: '',
					expiration_date: '',
					supplier: '',
					delivery_date: ''
				},

				show_supplier_form: false,
				supplier_form_action: 'create',
				supplier_form_submitting: false,
			}
		},

		methods: {
			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			},
			handleSupplierFormOnClose(params){
				if(params.suppliers){
					this.$emit('resetsupplierlist', params.suppliers);
				}

				this.show_supplier_form = false;
				this.supplier_form_submitting = false;
			}
		},

		created(){
			console.log("RESTOCK FORM SUPPLIERS: ", this.suppliers);
		}
	}
</script>