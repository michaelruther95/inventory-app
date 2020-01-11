<template>
	<div id="default-layout-component">
		<div id="default-layout-content-wrapper">
			<router-view></router-view>
		</div>
		
	</div>
</template>
<style type="text/css">
	#default-layout-content-wrapper {
		height: 100vh !important; 
		overflow-y: hidden; 
		align-items: center;
		display: flex;
		justify-content: center;
		background: #bcbbb930 !important;
	}

	#default-layout-content-wrapper:first-child {
		height: auto;
	    overflow-y: auto;
	    padding-top: 60px;
	    padding-bottom: 60px;
	}
</style>