class UIManager {
	constructor(translationService) {
		this.translationService = translationService;
	}

	// Tab management
	handleTabClick(event) {
		const tab = event.target.dataset.tab;

		document.querySelectorAll('.tab-content').forEach(content => {
			content.classList.add('hidden');
		});

		document.getElementById(`tab-${tab}`)?.classList.remove('hidden');

		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		event.target.classList.add('active');
	}

	// Modal window control
	openModal(modalId, title = '') {
		const modal = document.getElementById(modalId);
		if (modal && title) {
			modal.querySelector('.attack-modal-title').innerHTML = title;
			modal.showModal();
		}
		return modal;
	}

	closeModal(modalId) {
		const modal = document.getElementById(modalId);
		if (modal) {
			modal.close();
		}
	}

	// refresh UI of attack
	updateAttackInUI(attackId, attackData) {
		const attackElement = this.getAttackElement(attackId);
		if (!attackElement) return;

		this.removeEndButton(attackElement);
		this.updateEndTime(attackElement, attackData);
		attackElement.classList.add('attack-ended');
		// TODO remove after testing
		console.log('Attack UI updated successfully:', attackId);
	}

	getAttackElement(attackId) {
		return document.querySelector(`.end-attack-button[data-attack-id="${attackId}"]`)
			?.closest('.migraine-list-item');
	}

	removeEndButton(attackElement) {
		const endButton = attackElement.querySelector('.end-attack-button');
		if (endButton) endButton.remove();
	}

	updateEndTime(attackElement, attackData) {
		const header = attackElement.querySelector('.statistic-header');
		const startTimeSpan = header.querySelector('span:first-child');

		if (startTimeSpan) {
			let endTimeSpan = header.querySelector('span:nth-child(2)') ||
				document.createElement('span');

			if (!endTimeSpan.parentNode) {
				startTimeSpan.parentNode.insertBefore(endTimeSpan, startTimeSpan.nextSibling);
			}

			endTimeSpan.innerHTML = `
				<strong>${this.translationService.translate('end_time')}:</strong>
				${attackData.end_time_formatted}
			`;
		}
	}

	removeAttackFromUI(attackId) {
		const attackElement = document.querySelector(`.delete-btn[data-attack-id="${attackId}"]`)
			?.closest('.migraine-list-item');

		if (!attackElement) return;

		attackElement.style.transition = 'opacity 0.3s ease';
		attackElement.style.opacity = '0';

		setTimeout(() => {
			attackElement.remove();
			this.checkEmptyList();
		}, 300);
	}

	checkEmptyList() {
		const listContainer = document.querySelector('.list');
		const hasItems = document.querySelectorAll('.migraine-list-item').length > 0;

		if (!hasItems && listContainer) {
			const message = this.translationService.translate('no_rec_found', 'No records found');
			listContainer.innerHTML = `<p class="text-center py-8">${message}</p>`;
		}
	}
}

export default UIManager;
