import Chart from 'chart.js/auto';

export default class ChartManager {
	constructor() {
		this.chart = null;
	}

	initFrequencyChart(data) {
		const ctx = document.getElementById('migraineFrequencyChart').getContext('2d');

		this.chart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: data.labels,
				datasets: [{
					label: 'Count of Attacks',
					data: data.values,
					backgroundColor: 'rgba(59, 130, 246, 0.5)',
					borderColor: 'rgba(59, 130, 246, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});
	}

	updateChart(newData) {
		if (this.chart) {
			this.chart.data.labels = newData.labels;
			this.chart.data.datasets[0].data = newData.values;
			this.chart.update();
		}
	}

	destroy() {
		if (this.chart) {
			this.chart.destroy();
			this.chart = null;
		}
	}
}
