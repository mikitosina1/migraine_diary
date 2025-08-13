import axios from 'axios';
import $ from 'jquery';

window.$ = $;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {
	const modal = document.getElementById('editModal');
	const itemType = document.getElementById('itemType');
	const itemId = document.getElementById('itemId');
	const itemCode = document.getElementById('itemCode');
	const codeError = document.getElementById('codeError');
	const modalTitle = document.getElementById('modalTitle');
	const modalSaveBtn = document.getElementById('modalSaveBtn');

	// helpers
	const openModal = async (type, id = '') => {
		itemType.value = type;
		itemId.value = id;

		if (id) {
			modalTitle.textContent = `${modalTitle.dataset.edit} (${type})`;
			modalSaveBtn.textContent = modalSaveBtn.dataset.update;

			try {
				const {data} = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`);

				document.getElementById('itemCode').value = data.code || '';
				document.querySelectorAll('[id^="name_"]').forEach(input => input.value = '');

				(data.translations || []).forEach(t => {
					const input = document.getElementById(`name_${t.locale}`);
					if (input) {
						input.value = t.name || '';
					}
				});

			} catch (err) {
				console.error('Error loading data:', err);
				alert('Can not load data. Please try again later.');
				return;
			}

		} else {
			modalTitle.textContent = `${modalTitle.dataset.add} (${type})`;
			modalSaveBtn.textContent = modalSaveBtn.dataset.save;

			// empty form
			itemId.value = '';
			document.getElementById('itemCode').value = '';
			document.querySelectorAll('[id^="name_"]').forEach(input => input.value = '');
		}

		modal.classList.remove('hidden');
	};

	const closeModal = () => modal.classList.add('hidden');

	const searchList = (type, term) => {
		document.querySelectorAll(`#${type}-tab ul li`).forEach(item => {
			item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
		});
	};

	// tabs
	document.querySelector('.tab-buttons').addEventListener('click', e => {
		if (!e.target.matches('.tab-button')) return;

		document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
		document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

		e.target.classList.add('active');
		document.getElementById(`${e.target.dataset.tab}-tab`).classList.remove('hidden');
	});

	// actions
	document.body.addEventListener('click', e => {
		if (e.target.closest('.edit-item-btn')) {
			const btn = e.target.closest('.edit-item-btn');
			openModal(btn.dataset.type, btn.dataset.id);
		}
		if (e.target.closest('.add-item-btn')) {
			openModal(e.target.closest('.add-item-btn').dataset.type);
		}
		if (e.target.closest('.modal-close')) {
			closeModal();
		}
	});

	// search
	document.body.addEventListener('input', e => {
		if (e.target.matches('.search-input')) {
			searchList(e.target.dataset.type, e.target.value.toLowerCase());
		}
	});

	// unique code check
	itemCode.addEventListener('blur', async () => {
		if (!itemCode.value) return;
		try {
			const { data } = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`, {
				params: { code: itemCode.value }
			});
			if (data.exists) {
				codeError.textContent = `Code already exists: ${data.item.name}`;
				codeError.classList.remove('hidden');
			} else {
				codeError.classList.add('hidden');
			}
		} catch (err) {
			console.error('Code check error:', err);
		}
	});

	// form submit
	document.getElementById('editForm').addEventListener('submit', async e => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const type = formData.get('type');
		const id = formData.get('id');

		try {
			let url;
			let method = 'post';

			if (id) {
				url = `/admin/migraine-diary/${type}/${id}/update`;
				formData.append('_method', 'PUT'); // Laravel spoof
			} else {
				url = `/admin/migraine-diary/${type}/store`;
			}

			await axios({
				method,
				url,
				data: formData
			});
			location.reload();
		} catch (err) {
			alert(`Save error: ${err.response?.statusText || err.message}`);
		}
	});


	$('.delete-item-btn').click(async function (e) {
		e.preventDefault();

		const id = $(this).data('id');
		const type = $(this).data('type');

		if (!confirm('Delete this element?')) return;

		try {
			await axios.delete(`/admin/migraine-diary/${type}/${id}`, {
				data: { _token: $('meta[name="csrf-token"]').attr('content') }
			});

			location.reload();
		} catch (err) {
			console.error('Delete error:', err);
			alert('Error deleting item. Please try again later.');
		}
	});
});
