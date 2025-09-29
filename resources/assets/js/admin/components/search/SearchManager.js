/**
 * Class for managing search functionality
 * @class SearchManager
 * @description Manages search functionality for admin interface
 */
export default class SearchManager {
	init() {
		document.body.addEventListener('input', e => {
			if (e.target.matches('.search-input')) {
				this.searchList(e.target.dataset.type, e.target.value.toLowerCase());
			}
		});
	}

	searchList(type, term) {
		document.querySelectorAll(`#${type}-tab ul li`).forEach(item => {
			item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
		});
	}
}
