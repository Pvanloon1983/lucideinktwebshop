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

});
