import axios from 'axios';

class ApiService {
	constructor() {
		this.setupAxios();
	}

	setupAxios() {
		axios.defaults.withCredentials = true;
		axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
	}

	async get(url) {
		try {
			return await axios.get(url);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async post(url, data) {
		try {
			return await axios.post(url, data);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async put(url, data) {
		try {
			return await axios.put(url, data);
		} catch (error) {
			this.handleError(error);
			throw error;
		}
	}

	async delete(url) {
		try {
			return await axios.delete(url);
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
