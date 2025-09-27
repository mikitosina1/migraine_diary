import axios from 'axios';

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
	const editForm = document.getElementById('editForm');

	// Tab buttons
	document.querySelectorAll('.tab-button').forEach(btn => {
		btn.addEventListener('click', e => {
			localStorage.setItem('activeTab', e.target.dataset.tab);
		});
	});

	// Open modal
	const openModal = async (type, id = '') => {
		itemType.value = type;
		itemId.value = id;

		if (id) {
			modalTitle.textContent = `${modalTitle.dataset.edit} (${type})`;
			modalSaveBtn.textContent = modalSaveBtn.dataset.update;

			try {
				const {data} = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`);
				itemCode.value = data.code || '';

				document.querySelectorAll('[id^="name_"]').forEach(input => input.value = '');
				(data.translations || []).forEach(t => {
					const input = document.getElementById(`name_${t.locale}`);
					if (input) input.value = t.name || '';
				});
			} catch (err) {
				console.error('Error loading data:', err);
				alert('Cannot load data. Please try again later.');
				return;
			}
		} else {
			modalTitle.textContent = `${modalTitle.dataset.add} (${type})`;
			modalSaveBtn.textContent = modalSaveBtn.dataset.save;
			itemId.value = '';
			itemCode.value = '';
			document.querySelectorAll('[id^="name_"]').forEach(input => input.value = '');
		}

		modal.classList.remove('hidden');
	};

	const closeModal = () => modal.classList.add('hidden');

	// Search
	const searchList = (type, term) => {
		document.querySelectorAll(`#${type}-tab ul li`).forEach(item => {
			item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
		});
	};

	// Tab switch
	document.querySelector('.tab-buttons').addEventListener('click', e => {
		if (!e.target.matches('.tab-button')) return;

		document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
		document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

		e.target.classList.add('active');
		document.getElementById(`${e.target.dataset.tab}-tab`).classList.remove('hidden');
	});

	// Delegated actions
	document.body.addEventListener('click', e => {
		const editBtn = e.target.closest('.edit-item-btn');
		const addBtn = e.target.closest('.add-item-btn');
		const closeBtn = e.target.closest('.modal-close');

		if (editBtn) openModal(editBtn.dataset.type, editBtn.dataset.id);
		if (addBtn) openModal(addBtn.dataset.type);
		if (closeBtn) closeModal();

		// Delete button
		const deleteBtn = e.target.closest('.delete-item-btn');
		if (deleteBtn) {
			e.preventDefault();
			const id = deleteBtn.dataset.id;
			const type = deleteBtn.dataset.type;
			if (!confirm('Delete this element?')) return;

			axios.delete(`/admin/migraine-diary/${type}/${id}`, {
				data: {_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
			})
				.then(() => location.reload())
				.catch(err => {
					console.error('Delete error:', err);
					alert('Error deleting item. Please try again later.');
				});
		}
	});

	// Search input
	document.body.addEventListener('input', e => {
		if (e.target.matches('.search-input')) {
			searchList(e.target.dataset.type, e.target.value.toLowerCase());
		}
	});

	// Unique code check
	itemCode.addEventListener('blur', async () => {
		const type = itemType.value;
		const id = itemId.value;
		if (!itemCode.value) return;

		try {
			/**
			 * @type {{ exists?: boolean, item?: { name?: string } }}
			 */
			const {data} = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`, {
				params: {code: itemCode.value}
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

	// Form submit
	editForm.addEventListener('submit', async e => {
		e.preventDefault();
		const formData = new FormData(editForm);
		const type = formData.get('type');
		const id = formData.get('id');

		try {
			let url = id
				? `/admin/migraine-diary/${type}/${id}/update`
				: `/admin/migraine-diary/${type}/store`;

			if (id) formData.append('_method', 'PUT');

			await axios.post(url, formData);
			location.reload();
		} catch (err) {
			alert(`Save error: ${err.response?.statusText || err.message}`);
		}
	});

	// Restore active tab
	const activeTab = localStorage.getItem('activeTab');
	if (activeTab) {
		document.querySelector(`.tab-button[data-tab="${activeTab}"]`)?.click();
	}
});
