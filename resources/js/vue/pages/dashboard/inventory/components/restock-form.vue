<template>
	<div id="restock-form-component">
		<label class="input-label"><small>Number Of Stocks</small></label>
		<el-input placeholder="Enter Number Of Stocks Here..." size="small" v-model="form.number_of_stocks"></el-input>
		<p class="text-danger"><small>{{ api_validators.number_of_stocks }}</small></p>

		<label class="input-label"><small>Expiration Date</small></label>
		<el-date-picker placeholder="Select Date Here Here..." size="small" v-model="form.expiration_date_display"></el-date-picker>
		<p class="text-danger"><small>{{ api_validators.expiration_date }}</small></p>

		<label class="input-label"><small>Supplier</small></label>
		<el-input placeholder="Enter Supplier Here..." size="small" v-model="form.supplier"></el-input>
		<p class="text-danger"><small>{{ api_validators.supplier }}</small></p>

		<label class="input-label"><small>Delivery Date</small></label>
		<el-date-picker placeholder="Select Delivery Date Here..." size="small" v-model="form.delivery_date_display"></el-date-picker>
		<p class="text-danger"><small>{{ api_validators.delivery_date }}</small></p>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['submitting', 'product', 'action'],
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
				}
			}
		},

		methods: {
			clearApiValidators(){
				for(let key in this.api_validators){
					this.api_validators[key] = '';
				}
			}
		}
	}
</script>