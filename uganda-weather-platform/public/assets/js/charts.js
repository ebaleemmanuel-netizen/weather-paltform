// Chart.js rendering for analytics dashboards
document.addEventListener('DOMContentLoaded', function () {
  const scope = typeof ANALYTICS_SCOPE !== 'undefined' ? ANALYTICS_SCOPE : 'mine';

  fetch(BASE_URL + '/api/analytics.php?scope=' + scope)
    .then(res => res.json())
    .then(data => {
      // Outcome pie chart
      const outcomeLabels = data.outcome.map(o => o.outcome === 'good' ? 'Good' : 'Bad');
      const outcomeValues = data.outcome.map(o => o.cnt);
      new Chart(document.getElementById('outcomeChart'), {
        type: 'pie',
        data: { labels: outcomeLabels, datasets: [{ data: outcomeValues, backgroundColor: ['#2e7d32', '#c62828'] }] }
      });

      // Top failure reasons bar chart
      new Chart(document.getElementById('reasonsChart'), {
        type: 'bar',
        data: {
          labels: data.top_failure_reasons.map(r => r.label),
          datasets: [{ label: 'Occurrences', data: data.top_failure_reasons.map(r => r.cnt), backgroundColor: '#ef6c00' }]
        },
        options: { plugins: { legend: { display: false } } }
      });

      // Crop performance stacked bar chart
      const cropNames = [...new Set(data.by_crop.map(r => r.name))];
      const goodData = cropNames.map(name => (data.by_crop.find(r => r.name === name && r.outcome === 'good') || {}).cnt || 0);
      const badData = cropNames.map(name => (data.by_crop.find(r => r.name === name && r.outcome === 'bad') || {}).cnt || 0);
      new Chart(document.getElementById('cropChart'), {
        type: 'bar',
        data: {
          labels: cropNames,
          datasets: [
            { label: 'Good', data: goodData, backgroundColor: '#2e7d32' },
            { label: 'Bad', data: badData, backgroundColor: '#c62828' }
          ]
        },
        options: { scales: { x: { stacked: true }, y: { stacked: true } } }
      });
    })
    .catch(err => console.error('Analytics fetch failed:', err));
});
