document.addEventListener('DOMContentLoaded', function () {

    // Menu side bar
    const toggle = document.querySelector('.sidebar-toggle');
    const closeToggle = document.querySelector('.close-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }
    if (closeToggle && sidebar) {
        closeToggle.addEventListener('click', function () {
            sidebar.classList.remove('open');
        });
    }

    // Dashboard side bar
    const adminToggle = document.querySelector('.admin-sidebar-toggle');
    const adminSidebar = document.querySelector('.sidebar.admin-panel');
    const adminContainer = document.querySelector('.container.page.dashboard');
    if (adminToggle && adminSidebar) {

    adminToggle.addEventListener('click', function () {
        adminSidebar.classList.toggle('close');
        adminToggle.classList.toggle('close');
        adminContainer.classList.toggle('close');
    });

    adminToggle.addEventListener('click', function () {
        adminSidebar.classList.toggle('open');
        adminToggle.classList.toggle('open');
        adminContainer.classList.toggle('open');
    });

    }
 
    for(let i=1; i<=4; i++) {
        
        const input = document.getElementById('image_' + i);
        const label = document.getElementById('image_' + i + '_label_text');
        const removeBtn = document.querySelector('[data-input="image_' + i + '"]');
        const preview = document.getElementById('image_' + i + '_preview');
        const deleteCheckbox = document.getElementById('delete_image_' + i);

        if (input) {

        // Preview bij nieuwe selectie
        input.addEventListener('change', function(e) {
            if(e.target.files.length) {
                const file = e.target.files[0];
                label.textContent = file.name;
                // Toon preview
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.innerHTML = '<img src="' + ev.target.result + '" style="max-width:60px;max-height:60px;">';
                };
                reader.readAsDataURL(file);
                removeBtn.style.display = 'inline-block';
                if(deleteCheckbox) deleteCheckbox.checked = false;
            } else {
                label.textContent = "Kies afbeelding...";
                preview.innerHTML = '';
                removeBtn.style.display = 'none';
                if(deleteCheckbox) deleteCheckbox.checked = false;
            }
        });

        // Verwijderknop
        removeBtn.addEventListener('click', function() {
            input.value = "";
            label.textContent = "Kies afbeelding...";
            preview.innerHTML = '';
            removeBtn.style.display = 'none';
            if(deleteCheckbox) deleteCheckbox.checked = true;
        });   
        }
    }

    // Loader button logic
    document.querySelectorAll('form').forEach(function(form) {
        const button = form.querySelector('button[type="submit"]');
        const loader = button ? button.querySelector('.loader') : null;

        if (button && loader) {
            form.addEventListener('submit', function() {
                loader.style.display = 'inline-block';
                button.disabled = true;
            });

            // On page load, hide loader and enable button
            loader.style.display = 'none';
            button.disabled = false;
        }
    });


    // Customer account creation
    const altInput = document.querySelector('#alt-shipping');
    const altShipping = document.querySelector('.customer-details.alternate');

    if (altInput && altShipping) {
        altInput.addEventListener('change', function() {
            altShipping.classList.toggle('open');
            if (!altInput.checked) {
                // Clear all shipping fields when unchecked
                altShipping.querySelectorAll('input, select').forEach(function(field) {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = false;
                    } else {
                        field.value = '';
                    }
                });
            }
            // Update MyParcel widget after toggling
            if (typeof updateDeliveryOptionsFromForm === 'function') {
                updateDeliveryOptionsFromForm();
            }
        });
    }

    // Dashboard place order calculation script - function will loaded directly
    (function initOrderCalc(){
        const qtyInputs = document.querySelectorAll('.qty-input');
        if(!qtyInputs.length) return; // Not on order create page

        const totalEl = document.getElementById('total-price');
        const discountedEl = document.getElementById('discounted-total');
        const discountValueEl = document.getElementById('discount_value');
        const discountTypeEl = document.getElementById('discount_type');

        function formatEuro(val){
            return '€ ' + val.toFixed(2).replace('.', ',');
        }

        function updatePrices(){
            let total = 0;
            qtyInputs.forEach(input => {
                const qty = parseFloat(input.value) || 0;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                const id = input.getAttribute('data-id');
                const subtotal = qty * price;
                total += subtotal;
                const subItem = document.getElementById('sub-item-price-' + id);
                if(subItem){
                    subItem.innerText = qty > 0 ? formatEuro(subtotal) : '';
                }
            });

            if(totalEl){
                totalEl.innerText = total > 0 ? 'Totaal: ' + formatEuro(total) : '';
            }

            if(discountValueEl && discountTypeEl && discountedEl){
                const discountValue = parseFloat(discountValueEl.value) || 0;
                const discountType = discountTypeEl.value;
                let discountedTotal = total;
                if(discountValue > 0){
                    discountedTotal = discountType === 'percent'
                        ? total - (total * (discountValue / 100))
                        : total - discountValue;
                    if(discountedTotal < 0) discountedTotal = 0;
                    discountedEl.innerText = 'Totaal na korting: ' + formatEuro(discountedTotal);
                } else {
                    discountedEl.innerText = '';
                }
            }
        }

        qtyInputs.forEach(input => {
            input.addEventListener('input', updatePrices);
            input.addEventListener('change', updatePrices);
        });
        if(discountValueEl){ discountValueEl.addEventListener('input', updatePrices); }
        if(discountTypeEl){ discountTypeEl.addEventListener('change', updatePrices); }
        updatePrices();
    })();

});
