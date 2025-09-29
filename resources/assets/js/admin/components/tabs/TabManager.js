/**
 * Class for managing tab functionality
 * @class TabManager
 * @description Manages tab functionality for admin interface
 */
export default class TabManager {
	init() {
		const tabButtons = document.querySelector('.tab-buttons');
		if (!tabButtons) return;

		tabButtons.addEventListener('click', e => {
			if (!e.target.matches('.tab-button')) return;

			document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
			document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

			e.target.classList.add('active');
			document.getElementById(`${e.target.dataset.tab}-tab`).classList.remove('hidden');

			localStorage.setItem('activeTab', e.target.dataset.tab);
		});
	}
}
