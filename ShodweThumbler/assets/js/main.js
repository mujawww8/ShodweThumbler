/**
 * Shodwe Tumbler Hub — Main JavaScript
 * Handles: Toggle Password, Shopping Cart, Quick View, Toasts, Button Actions
 */

document.addEventListener('DOMContentLoaded', function () {

    // ==================== TOAST NOTIFICATIONS ====================
    const ToastManager = {
        container: null,
        init() {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        },
        show(message, type = 'success', duration = 3000) {
            if (!this.container) this.init();
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            const icons = {
                success: '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                error: '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
                warning: '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                info: '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="#C4A265" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="16 12 12 8 8 12"></polyline><line x1="12" y1="16" x2="12" y2="8"></line></svg>'
            };
            toast.innerHTML = `${icons[type] || icons.info}<span>${message}</span>`;
            this.container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    };
    window.ToastManager = ToastManager;

    // ==================== TOGGLE PASSWORD ====================
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-toggle-password');
            const input = document.getElementById(targetId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            // Toggle eye icon
            if (isPassword) {
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        });
    });

    // ==================== SHOPPING CART ====================
    const Cart = {
        items: [],
        init() {
            const saved = localStorage.getItem('shodwe_cart');
            if (saved) {
                try { this.items = JSON.parse(saved); } catch (e) { this.items = []; }
            }
            this.updateBadge();
            this.renderCartPanel();
        },
        save() {
            localStorage.setItem('shodwe_cart', JSON.stringify(this.items));
        },
        addItem(product) {
            const existing = this.items.find(i => i.id === product.id && i.color === product.color);
            if (existing) {
                existing.quantity += (product.quantity || 1);
            } else {
                this.items.push({
                    id: product.id,
                    name: product.name,
                    price: parseInt(product.price),
                    image: product.image,
                    color: product.color || '',
                    quantity: product.quantity || 1
                });
            }
            this.save();
            this.updateBadge();
            this.renderCartPanel();
            ToastManager.show(`${product.name} ditambahkan ke keranjang!`, 'success');
        },
        removeItem(index) {
            const removed = this.items.splice(index, 1);
            this.save();
            this.updateBadge();
            this.renderCartPanel();
            if (removed.length) ToastManager.show(`${removed[0].name} dihapus dari keranjang`, 'warning');
        },
        updateQuantity(index, qty) {
            if (qty < 1) return this.removeItem(index);
            this.items[index].quantity = qty;
            this.save();
            this.updateBadge();
            this.renderCartPanel();
        },
        getTotal() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        getTotalItems() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },
        updateBadge() {
            const badges = document.querySelectorAll('.cart-badge');
            const count = this.getTotalItems();
            badges.forEach(badge => {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            });
        },
        formatRupiah(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
        renderCartPanel() {
            const body = document.querySelector('.cart-panel-body');
            const footer = document.querySelector('.cart-panel-footer');
            if (!body || !footer) return;

            if (this.items.length === 0) {
                body.innerHTML = `
                    <div class="cart-empty">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        <h4 style="margin-bottom:0.5rem;">Keranjang kosong</h4>
                        <p style="font-size:0.9rem;">Tambahkan produk tumbler favoritmu!</p>
                        <a href="/ShodweThumbler/toko.php" class="btn btn-primary" style="margin-top:1rem;padding:0.75rem 1.5rem;">Belanja Sekarang</a>
                    </div>`;
                footer.innerHTML = '';
                return;
            }

            let html = '';
            this.items.forEach((item, idx) => {
                html += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="/ShodweThumbler/assets/images/${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-info">
                        <div>
                            <h4 style="font-size:0.95rem;margin-bottom:0.25rem;">${item.name}</h4>
                            ${item.color ? `<span style="font-size:0.75rem;color:var(--text-muted);">Warna: ${item.color}</span>` : ''}
                        </div>
                        <div class="flex justify-between items-center" style="margin-top:0.5rem;">
                            <div class="qty-selector" style="transform:scale(0.85);transform-origin:left;">
                                <button type="button" onclick="Cart.updateQuantity(${idx}, ${item.quantity - 1})">−</button>
                                <input type="number" value="${item.quantity}" min="1" onchange="Cart.updateQuantity(${idx}, parseInt(this.value)||1)" readonly>
                                <button type="button" onclick="Cart.updateQuantity(${idx}, ${item.quantity + 1})">+</button>
                            </div>
                            <span style="font-weight:600;color:var(--primary-color);font-size:0.95rem;">${this.formatRupiah(item.price * item.quantity)}</span>
                        </div>
                    </div>
                    <button class="cart-item-remove" onclick="Cart.removeItem(${idx})" title="Hapus">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>`;
            });
            body.innerHTML = html;

            footer.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <span style="color:var(--text-muted);">Total (${this.getTotalItems()} item)</span>
                    <span style="font-size:1.25rem;font-weight:700;color:var(--primary-color);">${this.formatRupiah(this.getTotal())}</span>
                </div>
                <a href="/ShodweThumbler/checkout.php" class="btn btn-primary w-full" style="padding:0.875rem;font-size:1rem;">
                    Checkout Sekarang
                </a>
                <button onclick="Cart.togglePanel()" class="btn btn-outline w-full" style="margin-top:0.5rem;padding:0.75rem;background:white;">
                    Lanjut Belanja
                </button>`;
        },
        togglePanel() {
            const panel = document.querySelector('.cart-panel');
            const overlay = document.querySelector('.cart-overlay');
            if (!panel || !overlay) return;
            panel.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = panel.classList.contains('open') ? 'hidden' : '';
        }
    };
    window.Cart = Cart;
    Cart.init();

    // Cart panel toggle buttons
    document.querySelectorAll('[data-open-cart]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            Cart.togglePanel();
        });
    });
    const cartOverlay = document.querySelector('.cart-overlay');
    if (cartOverlay) cartOverlay.addEventListener('click', () => Cart.togglePanel());

    // Add to cart buttons
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const data = JSON.parse(this.getAttribute('data-add-to-cart'));
            Cart.addItem(data);
        });
    });

    // ==================== QUICK VIEW ====================
    document.querySelectorAll('[data-quick-view]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productData = JSON.parse(this.getAttribute('data-quick-view'));
            openQuickView(productData);
        });
    });

    function openQuickView(product) {
        const modal = document.getElementById('quickViewModal');
        if (!modal) return;

        modal.querySelector('.qv-image').src = '/ShodweThumbler/assets/images/' + product.image;
        modal.querySelector('.qv-image').alt = product.name;
        modal.querySelector('.qv-name').textContent = product.name;
        modal.querySelector('.qv-price').textContent = Cart.formatRupiah(parseInt(product.price));
        modal.querySelector('.qv-desc').textContent = product.description || '';
        modal.querySelector('.qv-rating').textContent = product.rating || '0';
        modal.querySelector('.qv-reviews').textContent = `(${product.reviews || 0} reviews)`;
        modal.querySelector('.qv-category').textContent = product.category || '';
        modal.querySelector('.qv-capacity').textContent = product.capacity || '500ml';

        // Color swatches
        const colorMap = {
            'Cream': '#F5E6D0', 'Mocha': '#8B6F47', 'Black': '#2C2C2C', 'White': '#F5F5F5',
            'Rose Gold': '#B76E79', 'Green': '#6B8E6B', 'Gold': '#D4A843', 'Brown': '#8B6914',
            'Latte': '#C8A882'
        };
        const swatchContainer = modal.querySelector('.qv-colors');
        swatchContainer.innerHTML = '';
        const colors = Object.entries(colorMap);
        colors.forEach(([name, hex]) => {
            const swatch = document.createElement('div');
            swatch.className = 'color-swatch' + (name === product.color ? ' active' : '');
            swatch.style.backgroundColor = hex;
            swatch.title = name;
            swatch.setAttribute('data-color', name);
            swatch.addEventListener('click', function () {
                swatchContainer.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
            });
            swatchContainer.appendChild(swatch);
        });

        // Quantity
        const qtyInput = modal.querySelector('.qv-qty');
        if (qtyInput) qtyInput.value = 1;

        // Add to cart from quick view
        const addBtn = modal.querySelector('.qv-add-cart');
        if (addBtn) {
            addBtn.onclick = function () {
                const qty = parseInt(modal.querySelector('.qv-qty')?.value) || 1;
                const selectedColor = modal.querySelector('.color-swatch.active');
                Cart.addItem({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    color: selectedColor ? selectedColor.getAttribute('data-color') : product.color,
                    quantity: qty
                });
                closeModal('quickViewModal');
            };
        }

        openModal('quickViewModal');
    }
    window.openQuickView = openQuickView;

    // ==================== MODAL SYSTEM ====================
    window.openModal = function (id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function (id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // Close modal on X button
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = this.closest('.modal-overlay');
            if (modal) closeModal(modal.id);
        });
    });

    // ESC key to close modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
            const cartPanel = document.querySelector('.cart-panel.open');
            if (cartPanel) Cart.togglePanel();
            const searchOverlay = document.querySelector('.search-overlay.active');
            if (searchOverlay) searchOverlay.classList.remove('active');
        }
    });

    // ==================== SEARCH OVERLAY ====================
    const searchBtn = document.querySelector('[data-search-toggle]');
    const searchOverlay = document.querySelector('.search-overlay');
    if (searchBtn && searchOverlay) {
        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            searchOverlay.classList.add('active');
            setTimeout(() => searchOverlay.querySelector('input')?.focus(), 100);
        });
        searchOverlay.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
        const searchInput = searchOverlay.querySelector('input');
        if (searchInput) {
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && this.value.trim()) {
                    window.location.href = '/ShodweThumbler/toko.php?search=' + encodeURIComponent(this.value.trim());
                }
            });
        }
    }

    // ==================== COPY TO CLIPBOARD ====================
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.getAttribute('data-copy');
            navigator.clipboard.writeText(text).then(() => {
                ToastManager.show('Berhasil disalin ke clipboard!', 'success');
            }).catch(() => {
                // Fallback
                const input = document.createElement('input');
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                input.remove();
                ToastManager.show('Berhasil disalin!', 'success');
            });
        });
    });

    // ==================== NEWSLETTER SUBSCRIBE ====================
    document.querySelectorAll('.footer form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput && emailInput.value.trim()) {
                ToastManager.show('Terima kasih! Anda berhasil berlangganan newsletter.', 'success');
                emailInput.value = '';
            } else {
                ToastManager.show('Silakan masukkan alamat email Anda.', 'warning');
            }
        });
    });

    // ==================== FLOATING CHAT BUTTON ====================
    document.querySelectorAll('[data-chat-btn]').forEach(btn => {
        btn.addEventListener('click', function () {
            ToastManager.show('Chat dengan customer service akan segera tersedia!', 'info');
        });
    });

    // ==================== ADMIN: SETTINGS TABS ====================
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.getAttribute('data-tab');
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const panel = document.getElementById(target);
            if (panel) panel.classList.add('active');
        });
    });

    // ==================== ADMIN: COLOR VARIANTS ====================
    const addColorBtn = document.getElementById('addColorVariant');
    if (addColorBtn) {
        addColorBtn.addEventListener('click', function () {
            const container = document.getElementById('colorVariantsContainer');
            if (!container) return;
            const idx = container.children.length;
            const div = document.createElement('div');
            div.className = 'color-variant-item';
            div.innerHTML = `
                <input type="color" name="variant_color[]" value="#C4A265">
                <input type="text" name="variant_name[]" class="form-control" placeholder="Nama warna" style="flex:1;">
                <input type="number" name="variant_stock[]" class="form-control" placeholder="Stok" style="width:80px;" min="0" value="0">
                <button type="button" class="color-variant-remove" onclick="this.parentElement.remove()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>`;
            container.appendChild(div);
        });
    }

    // ==================== QTY SELECTOR IN QUICK VIEW ====================
    document.addEventListener('click', function(e) {
        if (e.target.closest('.qv-qty-minus')) {
            const input = e.target.closest('.qty-selector').querySelector('input');
            if (input && parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }
        if (e.target.closest('.qv-qty-plus')) {
            const input = e.target.closest('.qty-selector').querySelector('input');
            if (input) input.value = parseInt(input.value) + 1;
        }
    });

});
