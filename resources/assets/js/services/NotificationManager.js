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

		document.body.appendChild(notification);
		this.notifications.push(notification);

		// Auto-remove after duration
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
		const bgClass = this.getBackgroundClass(type);

		notification.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 transition-opacity duration-300 ${bgClass}`;
		notification.textContent = message;

		// Add a close button
		const closeBtn = document.createElement('button');
		closeBtn.className = 'ml-4 text-white hover:text-gray-200';
		closeBtn.innerHTML = '&times;';
		closeBtn.onclick = () => this.remove(notification);

		notification.appendChild(closeBtn);

		return notification;
	}

	/**
	 * Get CSS background class for a notification type
	 * @param {string} type
	 * @returns {string}
	 */
	getBackgroundClass(type) {
		const classes = {
			success: 'bg-green-500',
			error: 'bg-red-500',
			warning: 'bg-yellow-500',
			info: 'bg-blue-500'
		};
		return classes[type] || classes.success;
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
}

export default NotificationManager;
