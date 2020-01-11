<template>
	<div id="main-app-component" style="height: 100vh !important;" v-loading="$store.getters.isLoading" element-loading-background="rgba(0, 0, 0, 0.8)" :element-loading-text="$store.getters.getLoadingMessage">
		<div v-if="layout">
			<default-layout v-if="layout == 'default'"></default-layout>
			<dashboard-layout v-if="layout == 'dashboard'"></dashboard-layout>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		components: {
			'default-layout': require('./layouts/default.vue').default,
			'dashboard-layout': require('./layouts/dashboard.vue').default
		},
		computed: {
			layout(){
				return this.$route.meta.layout;
			}
		},
	}
</script>