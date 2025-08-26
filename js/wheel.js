const spinBtn = document.getElementById('spin');
const wheel = document.getElementById('wheel');
const result = document.getElementById('result');
const segment = 36; // 360 / 10
let spinning = false;

spinBtn.addEventListener('click', async () => {
  if (spinning) return;
  spinning = true;
  result.textContent = '';
  try {
    const res = await fetch('api/spin.php', { method: 'POST' });
    const data = await res.json();
    if (!data.success) {
      result.textContent = data.message;
      spinning = false;
      return;
    }
    const finalAngle = (360 * 5) + (data.index * segment) + (segment / 2);
    wheel.style.transform = `rotate(${finalAngle}deg)`;
    setTimeout(() => {
      result.textContent = `شما برنده ${data.prize} شدید!`;
      spinning = false;
    }, 5000);
  } catch (e) {
    result.textContent = 'خطا در ارتباط با سرور';
    spinning = false;
  }
});
