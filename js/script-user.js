const NESHAN_MAP_API_KEY = window.NESHAN_MAP_API_KEY || '';
const KISH_BOUNDS = [[26.4,53.8],[26.65,54.1]];
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') document.body.classList.add('dark-mode');

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
        var map = new L.Map('motorMap', {
            key: NESHAN_MAP_API_KEY,
            maptype: 'neshan',
            center: [26.5307, 53.9800],
            zoom: 12,
            maxBounds: KISH_BOUNDS
        });
        var activeIcon = L.divIcon({html:'<i class="bi bi-scooter text-success"></i>', className:'motor-marker', iconSize:[32,32], iconAnchor:[16,16]});
        var reservedIcon = L.divIcon({html:'<i class="bi bi-scooter text-secondary"></i>', className:'motor-marker', iconSize:[32,32], iconAnchor:[16,16]});
        var markers = [];
        mapMotors.forEach(function(m){
            if(m.lat && m.lng){
                var marker = L.marker([m.lat, m.lng], {icon: m.available==1 ? activeIcon : reservedIcon}).addTo(map).bindPopup('<strong>'+m.model+'</strong>');
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
                    miniMap = new L.Map('pickupMap', {
                        key: NESHAN_MAP_API_KEY,
                        maptype: 'neshan',
                        zoomControl:false,
                        center:[lat,lng],
                        zoom:14,
                        maxBounds: KISH_BOUNDS
                    });
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

    // floating photo upload button
    var fab = document.createElement('button');
    fab.type = 'button';
    fab.className = 'photo-fab btn btn-primary rounded-circle';
    fab.innerHTML = '<i class="bi bi-camera"></i>';
    document.body.appendChild(fab);

    var fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/jpeg,image/png';
    fileInput.style.display = 'none';
    document.body.appendChild(fileInput);

    fab.addEventListener('click', function(){
        fileInput.click();
    });

    fileInput.addEventListener('change', function(){
        if (!fileInput.files.length) return;
        var fd = new FormData();
        fd.append('photo', fileInput.files[0]);
        fetch('photo_upload.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(function(resp){ alert(resp.message || 'ارسال شد'); fileInput.value=''; })
            .catch(function(){ alert('خطا در ارسال تصویر'); });
    });

    var vehicleForm = document.getElementById('vehicle-filter');
    if (vehicleForm) {
        var saved = localStorage.getItem('vehicleFilters');
        if (saved && !location.search) {
            try {
                var f = JSON.parse(saved);
                Object.keys(f).forEach(function(k){ if(vehicleForm[k]) vehicleForm[k].value = f[k]; });
                ['min_range','max_range'].forEach(function(id){ var el=vehicleForm[id]; var out=document.getElementById(id+'Output'); if(el&&out){ out.textContent = el.value; el.addEventListener('input', function(){out.textContent=el.value;}); }});
                if (Object.values(f).some(v => v!=='')) vehicleForm.submit();
            } catch(e){}
        } else {
            ['min_range','max_range'].forEach(function(id){ var el=vehicleForm[id]; var out=document.getElementById(id+'Output'); if(el&&out){ out.textContent = el.value; el.addEventListener('input', function(){out.textContent=el.value;}); }});
        }
        vehicleForm.addEventListener('submit', function(){
            var data = {
                model: vehicleForm.model.value,
                availability: vehicleForm.availability.value,
                min_price: vehicleForm.min_price.value,
                max_price: vehicleForm.max_price.value,
                min_range: vehicleForm.min_range.value,
                max_range: vehicleForm.max_range.value
            };
            localStorage.setItem('vehicleFilters', JSON.stringify(data));
        });
    }
});
