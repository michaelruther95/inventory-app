<template>
	<div id="dashboard-layout-component">
		<dashboard-navbar></dashboard-navbar>
		<div class="pt-5">
			<div class="pt-5 container">
				<h5>{{ $route.meta.page_title }}</h5>
				<hr>
				<router-view></router-view>
			</div>
		</div>
		
	</div>
</template>
<script type="text/javascript">
	export default {
		components: {
			'dashboard-navbar': require('../components/navbar/dashboard-navbar').default
		}
	}
</script>