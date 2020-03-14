<template>
	<div id="supplier-products-list-component">

		<label class="d-block"><strong>Supplier Name: </strong>{{ record.information.supplier_name }}</label>
		<hr>
		<label class="d-block"><strong>Contact Number: </strong>+63{{ record.information.contact_number }}</label>
		<hr>

		<el-table
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
				label="Generic Name"
				prop="generic_name"
			>
				<template slot-scope="scope">
					<span v-if="scope.row.generic_name">
						{{ scope.row.generic_name }}
					</span>
					<span v-else>
						N/A
					</span>
				</template>
			</el-table-column>
			<el-table-column
				label="Brand"
				prop="brand"
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
			</el-table-column>
		</el-table>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['record'],
		data(){
			return {
				table_search: '',
				products: []
			}
		},
		created(){
			console.log("SUPPLIER PRODUCTS LIST: ", this.record);
			for(let counter = 0; counter < this.record.product_suppliers.length; counter++){

				let product_info = this.record.product_suppliers[counter]['product_info'];


				this.products.push({
					id: product_info.id,
					product_type: product_info.information.product_type,
					item_name: product_info.information.name,
					generic_name: product_info.information.generic_name,
					brand: product_info.information.brand,
					raw_info: product_info
				});
			}
		}
	}
</script>