// Simple client-side data management for demo purposes

function initData() {
  if (!localStorage.getItem('users')) {
    const defaultUsers = [
      { id: 1, name: 'کاربر نمونه', email: 'user@example.com', password: 'user123', role: 'user' },
      { id: 2, name: 'مدیر', email: 'admin@example.com', password: 'admin123', role: 'admin' },
    ];
    localStorage.setItem('users', JSON.stringify(defaultUsers));
  }
  if (!localStorage.getItem('motorcycles')) {
    const defaultMotors = [
      { id: 1, name: 'اسکوتر وسپا', pricePerDay: 120000 },
      { id: 2, name: 'موتور کروزر', pricePerDay: 180000 },
      { id: 3, name: 'موتور برقی', pricePerDay: 90000 },
    ];
    localStorage.setItem('motorcycles', JSON.stringify(defaultMotors));
  }
  if (!localStorage.getItem('bookings')) {
    localStorage.setItem('bookings', JSON.stringify([]));
  }
}

function getLoggedInUser() {
  return JSON.parse(localStorage.getItem('loggedInUser'));
}

function setLoggedInUser(user) {
  localStorage.setItem('loggedInUser', JSON.stringify(user));
}

function logout() {
  localStorage.removeItem('loggedInUser');
  window.location.href = 'login.php';
}

function setupLogin() {
  const form = document.getElementById('loginForm');
  if (!form) return;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const users = JSON.parse(localStorage.getItem('users'));
    const user = users.find(u => u.email === email && u.password === password);
    if (user) {
      setLoggedInUser(user);
      window.location.href = user.role === 'admin' ? 'admin.php' : 'dashboard.html';
    } else {
      alert('اطلاعات ورود نادرست است');
    }
  });
}

function loadUserDashboard() {
  const user = getLoggedInUser();
  if (!user || user.role !== 'user') {
    window.location.href = 'login.php';
    return;
  }
  const welcomeSpan = document.getElementById('welcomeUser');
  if (welcomeSpan) welcomeSpan.textContent = user.name || user.email;
  renderMotorList(user);
  renderUserBookings(user);
  updateUserStats(user);
}

function renderMotorList(user) {
  const motors = JSON.parse(localStorage.getItem('motorcycles'));
  const container = document.getElementById('motorList');
  if (!container) return;
  container.innerHTML = '';
  motors.forEach(m => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${m.name}</td>
      <td>${m.pricePerDay.toLocaleString()} تومان</td>
      <td class="text-end"><button class="btn btn-sm btn-primary" data-id="${m.id}">رزرو</button></td>
    `;
    container.appendChild(row);
  });
  container.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => bookMotorcycle(user, parseInt(btn.dataset.id)));
  });
}

function bookMotorcycle(user, motoId) {
  const start = prompt('تاریخ شروع (مثلاً 1403/05/01):');
  const end = prompt('تاریخ پایان (مثلاً 1403/05/03):');
  if (!start || !end) return;
  const motors = JSON.parse(localStorage.getItem('motorcycles'));
  const moto = motors.find(m => m.id === motoId);
  const bookings = JSON.parse(localStorage.getItem('bookings'));
  const newBooking = {
    id: Date.now(),
    userEmail: user.email,
    motoId: moto.id,
    motoName: moto.name,
    startDate: start,
    endDate: end,
    amount: moto.pricePerDay,
    status: 'pending'
  };
  bookings.push(newBooking);
  localStorage.setItem('bookings', JSON.stringify(bookings));
  renderUserBookings(user);
  updateUserStats(user);
}

function renderUserBookings(user) {
  const bookings = JSON.parse(localStorage.getItem('bookings')).filter(b => b.userEmail === user.email);
  const tbody = document.getElementById('bookingsTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  bookings.forEach(b => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${b.motoName}</td>
      <td>${b.startDate}</td>
      <td>${b.endDate}</td>
      <td>${renderStatusBadge(b.status)}</td>
      <td class="text-end">${b.amount.toLocaleString()} تومان</td>
    `;
    tbody.appendChild(tr);
  });
}

function updateUserStats(user) {
  const bookings = JSON.parse(localStorage.getItem('bookings')).filter(b => b.userEmail === user.email);
  document.getElementById('totalBookings').textContent = bookings.length;
  const completed = bookings.filter(b => b.status === 'completed');
  document.getElementById('completedTrips').textContent = completed.length;
  const totalPaid = completed.reduce((sum, b) => sum + b.amount, 0);
  document.getElementById('totalPaid').textContent = totalPaid.toLocaleString() + ' تومان';
}

function loadAdminDashboard() {
  const user = getLoggedInUser();
  if (!user || user.role !== 'admin') {
    window.location.href = 'login.php';
    return;
  }
  renderAdminStats();
  renderUsersTable();
  renderAdminBookings();
  renderAdminMotors();
  const addMotorForm = document.getElementById('addMotorForm');
  if (addMotorForm) {
    addMotorForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('motorName').value.trim();
      const price = parseInt(document.getElementById('motorPrice').value);
      if (!name || isNaN(price)) return;
      const motors = JSON.parse(localStorage.getItem('motorcycles'));
      motors.push({ id: Date.now(), name, pricePerDay: price });
      localStorage.setItem('motorcycles', JSON.stringify(motors));
      e.target.reset();
      renderAdminMotors();
    });
  }
}

function renderAdminStats() {
  const bookings = JSON.parse(localStorage.getItem('bookings'));
  const users = JSON.parse(localStorage.getItem('users'));
  const revenue = bookings.filter(b => b.status === 'completed').reduce((s, b) => s + b.amount, 0);
  const pending = bookings.filter(b => b.status === 'pending').length;
  document.getElementById('totalRevenue').textContent = (revenue / 1000000).toFixed(1) + 'م';
  document.getElementById('newUsers').textContent = users.length;
  document.getElementById('pendingBookings').textContent = pending;
  document.getElementById('errorsCount').textContent = '0';
}

function renderUsersTable() {
  const users = JSON.parse(localStorage.getItem('users'));
  const tbody = document.getElementById('usersTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  users.forEach((u, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${u.name}</td>
      <td>${u.email}</td>
      <td>${u.role}</td>
    `;
    tbody.appendChild(tr);
  });
}

function renderAdminBookings() {
  const bookings = JSON.parse(localStorage.getItem('bookings'));
  const tbody = document.getElementById('adminBookingsBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  bookings.forEach(b => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${b.motoName}</td>
      <td>${b.userEmail}</td>
      <td>${b.startDate}</td>
      <td>${b.endDate}</td>
      <td>${renderStatusBadge(b.status)}</td>
      <td class="text-end">${b.amount.toLocaleString()}</td>
    `;
    tbody.appendChild(tr);
  });
}

function renderAdminMotors() {
  const motors = JSON.parse(localStorage.getItem('motorcycles'));
  const tbody = document.getElementById('motorTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  motors.forEach(m => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${m.name}</td>
      <td>${m.pricePerDay.toLocaleString()} تومان</td>
      <td class="text-end"><button class="btn btn-sm btn-danger" data-id="${m.id}"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
  });
  tbody.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id);
      const motors = JSON.parse(localStorage.getItem('motorcycles')).filter(m => m.id !== id);
      localStorage.setItem('motorcycles', JSON.stringify(motors));
      renderAdminMotors();
    });
  });
}

function renderStatusBadge(status) {
  switch (status) {
    case 'completed':
      return '<span class="badge bg-success-subtle text-success-emphasis rounded-pill">تکمیل شده</span>';
    case 'cancelled':
      return '<span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">لغو شده</span>';
    default:
      return '<span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">در انتظار</span>';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  initData();
  const page = window.location.pathname.split('/').pop();
  if (page === 'login.php') {
    setupLogin();
  } else if (page === 'dashboard.html') {
    loadUserDashboard();
  } else if (page === 'admin.php') {
    loadAdminDashboard();
  }
});

