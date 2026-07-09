<!-- Footer -->
<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <!-- Brand -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="footer-brand">DigiStore</h5>
                <p class="footer-desc">
                    Toko produk digital terpercaya. Temukan ebook, template, software, dan berbagai produk digital berkualitas.
                </p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <!-- Menu -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="footer-heading">Menu</h6>
                <ul class="footer-links">
                    <li><a href="index.php?url=home">Beranda</a></li>
                    <li><a href="index.php?url=product/search">Produk</a></li>
                    <li><a href="index.php?url=cart/index">Keranjang</a></li>
                </ul>
            </div>
            <!-- Bantuan -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="footer-heading">Bantuan</h6>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <!-- Kontak -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="footer-heading">Kontak</h6>
                <ul class="footer-contact">
                    <li><i class="fas fa-envelope"></i> <a href="mailto:support@digistore.com">support@digistore.com</a></li>
                    <li><i class="fas fa-phone-alt"></i> +62 812 3456 7890</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?= date('Y') ?> DigiStore. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk UAS Project</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Tambahan CSS (jika tidak ada di style.css) -->
<style>
    .footer-modern {
        background: linear-gradient(135deg, #1e293b 0%, #0f1724 100%);
        color: #cbd5e1;
        padding: 3rem 0 1.5rem;
        margin-top: 3rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .footer-brand {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #fff, #10b981);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 1rem;
    }
    .footer-desc {
        font-size: 0.9rem;
        line-height: 1.6;
        opacity: 0.8;
    }
    .social-icons {
        display: flex;
        gap: 12px;
        margin-top: 1rem;
    }
    .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        color: #fff;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .social-icon:hover {
        background: #10b981;
        transform: translateY(-3px);
        color: white;
    }
    .footer-heading {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        color: #fff;
        position: relative;
        display: inline-block;
    }
    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 35px;
        height: 2px;
        background: #10b981;
    }
    .footer-links, .footer-contact {
        list-style: none;
        padding-left: 0;
    }
    .footer-links li, .footer-contact li {
        margin-bottom: 0.6rem;
    }
    .footer-links a, .footer-contact a {
        color: #cbd5e1;
        text-decoration: none;
        transition: 0.2s;
    }
    .footer-links a:hover, .footer-contact a:hover {
        color: #10b981;
        padding-left: 5px;
    }
    .footer-contact i {
        width: 28px;
        color: #10b981;
    }
    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 1.5rem;
        margin-top: 1rem;
        font-size: 0.85rem;
    }
    @media (max-width: 768px) {
        .footer-modern {
            text-align: center;
        }
        .social-icons {
            justify-content: center;
        }
        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/assets/js/script.js"></script>
<script src="public/assets/js/cart.js"></script>
</body>
</html>