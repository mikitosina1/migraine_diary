import axios from 'axios';

/**
 * Handles application translations and internationalization
 */
class TranslationService {
	constructor() {
		this.translations = {};
		this.loaded = false;
		this.endpoint = '/migraine-diary/translations';
	}

	/**
	 * Load translations from server
	 * @returns {Promise<Object>} - Translations object
	 */
	async load(apiService) {
		if (this.loaded) return this.translations;

		try {
			const response = await axios.get(this.endpoint);

			if (response.data?.success && response.data.translations) {
				this.translations = response.data.translations;
				this.loaded = true;
				console.log('Translations loaded successfully');
			} else {
				console.warn('Unexpected response format for translations');
			}
		} catch (error) {
			console.error('Failed to load translations:', error);
		}

		return this.translations;
	}

	/**
	 * Get translation for a key with fallback
	 * @param {string} key - Translation key
	 * @param {string} fallback - Fallback text if key not found
	 * @returns {string} - Translated text
	 */
	translate(key, fallback = '') {
		return this.translations[key] || fallback || key;
	}

	/**
	 * Check if translations are loaded
	 * @returns {boolean}
	 */
	isLoaded() {
		return this.loaded;
	}

	/**
	 * Add translation dynamically
	 * @param {string} key
	 * @param {string} value
	 */
	addTranslation(key, value) {
		this.translations[key] = value;
	}

	/**
	 * Get all translations
	 * @returns {Object}
	 */
	getAll() {
		return { ...this.translations };
	}
}

export default TranslationService;
