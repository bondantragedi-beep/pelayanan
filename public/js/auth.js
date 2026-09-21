document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('togglePass');
    var passInput = document.getElementById('password');
    var eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passInput && eyeIcon) {
        toggleBtn.addEventListener('click', function () {
            var isHidden = passInput.type === 'password';
            passInput.type = isHidden ? 'text' : 'password';
            toggleBtn.setAttribute('aria-pressed', String(isHidden));
            toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            eyeIcon.innerHTML = isHidden
                ? '<path d="M3 3L21 21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M9.9 5.1C10.6 5 11.3 5 12 5C18.5 5 22 12 22 12C21.6 12.7 20.8 13.9 19.6 15.1M6.3 6.9C3.6 8.8 2 12 2 12C2 12 5.5 19 12 19C13.9 19 15.5 18.5 16.8 17.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 12.5C9.9 13.9 10.9 15 12 15C13.1 15 14 14 14 12.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'
                : '<path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.6"/>';
        });
    }

    var form = document.getElementById('loginForm');
    var statusMsg = document.getElementById('statusMsg');
    var statusMsgText = document.getElementById('statusMsgText');
    var npsnInput = document.getElementById('npsn');

    if (form) {
        form.addEventListener('submit', function (e) {
            var npsnVal = npsnInput.value.trim();
            var passVal = passInput.value.trim();

            if (!npsnVal || !passVal) {
                e.preventDefault();
                statusMsgText.textContent = 'Lengkapi NPSN dan kata sandi terlebih dahulu.';
                statusMsg.classList.add('show');
                (!npsnVal ? npsnInput : passInput).focus();
            }
            // Jika lolos validasi ringan ini, form akan dikirim (POST) ke
            // route('login') dan ditangani oleh controller/backend Laravel.
        });
    }
});
