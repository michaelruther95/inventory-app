<template>
	<div id="update-patient-changes-component">
		<table class="table mt-3">
			<tr>
				<th><small></small></th>
				<th><small><strong>Previous Value</strong></small></th>
				<th><small><strong>Current Value</strong></small></th>
			</tr>
			<tr>
				<th><small><strong>Email Address:</strong></small></th>
				<td><small>{{ data.orig_value.email }}</small></td>
				<td><small>{{ data.new_value.email }}</small></td>
			</tr>

			<tr>
				<th><small><strong>First Name:</strong></small></th>
				<td><small>{{ data.orig_value.information.first_name }}</small></td>
				<td><small>{{ data.new_value.information.first_name }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Last Name:</strong></small></th>
				<td><small>{{ data.orig_value.information.last_name }}</small></td>
				<td><small>{{ data.new_value.information.last_name }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Phone Number:</strong></small></th>
				<td><small>{{ data.orig_value.information.phone_number }}</small></td>
				<td><small>{{ data.new_value.information.phone_number }}</small></td>
			</tr>

			<tr>
				<th><small><strong>Address:</strong></small></th>
				<td><small>{{ data.orig_value.information.address }}</small></td>
				<td><small>{{ data.new_value.information.address }}</small></td>
			</tr>
		</table>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: ['data']
	}
</script>