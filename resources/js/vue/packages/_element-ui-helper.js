export default function(Vue) {
	Vue.elementHelper = {
		formatDate(given_date){
			if(given_date){
				let raw_date = new Date(given_date);
				raw_date.setDate(raw_date.getDate() + 1);
				let date_to_return = new Date(raw_date).toISOString().slice(0,10);
				return date_to_return;
			}
			else{
				return given_date;
			}
		}
	}

	Object.defineProperties(Vue.prototype, {
		$elementHelper: {
			get: () => {
				return Vue.elementHelper
			}
		}
	})
}