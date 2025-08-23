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
});
