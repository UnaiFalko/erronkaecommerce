(() => {
    const cart = document.querySelector('[data-cart]');
    if (!cart) return;

    const formatter = new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    });
    const totalElement = cart.querySelector('[data-cart-total]');
    const quantities = cart.querySelectorAll('[data-cart-quantity]');

    const updateTotals = () => {
        let total = 0;

        quantities.forEach((quantity) => {
            const row = quantity.closest('tr');
            const priceCell = row.querySelector('[data-unit-price]');
            const lineTotal = row.querySelector('[data-line-total]');
            const unitPrice = Number(priceCell.dataset.unitPrice);
            const count = Math.max(0, Number(quantity.value) || 0);
            const subtotal = unitPrice * count;

            lineTotal.textContent = formatter.format(subtotal);
            total += subtotal;
        });

        totalElement.textContent = formatter.format(total);
    };

    quantities.forEach((quantity) => {
        quantity.addEventListener('input', updateTotals);
        quantity.addEventListener('change', () => cart.requestSubmit());
    });
})();