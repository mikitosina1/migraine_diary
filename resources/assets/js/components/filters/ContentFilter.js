import axios from 'axios';

/**
 * Class for content filtering
 */
class ContentFilter {
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
			pain_level: 'all'
		};
	}

	// Show loading indicator
	showLoading() {
		const container = document.querySelector(this.containerSelector);
		if (container) {
			container.innerHTML = `<div class="text-center p-4 text-white">${this.loadingMessage}...</div>`;
		}
		return container;
	}

	// Show error message
	showError() {
		const container = document.querySelector(this.containerSelector);
		if (container) {
			container.innerHTML = `<div class="text-center p-4 text-red-500">${this.errorMessage}</div>`;
		}
	}

	// Parse HTML content
	parseHtmlContent(htmlContent) {
		if (!htmlContent.startsWith('<!DOCTYPE html>')) {
			htmlContent = '<!DOCTYPE html>\n' + htmlContent;
		}

		const parser = new DOMParser();
		const doc = parser.parseFromString(htmlContent, 'text/html');
		return doc.querySelector(this.targetSelector);
	}

	// Refresh content
	updateContent(newContent, container) {
		if (container && newContent) {
			container.innerHTML = newContent.innerHTML;
			return true;
		}
		return false;
	}

	// Update specific filter value
	setFilter(filterName, value) {
		this.currentFilters[filterName] = value;
		return this;
	}

	// Get all current filters
	getFilters() {
		return {...this.defaultParams, ...this.currentFilters};
	}

	// Main method to apply filtering
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
			const response = await axios.get(this.endpoint, {
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
