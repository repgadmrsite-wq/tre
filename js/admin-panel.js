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
  // Toast notifications polling for admins
  const container = document.getElementById('admin-toast-container');
  const shown = new Set();

  function playBeep() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      osc.frequency.value = 880;
      osc.connect(ctx.destination);
      osc.start();
      setTimeout(() => { osc.stop(); ctx.close(); }, 200);
    } catch (e) {}
  }

  function showToast(msg, id) {
    if (!container) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'toast align-items-center text-bg-primary border-0';
    wrapper.setAttribute('role','alert');
    wrapper.setAttribute('aria-live','assertive');
    wrapper.setAttribute('aria-atomic','true');
    wrapper.innerHTML = '<div class="d-flex"><div class="toast-body">'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    container.appendChild(wrapper);
    const toast = new bootstrap.Toast(wrapper, {delay:5000});
    toast.show();
  }

  async function fetchNotes() {
    try {
      const res = await fetch('notifications.php?ajax=1');
      if (!res.ok) return;
      const data = await res.json();
      data.forEach(n => {
        if (!shown.has(n.id)) {
          shown.add(n.id);
          showToast(n.message, n.id);
          playBeep();
          fetch('notifications.php?read=' + n.id);
        }
      });
    } catch (e) {
      // ignore errors
    }
  }

  fetchNotes();
  setInterval(fetchNotes, 10000);
});
