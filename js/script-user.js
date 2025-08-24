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

    var pwd = document.getElementById('new_password');
    if (pwd) {
        var help = document.getElementById('passwordHelp');
        pwd.addEventListener('input', function(){
            var val = pwd.value;
            var strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;
            var msg = 'ضعیف', cls = 'text-danger';
            if (strength >= 3) { msg = 'قوی'; cls = 'text-success'; }
            else if (strength === 2) { msg = 'متوسط'; cls = 'text-warning'; }
            help.textContent = 'قدرت رمز: ' + msg;
            help.className = 'form-text ' + cls;
        });
    }

    document.querySelectorAll('.star-rating').forEach(function(container){
        var input = container.querySelector('input[name="rating"]');
        var current = parseInt(container.getAttribute('data-current')) || 0;
        var stars = container.querySelectorAll('i');
        setStars(current);
        stars.forEach(function(star){
            star.addEventListener('mouseover', function(){ setStars(star.dataset.value); });
            star.addEventListener('mouseout', function(){ setStars(current); });
            star.addEventListener('click', function(){ current = star.dataset.value; input.value = current; setStars(current); });
        });
        function setStars(val){
            stars.forEach(function(s){
                s.classList.toggle('bi-star-fill', s.dataset.value <= val);
                s.classList.toggle('bi-star', s.dataset.value > val);
            });
        }
    });
});

