<template>
	<div id="product-form-component">
		<label class="input-label"><small>Name</small></label>
		<el-input placeholder="Enter Product Name Here..." size="small" v-model="form.name"></el-input>
		<p class="text-danger"><small>{{ api_validators.name }}</small></p>

		<label class="input-label"><small>Generic Name</small></label>
		<el-input placeholder="Enter Generic Name Here..." size="small" v-model="form.generic_name"></el-input>
		<p class="text-danger"><small>{{ api_validators.generic_name }}</small></p>

		<label class="input-label"><small>Brand</small></label>
		<el-input placeholder="Enter Brand Here..." size="small" v-model="form.brand"></el-input>
		<p class="text-danger"><small>{{ api_validators.brand }}</small></p>

		<label class="input-label"><small>Price</small></label>
		<el-input type="number" placeholder="Enter Product's Price Here..." size="small" v-model="form.price"></el-input>
		<p class="text-danger"><small>{{ api_validators.price }}</small></p>

		<label class="input-label"><small>Description (Optional)</small></label>
		<el-input type="textarea" :rows="3" placeholder="Enter Product Description Here..." size="small" v-model="form.description"></el-input>
		<p class="text-danger"><small>{{ api_validators.description }}</small></p>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['submitting', 'action', 'product'],
		watch: {
			'submitting': function(val){
				console.log("IS SUBMITTING: ", val);
				if(val){
					this.$store.dispatch('pageLoader', { display: true, message: 'Submitting Product Information, Please Wait...' });
					this.clearApiValidators();

					let uri = '/api/create-new-product';
					if(this.action == 'update'){
						uri = '/api/update-product';
					}

					this.$axios.post(uri, this.form).then((response) => {
						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$emit('aftersubmit', { submitting: false, data: response.data.product, show_form: false });
					}).catch((error) => {
						if(error.response){
							if(error.response.status == 422){
								for(let key in error.response.data.errors){
									this.api_validators[key] = error.response.data.errors[key][0];
								}
							}
						}

						this.$store.dispatch('pageLoader', { display: false, message: '' });
						this.$emit('aftersubmit', { submitting: false, show_form: true });
					});
				}		
			}
		},
		created(){
			if(this.action == 'update'){
				console.log("SELECTED PRODUCT: ", this.product);

				for(let key in this.form){
					this.form[key] = this.product.raw_info.information[key];
				}
				this.form['id'] = this.product.raw_info.id;
			}
		},
		data(){
			return {
				form: {
					id: '',
					name: '',
					generic_name: '',
					description: '',
					brand: '',
					price: ''
				},
				api_validators: {
					id: '',
					name: '',
					generic_name: '',
					description: '',
					brand: '',
					price: ''
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