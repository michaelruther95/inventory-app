export default {
	state: {
		loading_message: 'Page is loading. Please wait...',
		is_loading: true,
		user_info: JSON.parse(localStorage.getItem('user_info')),
		token: localStorage.getItem('token')
	},

	mutations: {
		loaderMutator(state, payload){
			state.loading_message = payload.message;
			state.is_loading = payload.display;
		},

		authTokenMutator(state, payload){
			state.token = payload;
		},

		userInfoMutator(state, payload){
			state.user_info = payload;
		}
	},
	getters: {
		getLoadingMessage(state){
			return state.loading_message;
		},

		isLoading(state){
			return state.is_loading;
		},

		authToken(state){
			return state.token;
		},

		userInfo(state){
			return state.user_info;
		}
	},
	actions: {
		async pageLoader({ commit }, payload){
			commit('loaderMutator', payload);
		},

		async setToken({ commit }, payload){
			commit('authTokenMutator', payload);
		},

		async setUserInfo({ commit }, payload){
			commit('userInfoMutator', payload);
		}
	}
}