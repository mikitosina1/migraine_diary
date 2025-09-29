/**
 * Class for managing modal operations
 * @class ModalManager
 * @description Manages modal operations for admin interface
 */
export default class ModalManager {
	constructor() {
		this.modal = document.getElementById('editModal');
		this.itemType = document.getElementById('itemType');
		this.itemId = document.getElementById('itemId');
		this.itemCode = document.getElementById('itemCode');
		this.codeError = document.getElementById('codeError');
		this.modalTitle = document.getElementById('modalTitle');
		this.modalSaveBtn = document.getElementById('modalSaveBtn');
		this.editForm = document.getElementById('editForm');
	}

	init() {
		if (!this.modal) return;

		document.body.addEventListener('click', e => {
			const editBtn = e.target.closest('.edit-item-btn');
			const addBtn = e.target.closest('.add-item-btn');
			const closeBtn = e.target.closest('.modal-close');

			if (editBtn) this.openModal(editBtn.dataset.type, editBtn.dataset.id);
			if (addBtn) this.openModal(addBtn.dataset.type);
			if (closeBtn) this.closeModal();
		});

		this.itemCode?.addEventListener('blur', () => this.checkUniqueCode());
		this.editForm?.addEventListener('submit', e => this.submitForm(e));
	}

	async openModal(type, id = '') {
		this.itemType.value = type;
		this.itemId.value = id;

		if (id) {
			this.modalTitle.textContent = `${this.modalTitle.dataset.edit} (${type})`;
			this.modalSaveBtn.textContent = this.modalSaveBtn.dataset.update;

			try {
				const { data } = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`);
				this.itemCode.value = data.code || '';

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
			this.modalTitle.textContent = `${this.modalTitle.dataset.add} (${type})`;
			this.modalSaveBtn.textContent = this.modalSaveBtn.dataset.save;
			this.itemId.value = '';
			this.itemCode.value = '';
			document.querySelectorAll('[id^="name_"]').forEach(input => input.value = '');
		}

		this.modal.classList.remove('hidden');
	}

	closeModal() {
		this.modal.classList.add('hidden');
	}

	async checkUniqueCode() {
		const type = this.itemType.value;
		const id = this.itemId.value;
		if (!this.itemCode.value) return;

		try {
			const { data } = await axios.get(`/admin/migraine-diary/${type}/${id}/edit`, {
				params: { code: this.itemCode.value }
			});

			if (data.exists) {
				this.codeError.textContent = `Code already exists: ${data.item.name}`;
				this.codeError.classList.remove('hidden');
			} else {
				this.codeError.classList.add('hidden');
			}
		} catch (err) {
			console.error('Code check error:', err);
		}
	}

	async submitForm(e) {
		e.preventDefault();
		const formData = new FormData(this.editForm);
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
	}
}
