import axios from 'axios';

export default function(Vue) {
	Vue.axios = {
		post(url, params, config = { headers: {} }){
			return new Promise(function(resolve, reject) {
				var token = localStorage.getItem('token');
				if(token){
					config['headers']['Authorization'] = 'Bearer ' + token;
				}

		        axios.post(url, params, config).then((response) => {
		        	console.log(response);
		        	resolve(response);
		        }).catch((errorResponse) => {
		        	var newErrorResponse = Vue.axios.axiosErrorResponse(errorResponse);
		        	reject(newErrorResponse);
		        });
		    });
		},

		get(url, params, config = { headers: {} }){
			return new Promise(function(resolve, reject) {
				var token = localStorage.getItem('token');
				if(token){
					config['headers']['Authorization'] = 'Bearer ' + token;
				}

				config['params'] = params;
				axios.get(url, config).then((response) => {
					console.log(response);
		        	resolve(response);
				}).catch(errorResponse => {
					var newErrorResponse = Vue.axios.axiosErrorResponse(errorResponse);
		        	reject(newErrorResponse);
				});
			});
		},

		put(url, params, config = { headers: {} }){
			return new Promise(function(resolve, reject) {
				var token = localStorage.getItem('token');
				if(token){
					config['headers']['Authorization'] = 'Bearer ' + token;
				}

		        axios.put(url, params, config).then((response) => {
		        	console.log(response);
		        	resolve(response);
		        }).catch((errorResponse) => {
		        	var newErrorResponse = Vue.axios.axiosErrorResponse(errorResponse);
		        	reject(newErrorResponse);
		        });
		    });
		},

		patch(url, params, config = { headers: {} }){
			return new Promise(function(resolve, reject) {
				var token = localStorage.getItem('token');
				if(token){
					config['headers']['Authorization'] = 'Bearer ' + token;
				}

		        axios.patch(url, params, config).then((response) => {
		        	console.log(response);
		        	resolve(response);
		        }).catch((errorResponse) => {
		        	var newErrorResponse = Vue.axios.axiosErrorResponse(errorResponse);
		        	reject(newErrorResponse);
		        });
		    });
		},

		delete(url, params, config = { headers: {} }){
			return new Promise(function(resolve, reject) {
				var token = localStorage.getItem('token');
				if(token){
					config['headers']['Authorization'] = 'Bearer ' + token;
				}

				config['params'] = params;
				axios.delete(url, config).then((response) => {
					console.log(response);
		        	resolve(response);
				}).catch(errorResponse => {
					var newErrorResponse = Vue.axios.axiosErrorResponse(errorResponse);
		        	reject(newErrorResponse);
				});
			});
		},

		axiosErrorResponse(errorResponse){
			if(errorResponse.response){
        		console.log(errorResponse.response);
        	}
        	else{
        		console.log(errorResponse);
        	}

        	return errorResponse;
		}
	}

	Object.defineProperties(Vue.prototype, {
		$axios: {
			get: () => {
				return Vue.axios
			}
		}
	})
}