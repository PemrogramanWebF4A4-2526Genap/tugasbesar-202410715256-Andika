/**
 * Digital Product Store - Main JavaScript
 * Fungsi umum: wishlist toggle, review photo preview, alert auto-close, konfirmasi, dll.
 */

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // 1. KONFIRMASI UNTUK AKSI BERBAHAYA
    // ============================================
    document.querySelectorAll('.btn-danger, .delete-confirm, [data-confirm]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin melanjutkan? Aksi ini tidak dapat dibatalkan.')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // ============================================
    // 2. PREVIEW GAMBAR COVER DI FORM UPLOAD
    // ============================================
    const coverInput = document.querySelector('input[name="cover"]');
    if (coverInput) {
        coverInput.addEventListener('change', function(e) {
            const preview = document.getElementById('cover-preview');
            if (!preview) return;
            preview.innerHTML = '';
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '150px';
                    img.classList.add('rounded', 'mt-2', 'border');
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================
    // 3. PREVIEW FOTO REVIEW DI FORM REVIEW
    // ============================================
    const reviewPhotoInput = document.querySelector('input[name="photo"]');
    if (reviewPhotoInput) {
        reviewPhotoInput.addEventListener('change', function(e) {
            const preview = document.getElementById('photo-preview');
            if (!preview) return;
            preview.innerHTML = '';
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '150px';
                    img.classList.add('rounded', 'mt-2', 'border');
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================
    // 4. NOTIFIKASI ALERT OTOMATIS HILANG
    // ============================================
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ============================================
    // 5. WISHLIST TOGGLE (di halaman detail produk)
    // ============================================
    document.querySelectorAll('.wishlist-toggle').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            fetch(url, { method: 'GET' })
                .then(res => res.text())
                .then(() => {
                    // reload halaman agar status wishlist update
                    window.location.reload();
                });
        });
    });

    // ============================================
    // 6. TOOLTIP BOOTSTRAP (jika diperlukan)
    // ============================================
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    }

    // ============================================
    // 7. ACTIVE CLASS DI NAVBAR
    // ============================================
    const currentUrl = window.location.href;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // ============================================
    // 8. VALIDASI FORM UPLOAD
    // ============================================
    document.querySelector('form[action*="uploadProof"]')?.addEventListener('submit', function(e) {
        const fileInput = this.querySelector('input[name="proof"]');
        if (fileInput && fileInput.files.length === 0) {
            alert('Silakan pilih file bukti pembayaran terlebih dahulu.');
            e.preventDefault();
            return false;
        }
    });

    // ============================================
    // 9. KONFIRMASI KHUSUS UNTUK KONFIRMASI PEMBAYARAN SELLER
    // ============================================
    document.querySelectorAll('.confirm-payment, a[href*="confirmOrder"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Konfirmasi bahwa pembayaran sudah valid dan buyer akan mendapat akses download?')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // ============================================
    // 10. PASSWORD VISIBILITY TOGGLE (untuk login/register)
    // ============================================
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const target = document.querySelector(this.dataset.target);
            if (target) {
                const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
                target.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            }
        });
    });

});