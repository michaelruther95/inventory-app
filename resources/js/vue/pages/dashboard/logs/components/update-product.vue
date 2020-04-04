<template>
	<div id="update-patient-changes-component">
		<table class="table mt-3">
			<tr>
				<th><small></small></th>
				<th><small><strong>Previous Value</strong></small></th>
				<th><small><strong>Current Value</strong></small></th>
			</tr>
			<tr>
				<th><small><strong>Product Name:</strong></small></th>
				<td><small>{{ data.orig_value.information.name }}</small></td>
				<td><small>{{ data.new_value.information.name }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Type:</strong></small></th>
				<td><small>{{ data.orig_value.information.product_type }}</small></td>
				<td><small>{{ data.new_value.information.product_type }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Generic Name:</strong></small></th>
				<td><small>{{ data.orig_value.information.generic_name }}</small></td>
				<td><small>{{ data.new_value.information.generic_name }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Brand:</strong></small></th>
				<td><small>{{ data.orig_value.information.brand }}</small></td>
				<td><small>{{ data.new_value.information.brand }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Price:</strong></small></th>
				<td><small>{{ data.orig_value.information.price | currency('₱') }}</small></td>
				<td><small>{{ data.new_value.information.price | currency('₱') }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Description:</strong></small></th>
				<td>
					<small>
						<span v-if="data.orig_value.information.description">
							{{ data.orig_value.information.description }}
						</span>
						<span v-else>
							N/A
						</span>
					</small>
				</td>
				<td>
					<small>
						<span v-if="data.new_value.information.description">
							{{ data.new_value.information.description }}
						</span>
						<span v-else>
							N/A
						</span>
					</small>
				</td>
			</tr>
		</table>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['data']
	}
</script>