document.addEventListener('DOMContentLoaded', function(){
    fetch('notifications.php?ajax=1')
        .then(res => res.json())
        .then(list => {
            list.forEach(function(n){
                showUserToast(n.message);
                fetch('notifications.php?read=' + n.id);
            });
        });
    function showUserToast(msg){
        var container=document.getElementById('toast-container');
        if(!container){
            container=document.createElement('div');
            container.id='toast-container';
            container.className='toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        var toastEl=document.createElement('div');
        toastEl.className='toast align-items-center text-bg-dark border-0';
        toastEl.innerHTML='<div class="d-flex"><div class="toast-body"><i class="bi bi-bell me-2"></i>'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(toastEl);
        new bootstrap.Toast(toastEl,{delay:5000}).show();
    }
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

    if (typeof historyLabels !== 'undefined') {
        var hs = document.getElementById('historyStatusChart');
        if (hs) {
            new Chart(hs, {
                type: 'doughnut',
                data: {
                    labels: historyLabels,
                    datasets: [{ data: historyData, backgroundColor: ['#4caf50','#f44336','#2196f3','#ff9800'] }]
                }
            });
        }
    }
    if (typeof historyMonths !== 'undefined') {
        var hm = document.getElementById('historyMonthChart');
        if (hm) {
            new Chart(hm, {
                type: 'bar',
                data: {
                    labels: historyMonths,
                    datasets: [{ label: 'رزروها', data: historyCounts, backgroundColor: 'rgba(54,162,235,0.5)', borderColor: 'rgb(54,162,235)', borderWidth: 1 }]
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

    if (typeof mapMotors !== 'undefined') {
        var map = L.map('motorMap').setView([26.5307, 53.9800], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        var greenIcon = new L.Icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png', iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34], shadowSize:[41,41]});
        var redIcon = new L.Icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png', iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34], shadowSize:[41,41]});
        var markers = [];
        mapMotors.forEach(function(m){
            if(m.lat && m.lng){
                var marker = L.marker([m.lat, m.lng], {icon: m.available==1 ? greenIcon : redIcon}).addTo(map).bindPopup('<strong>'+m.model+'</strong>');
                markers.push({marker: marker, available: m.available==1});
            }
        });
        function applyFilter(){
            var showAvail = document.getElementById('filterAvailable').checked;
            var showRes = document.getElementById('filterReserved').checked;
            markers.forEach(function(obj){
                var show = (obj.available && showAvail) || (!obj.available && showRes);
                if(show){ map.addLayer(obj.marker);} else { map.removeLayer(obj.marker);} });
        }
        document.getElementById('filterAvailable').addEventListener('change', applyFilter);
        document.getElementById('filterReserved').addEventListener('change', applyFilter);
    }

    var locInput = document.getElementById('locationSearch');
    if (locInput) {
        var miniMap;
        locInput.addEventListener('change', function(){
            var opt = Array.from(document.getElementById('locationList').options).find(o => o.value === this.value);
            if(opt){
                var lat = parseFloat(opt.dataset.lat), lng = parseFloat(opt.dataset.lng);
                if(!miniMap){
                    miniMap = L.map('pickupMap', {zoomControl:false}).setView([lat,lng], 14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(miniMap);
                } else {
                    miniMap.setView([lat,lng],14);
                }
                L.marker([lat,lng]).addTo(miniMap);
            }
        });
    }

    var cancelForm = document.getElementById('cancelForm');
    if (cancelForm) {
        document.querySelectorAll('.btn-cancel-booking').forEach(function(btn){
            btn.addEventListener('click', function(){
                if (confirm('از لغو این رزرو مطمئنید؟')) {
                    cancelForm.booking_id.value = this.dataset.id;
                    cancelForm.submit();
                }
            });
        });
        document.querySelectorAll('.btn-change-date').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.getElementById('editBookingId').value = this.dataset.id;
                document.getElementById('editStart').value = this.dataset.start;
                document.getElementById('editEnd').value = this.dataset.end;
            });
        });
    }
});
