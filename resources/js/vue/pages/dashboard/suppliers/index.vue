<template>
	<div id="suppliers-page-component">
		<div class="row m-0">
			<div class="col-12 px-0">
				<el-button type="primary" size="small" v-on:click="supplier_form_action = 'create'; supplier_form_submitting = false; show_supplier_form = true; supplier_dialog_title = 'Create New Supplier';">
					Add New Supplier
				</el-button>
				<hr>

				<el-table
					:data="suppliers.filter(
						data => !table_search || 
						(
							data.supplier_name.toLowerCase().includes(table_search.toLowerCase())
						)
					)"
					style="width: 100%"
				>
					<el-table-column
						label="Supplier Name"
						prop="supplier_name"
					>
					</el-table-column>

					<el-table-column
						label="Contact Number"
						prop="contact_number"
					>
					</el-table-column>

					<el-table-column
						label="Action"
						prop="action"
					>
						<template slot="header" slot-scope="scope">
							<el-input
								v-model="table_search"
								size="mini"
								placeholder="Search Keyword Here..."
							/>
						</template>

						<template slot-scope="scope">
							<div class="w-100 text-right">
								<el-button type="warning" plain size="small" v-on:click="selected_record = scope.row.raw_info; show_supplier_products_dialog = true;">
									View Supplied Products
								</el-button>
								<el-button type="success" plain size="small" v-on:click="setUpdateForm(scope.row)">
									Edit Supplier
								</el-button>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>




		<el-dialog
			:title="supplier_dialog_title"
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
				:record="selected_record"
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


		<el-dialog
			:title="'Products Supplied'"
			:visible.sync="show_supplier_products_dialog"
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
			:append-to-body="true"
			width="80%"
		>
			<supplier-products-list v-if="show_supplier_products_dialog" :record="selected_record"></supplier-products-list>
			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="danger" v-on:click="show_supplier_products_dialog = false;">
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
				suppliers: [],
				table_search: '',

				supplier_dialog_title: '',
				show_supplier_form: false,
				supplier_form_action: 'create',
				supplier_form_submitting: false,

				selected_record: {},

				show_supplier_products_dialog: false
			}
		},

		components: {
			'supplier-form': require('./components/supplier-form.vue').default,
			'supplier-products-list': require('./components/supplier-products-list.vue').default
		},

		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Suppliers. Please Wait...' });

			this.$axios.get('/api/get-suppliers', {}).then((response) => {
				for(let counter = 0; counter < response.data.suppliers.length; counter++){
					this.suppliers.push(this.setSupplierObject(response.data.suppliers[counter]));
				}

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},

		methods: {
			setSupplierObject(param){
				return {
					supplier_name: param.information.supplier_name,
					contact_number: '+63'+param.information.contact_number,
					action: '',
					raw_info: param
				};
			},

			handleSupplierFormOnClose(params){
				if(params.suppliers){
					this.suppliers = [];
					for(let counter = 0; counter < params.suppliers.length; counter++){
						this.suppliers.push(this.setSupplierObject(params.suppliers[counter]));
					}
				}

				this.show_supplier_form = false;
				this.supplier_form_submitting = false;
			},

			setUpdateForm(data){
				this.supplier_dialog_title = 'Update Supplier';
				this.selected_record = data.raw_info;
				this.supplier_form_action = 'update'; 
				this.supplier_form_submitting = false; 
				this.show_supplier_form = true;

				console.log("SET UPDATE FORM: ", data);
			}
		}
	}
</script>