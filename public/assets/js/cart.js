/**
 * Digital Product Store - Cart JavaScript
 * Fungsi: add to cart (AJAX), update quantity, remove item
 */

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // 1. ADD TO CART (dari halaman produk)
    // ============================================
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            if (!productId) return;

            fetch('index.php?url=cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Produk ditambahkan ke keranjang');
                    updateCartBadge(data.totalItems);
                } else {
                    alert(data.message || 'Gagal menambahkan produk');
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                console.error(err);
            });
        });
    });

    // ============================================
    // 2. UPDATE QUANTITY (di halaman keranjang)
    // ============================================
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            if (!row) return;
            const productId = row.dataset.productId;
            const newQty = parseInt(this.value);

            if (isNaN(newQty) || newQty < 1) {
                alert('Jumlah minimal 1');
                this.value = 1;
                return;
            }

            fetch('index.php?url=cart/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=${newQty}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update subtotal di baris
                    const priceText = row.querySelector('.price').innerText.replace(/[^0-9]/g, '');
                    const price = parseInt(priceText) || 0;
                    const subtotal = price * newQty;
                    const subtotalEl = row.querySelector('.subtotal');
                    if (subtotalEl) {
                        subtotalEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
                    }

                    // Update total harga di footer
                    updateCartTotal();

                    // Update badge keranjang
                    updateCartBadge(data.totalItems);
                } else {
                    alert('Gagal update keranjang');
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                console.error(err);
            });
        });
    });

    // ============================================
    // 3. REMOVE ITEM (di halaman keranjang)
    // ============================================
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Hapus produk ini dari keranjang?')) return;

            const row = this.closest('tr');
            if (!row) return;
            const productId = row.dataset.productId;

            fetch('index.php?url=cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    updateCartTotal();
                    updateCartBadge(data.totalItems);

                    // Jika keranjang kosong, reload halaman untuk menampilkan pesan kosong
                    if (document.querySelectorAll('#cart-table tbody tr').length === 0) {
                        location.reload();
                    }
                } else {
                    alert('Gagal hapus item');
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                console.error(err);
            });
        });
    });

    // ============================================
    // 4. FUNGSI BANTUAN
    // ============================================

    /**
     * Update total harga di footer keranjang
     */
    function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(el => {
            const val = el.innerText.replace(/[^0-9]/g, '');
            total += parseInt(val) || 0;
        });
        const totalEl = document.getElementById('cart-total');
        if (totalEl) {
            totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    /**
     * Update badge jumlah item di navbar
     */
    function updateCartBadge(count) {
        const badge = document.querySelector('.badge-cart');
        if (badge) {
            if (count > 0) {
                badge.innerText = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Update juga di total-items (jika ada)
        const totalItemsEl = document.getElementById('total-items');
        if (totalItemsEl) {
            totalItemsEl.innerText = count || 0;
        }
    }

});