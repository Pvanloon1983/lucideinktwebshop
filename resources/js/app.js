// ------------------------------------------------------------
// Utilities (bovenaan zodat overal beschikbaar)
// ------------------------------------------------------------
const formatEuro = (val) => '€ ' + val.toFixed(2).replace('.', ',');

// ------------------------------------------------------------
// Dynamische verzendkosten/totaal op checkout
// ------------------------------------------------------------
const shippingCostEl = document.getElementById('shipping-cost');
const orderTotalEl = document.getElementById('order-total');
const altShippingInput = document.getElementById('alt-shipping');
const billingCountry = document.querySelector('select[name="billing_country"]');
const shippingCountry = document.querySelector('select[name="shipping_country"]');

function getSelectedCountry() {
  if (altShippingInput && altShippingInput.checked && shippingCountry) {
    return shippingCountry.value;
  }
  return billingCountry ? billingCountry.value : 'NL';
}

function updateShippingCost() {
  if (!shippingCostEl || !orderTotalEl) return;
  const country = getSelectedCountry();
  if (!country) return;
  fetch(`/api/shipping-cost?country=${country}`)
    .then(r => r.json())
    .then(data => {
      const cost = parseFloat(data.cost) || 0;
      shippingCostEl.textContent = 'Verzendkosten: ' + formatEuro(cost);
      // Zoek het originele totaalbedrag (zonder verzendkosten)
      let subtotal = 0;
      if (orderTotalEl.dataset.subtotal) {
        subtotal = parseFloat(orderTotalEl.dataset.subtotal);
      } else {
        // Fallback: probeer uit de tekst te halen
        const match = orderTotalEl.textContent.replace(',', '.').match(/([\d\.]+)/);
        subtotal = match ? parseFloat(match[1]) : 0;
      }
      orderTotalEl.textContent = 'Totaal: ' + formatEuro(subtotal + cost);
    });
}

if (billingCountry) billingCountry.addEventListener('change', updateShippingCost);
if (shippingCountry) shippingCountry.addEventListener('change', updateShippingCost);
if (altShippingInput) altShippingInput.addEventListener('change', updateShippingCost);
updateShippingCost();

import axios from 'axios';

// Set CSRF token for all Axios requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

document.addEventListener('DOMContentLoaded', () => {
  // ------------------------------------------------------------
  // Utilities
  // ------------------------------------------------------------
  const formatEuro = (val) => '€ ' + val.toFixed(2).replace('.', ',');

  const ensureToast = () => {
    let toast = document.getElementById('copy-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'copy-toast';
      toast.className = 'copy-toast';
      document.body.appendChild(toast);
    }
    return toast;
  };

  const showToast = (msg, isError = false) => {
    const toast = ensureToast();
    toast.textContent = msg;
    toast.classList.remove('show', 'error');
    if (isError) toast.classList.add('error');
    void toast.offsetWidth; // reflow
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
  };

  // ------------------------------------------------------------
  // Page setup
  // ------------------------------------------------------------
  try {
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
    setTimeout(() => window.scrollTo(0, 0), 0);
  } catch (_) { }

  // ------------------------------------------------------------
  // Sidebar toggles
  // ------------------------------------------------------------
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.querySelector('.sidebar-toggle');
  const closeToggle = document.querySelector('.close-toggle');

  if (sidebar && toggle) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }
  if (sidebar && closeToggle) {
    closeToggle.addEventListener('click', () => sidebar.classList.remove('open'));
  }

  // Dashboard sidebar
  const adminSidebar = document.querySelector('.sidebar.admin-panel');
  const adminToggle = document.querySelector('.admin-sidebar-toggle');
  const adminContainer = document.querySelector('.container.page.dashboard');

  if (adminSidebar && adminToggle && adminContainer) {
    adminToggle.addEventListener('click', () => {
      [adminSidebar, adminToggle, adminContainer].forEach(el => {
        el.classList.toggle('open');
        el.classList.toggle('close');
      });
    });
  }

  // ------------------------------------------------------------
  // Image pickers (1–4)
  // ------------------------------------------------------------
  for (let i = 1; i <= 4; i++) {
    const input = document.getElementById(`image_${i}`);
    if (!input) continue;

    const label = document.getElementById(`image_${i}_label_text`);
    const preview = document.getElementById(`image_${i}_preview`);
    const removeBtn = document.querySelector(`[data-input="image_${i}"]`);
    const deleteCheckbox = document.getElementById(`delete_image_${i}`);

    input.addEventListener('change', (e) => {
      if (e.target.files.length) {
        const file = e.target.files[0];
        if (label) label.textContent = file.name;

        const reader = new FileReader();
        reader.onload = (ev) => {
          if (preview) {
            preview.innerHTML = `<img src="${ev.target.result}" style="max-width:60px;max-height:60px;" alt="Preview">`;
          }
        };
        reader.readAsDataURL(file);

        if (removeBtn) removeBtn.style.display = 'inline-block';
        if (deleteCheckbox) deleteCheckbox.checked = false;
      } else {
        if (label) label.textContent = "Kies afbeelding...";
        if (preview) preview.innerHTML = '';
        if (removeBtn) removeBtn.style.display = 'none';
        if (deleteCheckbox) deleteCheckbox.checked = false;
      }
    });

    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        input.value = "";
        if (label) label.textContent = "Kies afbeelding...";
        if (preview) preview.innerHTML = '';
        removeBtn.style.display = 'none';
        if (deleteCheckbox) deleteCheckbox.checked = true;
      });
    }
  }

  // ------------------------------------------------------------
  // Loader button logic (robust, only on actual submit)
  // ------------------------------------------------------------
  function enableAllSubmitButtonsAndHideLoaders() {
    document.querySelectorAll('button[type="submit"], button.add-to-cart-button').forEach(btn => {
      btn.disabled = false;
      const loader = btn.querySelector('.loader');
      if (loader) loader.style.display = 'none';
    });
  }

  enableAllSubmitButtonsAndHideLoaders();

  function getSubmitButtonsForForm(form) {
    const formId = form.id;
    let buttons = Array.from(form.querySelectorAll('button[type="submit"]'));
    if (formId) {
      buttons = buttons.concat(Array.from(document.querySelectorAll(`button[form='${formId}']`)));
    }
    return Array.from(new Set(buttons));
  }

  function showLoaderAndDisable(btn) {
    if (!btn) return;
    const loader = btn.querySelector('.loader');
    if (loader) loader.style.display = 'inline-block';
    btn.disabled = true;
  }

  function hideLoaderAndEnable(btn) {
    if (!btn) return;
    const loader = btn.querySelector('.loader');
    if (loader) loader.style.display = 'none';
    btn.disabled = false;
  }

  document.querySelectorAll('form').forEach(form => {
    // Remove previous submit event listeners
    form.removeEventListener('submit', form._loaderSubmitHandler || (() => { }));
    form._loaderSubmitHandler = function (e) {
      // Only show loader for actual submit, not for .needs-confirm
      if (!form.classList.contains('needs-confirm')) {
        // Find the button that triggered submit
        let btn = e.submitter || null;
        if (!btn) {
          // Fallback: find first enabled submit button
          btn = getSubmitButtonsForForm(form).find(b => !b.disabled);
        }
        showLoaderAndDisable(btn);
        // Optionally, re-enable after navigation or error
        setTimeout(() => hideLoaderAndEnable(btn), 5000);
      }
    };
    form.addEventListener('submit', form._loaderSubmitHandler);
  });

  // ------------------------------------------------------------
  // Alt shipping panel
  // ------------------------------------------------------------
  const altInput = document.querySelector('#alt-shipping');
  const altShipping = document.querySelector('.customer-details.alternate');

  if (altInput && altShipping) {
    altInput.addEventListener('change', () => {
      altShipping.classList.toggle('open');

      if (!altInput.checked) {
        altShipping.querySelectorAll('input, select').forEach((field) => {
          if (['checkbox', 'radio'].includes(field.type)) field.checked = false;
          else field.value = '';
        });
      }

      try { hardResetMyParcelState(); } catch (_) { }
    });
  }

  // ------------------------------------------------------------
  // Instant order calc
  // ------------------------------------------------------------
  (function initOrderCalc() {
    const qtyInputs = document.querySelectorAll('.qty-input');
    if (!qtyInputs.length) return;

    const totalEl = document.getElementById('total-price');
    const discountedEl = document.getElementById('discounted-total');
    const discountValueEl = document.getElementById('discount_value');
    const discountTypeEl = document.getElementById('discount_type');

    const updatePrices = () => {
      let total = 0;
      qtyInputs.forEach((input) => {
        const qty = parseFloat(input.value) || 0;
        const price = parseFloat(input.dataset.price) || 0;
        const id = input.dataset.id;
        const subtotal = qty * price;
        total += subtotal;
        const subItem = document.getElementById(`sub-item-price-${id}`);
        if (subItem) subItem.innerText = qty > 0 ? formatEuro(subtotal) : '';
      });

      if (totalEl) totalEl.innerText = total > 0 ? 'Totaal: ' + formatEuro(total) : '';

      if (discountValueEl && discountTypeEl && discountedEl) {
        const discountValue = parseFloat(discountValueEl.value) || 0;
        const discountType = discountTypeEl.value;
        let discountedTotal = total;

        if (discountValue > 0) {
          discountedTotal = discountType === 'percent'
            ? total - total * (discountValue / 100)
            : total - discountValue;
          discountedTotal = Math.max(discountedTotal, 0);
          discountedEl.innerText = 'Totaal na korting: ' + formatEuro(discountedTotal);
        } else {
          discountedEl.innerText = '';
        }
      }
    };

    qtyInputs.forEach((input) => {
      input.addEventListener('input', updatePrices);
      input.addEventListener('change', updatePrices);
    });
    discountValueEl?.addEventListener('input', updatePrices);
    discountTypeEl?.addEventListener('change', updatePrices);

    updatePrices();
  })();

  // ------------------------------------------------------------
  // Copy payment link
  // ------------------------------------------------------------
  const copyBtn = document.getElementById('copy-payment-link');
  if (copyBtn) {
    const explicitLink = copyBtn.dataset.paymentLink;
    const anchorLink = document.querySelector('#payment-link a')?.href || '';
    const linkToCopy = explicitLink || anchorLink || '';

    const fallbackCopy = (text) => {
      try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.readOnly = true;
        ta.style.position = 'absolute';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        if (ok) showToast('Betaallink gekopieerd naar klembord');
        else showToast('Kopiëren mislukt, kopieer handmatig', true);
      } catch {
        showToast('Kopiëren mislukt, kopieer handmatig', true);
      }
    };

    copyBtn.addEventListener('click', () => {
      if (!linkToCopy) return;
      if (navigator.clipboard?.writeText) {
        navigator.clipboard.writeText(linkToCopy)
          .then(() => showToast('Betaallink gekopieerd naar klembord'))
          .catch(() => fallbackCopy(linkToCopy));
      } else {
        fallbackCopy(linkToCopy);
      }
    });
  }

  // ------------------------------------------------------------
  // Custom confirm modal (cart removal etc.)
  // ------------------------------------------------------------
  let confirmModalOpen = false;
  let lastConfirmBtn = null;
  window.showConfirmModal = (message, onConfirm, triggerBtn) => {
    if (confirmModalOpen) return;
    confirmModalOpen = true;
    lastConfirmBtn = triggerBtn || null;
    document.querySelectorAll('.custom-confirm-modal').forEach((m) => m.remove());
    const modal = document.createElement('div');
    modal.className = 'custom-confirm-modal';
    modal.innerHTML = `
      <div class="custom-confirm-modal-backdrop"></div>
      <div class="custom-confirm-modal-content">
        <div class="custom-confirm-modal-message">${message}</div>
        <div class="custom-confirm-modal-actions">
          <button class="btn confirm-btn" type="button">Ja, bevestigen</button>
          <button class="btn cancel-btn" type="button">Annuleren</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    modal.querySelector('.confirm-btn').focus();
    modal.querySelector('.confirm-btn').onclick = () => {
      confirmModalOpen = false;
      modal.remove();
      onConfirm?.();
      lastConfirmBtn = null;
    };
    modal.querySelector('.cancel-btn').onclick =
      modal.querySelector('.custom-confirm-modal-backdrop').onclick = () => {
        confirmModalOpen = false;
        modal.remove();
        // Hide loader if present
        if (lastConfirmBtn) {
          const loader = lastConfirmBtn.querySelector('.loader');
          if (loader) loader.style.display = 'none';
          lastConfirmBtn.disabled = false;
          lastConfirmBtn = null;
        }
      };
  };

  // ------------------------------------------------------------
  // Kortingscode UI + Axios
  // ------------------------------------------------------------
  const updateDiscountUI = (data, code) => {
    const discountRow = document.getElementById('discount-row');
    const newTotalRow = document.getElementById('new-total-row');
    const discountAmount = document.getElementById('discount-amount');
    const orderTotal = document.getElementById('order-total');
    const orderNewTotal = document.getElementById('order-new-total');
    const discountCodeLabel = document.getElementById('discount-code-label');
    const removeDiscountContainer = document.getElementById('remove-discount-container');

    if (data && data.discount_amount > 0) {
      const isPercent = data.discount?.discount_type === 'percent';
      const shownDiscount = isPercent
        ? `${Number(data.discount.discount)}%`
        : ('€ ' + data.discount_amount.toFixed(2).replace('.', ','));

      if (discountRow) discountRow.style.display = '';
      if (newTotalRow) newTotalRow.style.display = '';
      if (discountAmount) discountAmount.textContent = shownDiscount;
      if (orderNewTotal) orderNewTotal.textContent = '€ ' + data.new_total.toFixed(2).replace('.', ',');
      if (orderTotal) orderTotal.textContent = '€ ' + data.total.toFixed(2).replace('.', ',');
      if (discountCodeLabel && code) discountCodeLabel.textContent = '(' + code + ')';
      if (removeDiscountContainer) removeDiscountContainer.style.display = '';
    } else {
      if (discountRow) discountRow.style.display = 'none';
      if (newTotalRow) newTotalRow.style.display = 'none';
      if (discountAmount) discountAmount.textContent = '0,00';
      if (orderNewTotal) orderNewTotal.textContent = '€ 0,00';
      if (orderTotal && data) orderTotal.textContent = '€ ' + data.total.toFixed(2).replace('.', ',');
      if (discountCodeLabel) discountCodeLabel.textContent = '';
      if (removeDiscountContainer) removeDiscountContainer.style.display = 'none';
    }
  };

  const discountButton = document.getElementById('add_discount_code');
  const discountCodeInput = document.getElementById('discount_code');
  let discountMsg = document.getElementById('discount_code_msg');

  if (!discountMsg && discountCodeInput) {
    discountMsg = document.createElement('div');
    discountMsg.id = 'discount_code_msg';
    discountMsg.style.marginTop = '6px';
    discountMsg.style.fontSize = '15px';
    discountCodeInput.parentNode.appendChild(discountMsg);
  }

  if (discountButton && discountCodeInput) {
    const loader = discountButton.querySelector('.loader');
    if (loader) loader.style.display = 'none';

    discountButton.addEventListener('click', (e) => {
      e.preventDefault();
      const code = discountCodeInput.value.trim();
      const billingEmailInput = document.querySelector('[name="billing_email"]');
      const billingEmail = billingEmailInput ? billingEmailInput.value.trim() : '';
      if (!billingEmail) {
        discountMsg.textContent = 'Vul eerst een e-mailadres in.';
        discountMsg.style.color = '#b30000';
        return;
      }
      if (!code) {
        discountMsg.textContent = 'Vul een kortingscode in.';
        discountMsg.style.color = '#b30000';
        return;
      }
      if (loader) loader.style.display = 'inline-block';
      discountButton.disabled = true;
      axios.post('/winkel/checkout/apply-discount-code', { code, billing_email: billingEmail }, {
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
        .then((response) => {
          const data = response.data;
          if (data.success) {
            discountMsg.textContent = 'Kortingscode toegepast.';
            discountMsg.style.color = 'green';
            updateDiscountUI(data, code);
          } else {
            discountMsg.textContent = data.message || 'Code bestaat niet.';
            discountMsg.style.color = '#b30000';
            updateDiscountUI(null);
          }
        })
        .catch((error) => {
          let msg = 'Er is een fout opgetreden.';
          if (error.response && error.response.data && error.response.data.message) {
            msg = error.response.data.message;
          }
          discountMsg.textContent = msg;
          discountMsg.style.color = '#b30000';
        })
        .finally(() => {
          if (loader) loader.style.display = 'none';
          discountButton.disabled = false;
        });
    });
  }

  const removeDiscountBtn = document.getElementById('remove_discount_code');
  if (removeDiscountBtn) {
    removeDiscountBtn.addEventListener('click', () => {
      axios.delete('/winkel/checkout/remove-discount-code', {
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        data: {}
      })
        .then((response) => {
          const data = response.data;
          updateDiscountUI(data);
          discountMsg.textContent = 'Kortingscode verwijderd.';
          discountMsg.style.color = '#b30000';
          discountCodeInput.value = '';
        })
        .catch(() => {
          discountMsg.textContent = 'Er is een fout opgetreden.';
          discountMsg.style.color = '#b30000';
        });

    });
  }


  // --- MyParcel Delivery Options Widget Integration (v6 compliant) ---
  const WIDGET_SELECTOR = '#myparcel-delivery-options';

  /* ---------------- Address Handling ---------------- */
  function currentAddress() {
    const useAlt = document.getElementById('alt-shipping')?.checked;
    const p = useAlt ? 'shipping_' : 'billing_';
    const street = document.querySelector(`[name="${p}street"]`)?.value || '';
    const nr = document.querySelector(`[name="${p}house_number"]`)?.value || '';
    return {
      cc: document.querySelector(`[name="${p}country"]`)?.value || 'NL',
      postalCode: (document.querySelector(`[name="${p}postal_code"]`)?.value || '')
        .replace(/\s+/g, '')
        .toUpperCase(),
      number: nr,
      street: street && nr ? `${street} ${nr}` : street,
      city: document.querySelector(`[name="${p}city"]`)?.value || '',
    };
  }

  function addressComplete(a) {
    return a.cc && a.postalCode && a.number && a.street && a.city;
  }

  /* ---------------- Hidden Input ---------------- */
  function ensureMyParcelInput() {
    let input = document.getElementById('myparcel_delivery_options');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.id = 'myparcel_delivery_options';
      input.name = 'myparcel_delivery_options';
      document.querySelector('form')?.appendChild(input);
    }
    return input;
  }

  /* ---------------- Locale Strings ---------------- */
  function getLocaleStrings(locale) {
    const strings = {
      nl: {
        deliveryTitle: 'Levering thuis of op het werk',
        pickupTitle: 'Ophalen bij een afleverpunt',
        deliveryStandard: 'Thuisbezorging',
        deliverySameDay: 'Vandaag bezorgd',
        deliveryExpress: 'Snelle levering',
        deliverySaturday: 'Bezorging op zaterdag',
        onlyRecipient: 'Alleen geadresseerde',
        signature: 'Handtekening voor ontvangst',
        free: 'Gratis',
        from: 'Vanaf',
        close: 'Sluiten',
        loading: 'Opties laden...',
        noOptions: 'Geen bezorgopties beschikbaar',
        choosePickup: 'Kies een afhaalpunt',
        postcode: 'Postcode',
        houseNumber: 'Huisnummer',
        street: 'Straat',
        city: 'Plaats',
        list: 'Lijst',
        map: 'Kaart',
        showMoreHours: 'Toon meer tijdvakken',
        showMoreLocations: 'Toon meer locaties',
        deliveryStandardTitle: 'Standaard bezorging',
        openingHours: 'Openingstijden',
        closed: 'gesloten',
      },
      en: {
        deliveryTitle: 'Home or work delivery',
        pickupTitle: 'Pick up at a service point',
        deliveryStandard: 'Home delivery',
        deliverySameDay: 'Delivered today',
        deliveryExpress: 'Express delivery',
        deliverySaturday: 'Saturday delivery',
        onlyRecipient: 'Only recipient',
        signature: 'Signature required',
        free: 'Free',
        from: 'From',
        close: 'Close',
        loading: 'Loading options...',
        noOptions: 'No delivery options available',
        choosePickup: 'Choose a pickup point',
        postcode: 'Postal code',
        houseNumber: 'House number',
        street: 'Street',
        city: 'City',
        list: 'List',
        map: 'Map',
        showMoreHours: 'Show more time slots',
        showMoreLocations: 'Show more locations',
        deliveryStandardTitle: 'Standard delivery',
        openingHours: 'Opening hours',
      },
    };
    return strings[locale] || strings['en'];
  }

  /* ---------------- Widget Dispatcher ---------------- */
  function dispatchMyParcel() {
    const addr = currentAddress();
    const container = document.querySelector(WIDGET_SELECTOR);
    if (!container) return;

    if (!addressComplete(addr)) {
      container.style.display = 'none';
      ensureMyParcelInput().value = '';
      return;
    }
    container.style.display = '';

    // Determine locale (ISO 639-1)
    let locale =
      document.documentElement.lang ||
      document.querySelector('meta[name="app-locale"]')?.content;
    if (!locale || typeof locale !== 'string' || locale.length < 2) locale = 'en';

    const configuration = {
      selector: WIDGET_SELECTOR,
      address: addr, // expects { cc, postalCode, street, number, city }
      config: {
        platform: 'myparcel',
        locale: locale,
        packageType: 'package',
        dropOffDelay: 1,
        deliveryDaysWindow: 0,
        allowPickupLocationsViewSelection: true,
        pickupLocationsDefaultView: 'list',
        showPriceZeroAsFree: false,
        carrierSettings: {
          postnl: {
            allowDeliveryOptions: true,
            allowStandardDelivery: true,
            allowExpressDelivery: false,
            allowSameDayDelivery: false,
            allowSaturdayDelivery: false,
            allowMorningDelivery: false,
            allowEveningDelivery: false,
            allowMondayDelivery: false,
            allowOnlyRecipient: false,
            allowSignature: false,
            allowPickupLocations: true,
          },
        },
      },
      strings: getLocaleStrings(locale),
    };

    // Dispatch configuration to widget
    document.dispatchEvent(
      new CustomEvent('myparcel_update_delivery_options', {
        detail: configuration,
      }),
    );
  }

  /* ---------------- Event Listeners ---------------- */
  // Listen ONCE for widget updates
  document.addEventListener('myparcel_updated_delivery_options', (e) => {
    console.log('[MyParcel] updated_delivery_options event:', e.detail);
    const input = ensureMyParcelInput();
    input.value = e.detail ? JSON.stringify(e.detail) : '';



  });

  // Listen for errors
  document.addEventListener('myparcel_error_delivery_options', (e) => {
    console.error('[MyParcel] error_delivery_options event:', e.detail);
  });

  // Attach listeners to relevant fields
  [
    'billing_country',
    'billing_postal_code',
    'billing_street',
    'billing_house_number',
    'billing_city',
    'shipping_country',
    'shipping_postal_code',
    'shipping_street',
    'shipping_house_number',
    'shipping_city',
    'alt-shipping',
  ].forEach((name) => {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) {
      el.addEventListener('input', dispatchMyParcel);
      el.addEventListener('change', dispatchMyParcel);
    }
  });

  // Initial dispatch
  dispatchMyParcel();


  // On form submit, ensure input is present and not empty
  const formCheck = document.querySelector('.form.checkout');
  if (formCheck) {
    formCheck.addEventListener('submit', function (e) {
      const input = ensureMyParcelInput();
      if (!input.value || input.value === '{}' || input.value === 'null') {
        // e.preventDefault();
        // alert('Kies een bezorgoptie voordat je de bestelling plaatst.');
        console.log('Kies een bezorgoptie voordat je de bestelling plaatst.');
        const submitBtn = formCheck.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
      }
    });
  }

  const formOrder = document.querySelector('.form.order');
  if (formOrder) {
    formOrder.addEventListener('submit', function (e) {
      const input = ensureMyParcelInput();
      if (!input.value || input.value === '{}' || input.value === 'null') {
        // e.preventDefault();
        // alert('Kies een bezorgoptie voordat je de bestelling plaatst.');
        console.log('Kies een bezorgoptie voordat je de bestelling plaatst.');
        const submitBtn = formOrder.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
      }
    });
  }

  // Universal confirmation modal logic for forms with .needs-confirm class
  function setupUniversalConfirmModals() {
    document.querySelectorAll('form.needs-confirm').forEach(function (form) {
      const formId = form.id;
      let triggerBtns = [];
      if (formId) {
        triggerBtns = Array.from(document.querySelectorAll(`button[form='${formId}']`));
      }
      const insideBtn = form.querySelector('button[type="submit"]');
      if (insideBtn) triggerBtns.push(insideBtn);
      triggerBtns = Array.from(new Set(triggerBtns));
      triggerBtns.forEach(triggerBtn => {
        if (!triggerBtn) return;
        triggerBtn.removeEventListener('click', triggerBtn._universalConfirmHandler || (() => { }));
        triggerBtn._universalConfirmHandler = function (e) {
          e.preventDefault();
          window.showConfirmModal(form.getAttribute('data-confirm') || 'Weet je het zeker?', function () {
            showLoaderAndDisable(triggerBtn);
            form.submit();
            // Optionally, re-enable after navigation or error
            setTimeout(() => hideLoaderAndEnable(triggerBtn), 5000);
          }, triggerBtn);
        };
        triggerBtn.addEventListener('click', triggerBtn._universalConfirmHandler);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupUniversalConfirmModals);
  } else {
    setupUniversalConfirmModals();
  }

  // On DOM changes, re-enable all submit buttons and hide loaders, and re-setup modals
  new MutationObserver((mutations) => {
    enableAllSubmitButtonsAndHideLoaders();
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1) {
          if (node.tagName === 'FORM') {
            // Setup loader logic for new forms
            node.removeEventListener('submit', node._loaderSubmitHandler || (() => { }));
            node._loaderSubmitHandler = function (e) {
              if (!node.classList.contains('needs-confirm')) {
                let btn = e.submitter || null;
                if (!btn) {
                  btn = getSubmitButtonsForForm(node).find(b => !b.disabled);
                }
                showLoaderAndDisable(btn);
                setTimeout(() => hideLoaderAndEnable(btn), 5000);
              }
            };
            node.addEventListener('submit', node._loaderSubmitHandler);
          }
          else node.querySelectorAll?.('form').forEach(form => {
            form.removeEventListener('submit', form._loaderSubmitHandler || (() => { }));
            form._loaderSubmitHandler = function (e) {
              if (!form.classList.contains('needs-confirm')) {
                let btn = e.submitter || null;
                if (!btn) {
                  btn = getSubmitButtonsForForm(form).find(b => !b.disabled);
                }
                showLoaderAndDisable(btn);
                setTimeout(() => hideLoaderAndEnable(btn), 5000);
              }
            };
            form.addEventListener('submit', form._loaderSubmitHandler);
          });
        }
      });
    });
    setupUniversalConfirmModals();
  }).observe(document.body, { childList: true, subtree: true });

  // ------------------------------------------------------------
  // Password show/hide toggle (Font Awesome)
  // ------------------------------------------------------------
  function setupPasswordToggles() {
    document.querySelectorAll('input[type="password"]').forEach(input => {
      // Avoid double wrapping
      if (input.parentElement?.classList.contains('password-toggle-container')) return;
      // Create container
      const container = document.createElement('div');
      container.className = 'password-toggle-container';
      container.style.position = 'relative';
      input.parentNode.insertBefore(container, input);
      container.appendChild(input);
      // Create toggle button
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'password-toggle-btn';
      btn.style.position = 'absolute';
      btn.style.right = '8px';
      btn.style.top = '50%';
      btn.style.transform = 'translateY(-50%)';
      btn.style.background = 'none';
      btn.style.border = 'none';
      btn.style.cursor = 'pointer';
      btn.style.padding = '0';
      btn.style.zIndex = '2';
      btn.innerHTML = '<i style="font-size: 20px;color: var(--main-font-color);opacity: 0.5" class="fa fa-eye"></i>';
      container.appendChild(btn);
      btn.addEventListener('click', () => {
        if (input.type === 'password') {
          input.type = 'text';
          btn.innerHTML = '<i style="font-size: 20px;color: var(--main-font-color);opacity: 0.5" class="fa fa-eye-slash"></i>';
        } else {
          input.type = 'password';
          btn.innerHTML = '<i style="font-size: 20px;color: var(--main-font-color);opacity: 0.5" class="fa fa-eye"></i>';
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupPasswordToggles);
  } else {
    setupPasswordToggles();
  }

  // Also handle dynamically added password fields
  new MutationObserver((mutations) => {
    setupPasswordToggles();
  }).observe(document.body, { childList: true, subtree: true });

  // Configurable speed (degrees per second) and direction (1=clockwise, -1=counterclockwise) for each hand
  const config = {
    hour: {
      speed: 400,
      direction: 1
    }, // 1x real speed, clockwise
    minute: {
      speed: 90,
      direction: 1
    }, // 1x real speed, clockwise
    second: {
      speed: 5,
      direction: 1
    } // 1x real speed, clockwise
  };

  // Internal state for virtual time
  let base = new Date();
  let last = Date.now();
  let hourAngle = ((base.getHours() % 12) + base.getMinutes() / 60 + base.getSeconds() / 3600) * 30;
  let minuteAngle = (base.getMinutes() + base.getSeconds() / 60 + base.getMilliseconds() / 60000) * 6;
  let secondAngle = (base.getSeconds() + base.getMilliseconds() / 1000) * 6;

  function animateHands() {
    const now = Date.now();
    const delta = (now - last) / 1000; // seconds since last frame
    last = now;

    // Advance each hand by its own speed and direction
    hourAngle += config.hour.speed * config.hour.direction * (360 / 43200) * delta; // 12h = 43200s
    minuteAngle += config.minute.speed * config.minute.direction * (360 / 3600) * delta; // 1h = 3600s
    secondAngle += config.second.speed * config.second.direction * (360 / 60) * delta; // 1m = 60s

    // Normalize angles
    hourAngle = ((hourAngle % 360) + 360) % 360;
    minuteAngle = ((minuteAngle % 360) + 360) % 360;
    secondAngle = ((secondAngle % 360) + 360) % 360;

    document.querySelector('.css-hour-hand').style.transform =
      `translate(-50%, 0) rotate(${hourAngle}deg)`;
    document.querySelector('.css-minute-hand').style.transform =
      `translate(-50%, 0) rotate(${minuteAngle}deg)`;
    document.querySelector('.css-second-hand').style.transform =
      `translate(-50%, 0) rotate(${secondAngle}deg)`;
    requestAnimationFrame(animateHands);
  }
  animateHands();

  // Example: to change speed/direction dynamically, you can do:
  // config.hour.speed = 2; // 2x speed
  // config.minute.direction = -1; // counterclockwise


});