export default function(Vue) {
	Vue.elementHelper = {
		addOneDayToDate(given_date){
			if(given_date){
				let raw_date = new Date(given_date);
				raw_date.setDate(raw_date.getDate() + 1);
				let date_to_return = new Date(raw_date).toISOString().slice(0,10);
				return date_to_return;
			}
			else{
				return null;
			}
		},

		formatDate(given_date){
			if(given_date){
				var date = new Date(given_date);

				// (YEAR, MONTH, DATE(DAY), HOUR, MINUTE, SECONDS)
				let date_parts = [
					date.getFullYear(),
					date.getMonth() + 1,
					date.getDate(),
					
					date.getHours(),
					date.getMinutes(),
					date.getSeconds()
				];

				for(let counter = 0; counter < date_parts.length; counter++){
					if(date_parts[counter] < 10){
						date_parts[counter] = '0' + String(date_parts[counter]);
					}
					else{
						date_parts[counter] = String(date_parts[counter]);
					}
				}

				console.log("DATE PARTS: ", date_parts);


				var converted_date = date_parts[0] + '-' + date_parts[1] + '-' + date_parts[2] + ' ' + date_parts[3] + ':' + date_parts[4] + ':' + date_parts[5];

				// var converted_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());

				// console.log("CONVERTED DATE IS: ", converted_date);

				return converted_date;

    // 			date.setDate(date.getDate() + 1);
    // 			return date;

				// let raw_date = new Date(given_date);
				// raw_date.setDate(raw_date.getDate() + 1);
				// let date_to_return = new Date(raw_date).toISOString().slice(0,10);
				// return date_to_return;
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