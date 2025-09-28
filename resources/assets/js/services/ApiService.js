class ApiService {
	constructor() {
		this.setupAxios();
	}

	setupAxios() {
		window.axios.defaults.withCredentials = true;
		window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
	}

	async get(url) {
		try {
			return await window.axios.get(url);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async post(url, data) {
		try {
			return await window.axios.post(url, data);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async put(url, data) {
		try {
			return await window.axios.put(url, data);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async delete(url) {
		try {
			return await window.axios.delete(url);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	handleError(error) {
		console.error('API Error:', error);
		// TODO: log error to server
	}
}

export default ApiService;
