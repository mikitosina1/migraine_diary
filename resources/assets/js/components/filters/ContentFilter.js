/**
 * Class responsible for filtering and dynamically updating content blocks.
 *
 * Example usage:
 * ```js
 * const listFilter = new ContentFilter({
 *   containerSelector: '.list',
 *   targetSelector: '.list',
 *   endpoint: '/migraine-diary',
 *   loadingMessage: 'Loading...',
 *   errorMessage: 'Error filtering list',
 *   translateFn: (key, fallback) => translations[key] || fallback
 * });
 *
 * listFilter.apply({ range: 'week', pain_level: 'high' });
 * ```
 *
 * @class
 */
class ContentFilter {
	/**
	 * Creates a new ContentFilter instance.
	 *
	 * @param {Object} config - Configuration options.
	 * @param {string} config.containerSelector - CSS selector for the container element to update.
	 * @param {string} config.targetSelector - CSS selector for the content block to extract from the response.
	 * @param {string} [config.endpoint='/migraine-diary'] - Endpoint URL to request data from.
	 * @param {Object} [config.params={}] - Default parameters for filtering.
	 * @param {string} [config.loadingMessage='Loading'] - Message shown while content is loading.
	 * @param {string} [config.errorMessage='Filtration error'] - Message shown if filtering fails.
	 * @param {function(string, string):string} [config.translateFn] - Optional translation function.
	 */
	constructor(config) {
		this.containerSelector = config.containerSelector;
		this.targetSelector = config.targetSelector;
		this.endpoint = config.endpoint || '/migraine-diary';
		this.defaultParams = config.params || {};
		this.params = config.params || {};
		this.loadingMessage = config.loadingMessage || 'Loading';
		this.errorMessage = config.errorMessage || 'Filtration error';
		this.translateFn = config.translateFn || ((key, fallback) => fallback);

		this.currentFilters = {
			range: 'month',
			pain_level: 'all',
			container: '.list'
		};
	}

	/**
	 * Shows a loading indicator inside the container.
	 *
	 * @returns {HTMLElement|null} The container element, or null if not found.
	 */
	showLoading() {
		const container = document.querySelector(this.containerSelector);
		if (container) {
			container.innerHTML = `<div class="text-center p-4 text-white">${this.loadingMessage}...</div>`;
		}
		return container;
	}

	/**
	 * Shows an error message inside the container.
	 *
	 * @returns {void}
	 */
	showError() {
		const container = document.querySelector(this.containerSelector);
		if (container) {
			container.innerHTML = `<div class="text-center p-4 text-red-500">${this.errorMessage}</div>`;
		}
	}

	/**
	 * Parses an HTML string and returns the target content element.
	 *
	 * @param {string} htmlContent - Raw HTML content from server.
	 * @returns {HTMLElement|null} Parsed content element or null if not found.
	 */
	parseHtmlContent(htmlContent) {
		if (!htmlContent.startsWith('<!DOCTYPE html>')) {
			htmlContent = '<!DOCTYPE html>\n' + htmlContent;
		}

		const parser = new DOMParser();
		const doc = parser.parseFromString(htmlContent, 'text/html');
		return doc.querySelector(this.targetSelector);
	}

	/**
	 * Updates the container's content with new HTML.
	 *
	 * @param {HTMLElement|null} newContent - The new content element to display.
	 * @param {HTMLElement} container - The container element to update.
	 * @returns {boolean} True if the content was successfully updated, false otherwise.
	 */
	updateContent(newContent, container) {
		if (container && newContent) {
			container.innerHTML = newContent.innerHTML;
			return true;
		}
		return false;
	}

	/**
	 * Updates a specific filter value.
	 *
	 * @param {string} filterName - Name of the filter (e.g., 'range', 'pain_level').
	 * @param {string} value - New filter value.
	 * @returns {ContentFilter} Returns the current instance for chaining.
	 */
	setFilter(filterName, value) {
		this.currentFilters[filterName] = value;
		return this;
	}

	/**
	 * Returns a merged object of default and current filters.
	 *
	 * @returns {Object} Active filters object.
	 */
	getFilters() {
		return {...this.defaultParams, ...this.currentFilters};
	}

	/**
	 * Applies filtering and updates the content.
	 *
	 * Fetches filtered data from the server and replaces the target container content.
	 *
	 * @async
	 * @param {Object} [filters=null] - Optional filters to override current ones.
	 * @returns {Promise<void>}
	 */
	async apply(filters = null) {
		if (filters) {
			// Update specific filters if provided
			Object.keys(filters).forEach(key => {
				if (filters[key] !== undefined) {
					this.currentFilters[key] = filters[key];
				}
			});
		}

		const container = this.showLoading();
		if (!container) return;

		try {
			const response = await window.axios.get(this.endpoint, {
				params: this.getFilters()
			});

			const newContent = this.parseHtmlContent(response.data);
			const success = this.updateContent(newContent, container);

			if (!success) {
				console.warn(`Target selector "${this.targetSelector}" not found in response`);
				this.showError();
			}

		} catch (error) {
			console.error('Filtration error:', error);
			this.showError();
		}
	}
}

export default ContentFilter;
