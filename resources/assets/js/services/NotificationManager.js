/**
 * Manages application notifications and alerts
 */
class NotificationManager {
	constructor() {
		this.notifications = [];
		this.defaultDuration = 3000;
	}

	/**
	 * Show notification message
	 * @param {string} message - Message text
	 * @param {string} type - Notification type (success|error|warning|info)
	 * @param {number} duration - Display duration in ms
	 * @returns {HTMLElement} - Created a notification element
	 */
	show(message, type = 'success', duration = this.defaultDuration) {
		const notification = this.createNotificationElement(message, type);

		const mainElement = document.querySelector('main');
		if (mainElement) {
			mainElement.insertBefore(notification, mainElement.firstChild);
		} else {
			// fallback on the body
			document.body.appendChild(notification);
		}

		this.notifications.push(notification);

		if (duration > 0) {
			setTimeout(() => this.remove(notification), duration);
		}

		return notification;
	}

	/**
	 * Create notification DOM element
	 * @param {string} message
	 * @param {string} type
	 * @returns {HTMLElement}
	 */
	createNotificationElement(message, type) {
		const notification = document.createElement('div');
		notification.className = `notification-block border rounded text-white z-50`;

		this.applyStyles(notification, type);

		notification.textContent = message;

		// Add a close button
		const closeBtn = document.createElement('button');
		closeBtn.className = 'ml-4 text-white hover:text-gray-200';
		closeBtn.innerHTML = '<i class="fa-solid fa-x"></i>';
		closeBtn.onclick = () => this.remove(notification);

		notification.appendChild(closeBtn);

		return notification;
	}

	/**
	 * Remove specific notification
	 * @param {HTMLElement} notification
	 */
	remove(notification) {
		if (notification && notification.parentNode) {
			notification.style.opacity = '0';

			setTimeout(() => {
				notification.remove();
				this.notifications = this.notifications.filter(n => n !== notification);
			}, 300);
		}
	}

	/**
	 * Clear all notifications
	 */
	clear() {
		this.notifications.forEach(notification => this.remove(notification));
	}

	/**
	 * Apply styles to a notification element
	 * @param {HTMLElement} notification
	 * @param {string} type
	 *
	 * @returns {HTMLElement} notification - with styles
	 */
	applyStyles(notification, type) {
		notification.style.position = 'fixed';
		notification.style.top = '6em';
		notification.style.padding = '1em';
		notification.style.transition = 'opacity 0.3s ease-in-out';
		notification.style.backgroundColor = this.getBackgroundColor(type);

		return notification;
	}

	/**
	 * Get CSS background for a notification type
	 * @param {string} type
	 * @returns {string}
	 */
	getBackgroundColor(type) {
		const colors = {
			success: '#93e4c1',
			error: '#f95959',
			warning: '#f8f398',
			info: '#8dc6ff'
		};
		return colors[type] || colors.success;
	}
}

export default NotificationManager;
