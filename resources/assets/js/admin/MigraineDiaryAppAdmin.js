import axios from 'axios';
import ModalManager from './components/modals/ModalManager.js';
import TabManager from './components/tabs/TabManager.js';
import SearchManager from './components/search/SearchManager.js';
import DeleteManager from './components/actions/DeleteManager.js';

// Config axios
window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Main application class for admin interface
 *
 * @TODO: Add error handling, translations, including added classes
 * @class MigraineDiaryAppAdmin
 * @description Manages all admin-related functionality
 */
export default class MigraineDiaryAppAdmin
{
	constructor()
	{
		this.modalManager = new ModalManager();
		this.tabManager = new TabManager();
		this.searchManager = new SearchManager();
		this.deleteManager = new DeleteManager();
		this.init();
	}

	init()
	{
		this.tabManager.init();
		this.modalManager.init();
		this.searchManager.init();
		this.deleteManager.init();

		// Restore active tab
		const activeTab = localStorage.getItem('activeTab');
		if (activeTab) {
			document.querySelector(`.tab-button[data-tab="${activeTab}"]`)?.click();
		}
	}
}
