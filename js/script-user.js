document.addEventListener('DOMContentLoaded', function(){
    if (typeof paymentData !== 'undefined') {
        var labels = paymentData.map(function(i){return i.period;});
        var data = paymentData.map(function(i){return i.total;});
        var ctx = document.getElementById('paymentsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'مبلغ پرداختی (تومان)', data: data, backgroundColor: 'rgba(54,162,235,0.5)', borderColor: 'rgb(54,162,235)', borderWidth: 1 }] },
                options: { scales: { y: { beginAtZero: true } } }
            });
        }
    }

    if (typeof revenueStats !== 'undefined') {
        var pie = document.getElementById('revenuePie');
        if (pie) {
            new Chart(pie, {
                type: 'pie',
                data: {
                    labels: ['روزانه','ماهانه','سالانه'],
                    datasets: [{ data: [revenueStats.daily, revenueStats.monthly, revenueStats.yearly], backgroundColor: ['#4caf50','#2196f3','#ff9800'] }]
                }
            });
        }
    }

    if (typeof financeLabels !== 'undefined') {
        var fctx = document.getElementById('financeChart');
        if (fctx) {
            new Chart(fctx, {
                data: {
                    labels: financeLabels,
                    datasets: [
                        { type: 'bar', label: 'این ماه', data: financeCurrent, backgroundColor: 'rgba(54,162,235,0.5)', borderColor: 'rgb(54,162,235)', borderWidth: 1 },
                        { type: 'line', label: 'ماه قبل', data: financePrev, borderColor: 'rgb(255,99,132)', tension: 0.3, fill: false }
                    ]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        }
    }

    if (typeof upcomingCount !== 'undefined' && upcomingCount > 0) {
        var modalEl = document.getElementById('reminderModal');
        if (modalEl) {
            var reminderModal = new bootstrap.Modal(modalEl);
            reminderModal.show();
        }
    }
});

