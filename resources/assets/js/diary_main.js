import axios from 'axios';
import $ from 'jquery';

window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// modal init
const modal = document.getElementById('migraineModal');
window.migraineModal = modal;

// close button
modal.querySelector('.modal-close').addEventListener('click', () => {
	modal.close();
});

// close modal on click outside
modal.addEventListener('click', (e) => {
	if (e.target === modal) {
		modal.close();
	}
});
