<template>
	<div id="dashboard-home-page-component">
		<h1>dashboard home page component!</h1>
	</div>
</template>
<script type="text/javascript">
	export default {
		created(){
			this.$store.dispatch('pageLoader', { display: false, message: '' });
		}
	}
</script>