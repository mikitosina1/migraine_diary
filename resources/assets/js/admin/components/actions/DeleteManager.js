/**
 * Class for managing delete operations
 * @class DeleteManager
 * @description Manages delete operations for admin interface
 */
export default class DeleteManager {
	init() {
		document.body.addEventListener('click', e => {
			const deleteBtn = e.target.closest('.delete-item-btn');
			if (deleteBtn) this.handleDelete(deleteBtn);
		});
	}

	async handleDelete(deleteBtn) {
		const id = deleteBtn.dataset.id;
		const type = deleteBtn.dataset.type;

		if (!confirm('Delete this element?')) return;

		try {
			await axios.delete(`/admin/migraine-diary/${type}/${id}`, {
				data: {_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
			});
			location.reload();
		} catch (err) {
			console.error('Delete error:', err);
			alert('Error deleting item. Please try again later.');
		}
	}
}
