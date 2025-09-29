import axios from 'axios';
import MigraineDiaryAppAdmin from "./MigraineDiaryAppAdmin.js";

window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {
	new MigraineDiaryAppAdmin();
});
