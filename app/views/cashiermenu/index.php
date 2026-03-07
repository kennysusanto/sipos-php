<h1 class="page-title">Cashier Menu</h1>
<div class="page-subtitle">Select menu items and place an order.</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="dashboard-card">
        <div class="card-title">No menu items found</div>
        <div class="card-value card-value-sm">Create menu items first to start ordering.</div>
    </div>
<?php else: ?>
    <div class="cashiermenu-layout">
        <div class="menu-grid cashiermenu-catalog">
            <?php foreach ($items as $item): ?>
                <button
                    type="button"
                    class="menu-card cashier-menu-card"
                    data-id="<?= htmlspecialchars((string)($item['id'] ?? '')) ?>"
                    data-name="<?= htmlspecialchars((string)($item['display_name'] ?? '')) ?>"
                    data-price="<?= htmlspecialchars((string)($item['price'] ?? '0')) ?>"
                    data-stock="<?= htmlspecialchars((string)($item['stock'] ?? '0')) ?>"
                >
                    <?php if (empty($item['url'])): ?>
                        <div class="menu-card-image">
                            <div class="menu-card-image-placeholder">Image</div>
                        </div>
                    <?php else: ?>
                        <div class="menu-card-image">
                            <img src="<?= htmlspecialchars((string)$item['url']) ?>" />
                        </div>
                    <?php endif; ?>
                    <div class="menu-card-content">
                        <h3><?= htmlspecialchars((string)($item['display_name'] ?? '')) ?></h3>
                        <p><?= nl2br(htmlspecialchars((string)($item['description'] ?? ''))) ?></p>
                        <div class="menu-card-price">IDR <?= number_format((float)($item['price'] ?? 0), 0, ',', '.') ?></div>
                        <div class="menu-card-meta">Stock: <?= htmlspecialchars((string)($item['stock'] ?? '0')) ?></div>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>

        <aside class="dashboard-card cashiermenu-sidebar">
            <h3 class="cashiermenu-sidebar-title">Selected Items</h3>
            <div id="cashiermenu-cart-list" class="cashiermenu-cart-list">
                <p class="cashiermenu-empty">No items selected.</p>
            </div>
            <div class="cashiermenu-cart-total">
                <span>Total</span>
                <strong id="cashiermenu-cart-total-value">IDR 0</strong>
            </div>
            <form method="post" action="/cashiermenu" id="cashiermenu-order-form">
                <input type="hidden" name="cart_payload" id="cashiermenu-cart-payload" value="[]">
                <button type="submit" class="btn btn-primary cashiermenu-order-btn" id="cashiermenu-order-btn" disabled>Order</button>
            </form>
        </aside>
    </div>

    <script>
        (function () {
            const cardButtons = document.querySelectorAll('.cashier-menu-card');
            const cartList = document.getElementById('cashiermenu-cart-list');
            const payloadInput = document.getElementById('cashiermenu-cart-payload');
            const orderButton = document.getElementById('cashiermenu-order-btn');
            const totalValue = document.getElementById('cashiermenu-cart-total-value');

            const menuById = new Map();
            const cart = new Map();
            const idrFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            cardButtons.forEach(function (button) {
                const id = parseInt(button.getAttribute('data-id') || '0', 10);
                const name = button.getAttribute('data-name') || '';
                const price = parseFloat(button.getAttribute('data-price') || '0');
                const stock = parseInt(button.getAttribute('data-stock') || '0', 10);

                if (id <= 0) {
                    return;
                }

                menuById.set(id, {
                    id: id,
                    name: name,
                    price: Number.isFinite(price) ? price : 0,
                    stock: Number.isFinite(stock) ? stock : 0
                });

                button.addEventListener('click', function () {
                    incrementItem(id);
                });
            });

            cartList.addEventListener('click', function (event) {
                const target = event.target.closest('button[data-action][data-id]');
                if (!target) {
                    return;
                }

                const id = parseInt(target.getAttribute('data-id') || '0', 10);
                const action = target.getAttribute('data-action') || '';

                if (id <= 0) {
                    return;
                }

                if (action === 'inc') {
                    incrementItem(id);
                }

                if (action === 'dec') {
                    decrementItem(id);
                }
            });

            function incrementItem(id) {
                const menu = menuById.get(id);
                if (!menu || menu.stock <= 0) {
                    return;
                }

                const current = cart.get(id) || 0;
                if (current >= menu.stock) {
                    return;
                }

                cart.set(id, current + 1);
                renderCart();
            }

            function decrementItem(id) {
                const current = cart.get(id) || 0;
                if (current <= 1) {
                    cart.delete(id);
                } else {
                    cart.set(id, current - 1);
                }
                renderCart();
            }

            function renderCart() {
                const payload = [];
                let total = 0;
                cartList.innerHTML = '';

                if (cart.size === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'cashiermenu-empty';
                    empty.textContent = 'No items selected.';
                    cartList.appendChild(empty);
                    payloadInput.value = '[]';
                    totalValue.textContent = idrFormatter.format(0);
                    orderButton.disabled = true;
                    return;
                }

                cart.forEach(function (quantity, id) {
                    const menu = menuById.get(id);
                    if (!menu) {
                        return;
                    }

                    payload.push({ id: id, quantity: quantity });

                    const row = document.createElement('div');
                    row.className = 'cashiermenu-cart-item';

                    const info = document.createElement('div');
                    info.className = 'cashiermenu-cart-info';

                    const name = document.createElement('div');
                    name.className = 'cashiermenu-cart-name';
                    name.textContent = menu.name;

                    const price = document.createElement('div');
                    price.className = 'cashiermenu-cart-price';
                    const lineTotal = menu.price * quantity;
                    total += lineTotal;
                    price.textContent = idrFormatter.format(lineTotal);

                    info.appendChild(name);
                    info.appendChild(price);

                    const controls = document.createElement('div');
                    controls.className = 'cashiermenu-cart-controls';

                    const dec = document.createElement('button');
                    dec.type = 'button';
                    dec.className = 'btn btn-secondary cashiermenu-qty-btn';
                    dec.setAttribute('data-action', 'dec');
                    dec.setAttribute('data-id', String(id));
                    dec.textContent = '-';

                    const qty = document.createElement('span');
                    qty.className = 'cashiermenu-qty-value';
                    qty.textContent = String(quantity);

                    const inc = document.createElement('button');
                    inc.type = 'button';
                    inc.className = 'btn btn-secondary cashiermenu-qty-btn';
                    inc.setAttribute('data-action', 'inc');
                    inc.setAttribute('data-id', String(id));
                    inc.textContent = '+';

                    controls.appendChild(dec);
                    controls.appendChild(qty);
                    controls.appendChild(inc);

                    row.appendChild(info);
                    row.appendChild(controls);

                    cartList.appendChild(row);
                });

                payloadInput.value = JSON.stringify(payload);
                totalValue.textContent = idrFormatter.format(total);
                orderButton.disabled = payload.length === 0;
            }
        })();
    </script>
<?php endif; ?>
