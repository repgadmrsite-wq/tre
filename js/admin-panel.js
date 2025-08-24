document.addEventListener('DOMContentLoaded', function () {
  if (typeof dailyLabels !== 'undefined') {
    const ctxDaily = document.getElementById('dailyRevenueChart');
    if (ctxDaily) {
      new Chart(ctxDaily, {
        type: 'line',
        data: {
          labels: dailyLabels,
          datasets: [{
            label: 'درآمد روزانه',
            data: dailyRevenue,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)',
            tension: 0.3
          }]
        },
        options: {
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    }
  }

  if (typeof monthlyLabels !== 'undefined') {
    const ctxMonthly = document.getElementById('monthlyRevenueChart');
    if (ctxMonthly) {
      new Chart(ctxMonthly, {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'درآمد ماهانه',
            data: monthlyRevenue,
            backgroundColor: 'rgba(25,135,84,0.7)'
          }]
        },
        options: {
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    }
  }
});
