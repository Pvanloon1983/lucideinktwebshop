import axios from 'axios';

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
  } catch (_) {}

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
  // Loader button logic
  // ------------------------------------------------------------
  const setupLoaderForForm = (form) => {
    const button = form.querySelector('button[type="submit"]');
    const loader = button?.querySelector('.loader');
    if (!button || !loader) return;

    form.addEventListener('submit', () => {
      loader.style.display = 'inline-block';
      button.disabled = true;
    });

    loader.style.display = 'none';
    button.disabled = false;
  };

  document.querySelectorAll('form').forEach(setupLoaderForForm);

  new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1) {
          if (node.tagName === 'FORM') setupLoaderForForm(node);
          else node.querySelectorAll?.('form').forEach(setupLoaderForForm);
        }
      });
    });
  }).observe(document.body, { childList: true, subtree: true });

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

      try { hardResetMyParcelState(); } catch (_) {}
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
  window.showConfirmModal = (message, onConfirm) => {
    if (confirmModalOpen) return;
    confirmModalOpen = true;

    document.querySelectorAll('.custom-confirm-modal').forEach((m) => m.remove());

    const modal = document.createElement('div');
    modal.className = 'custom-confirm-modal';
    modal.innerHTML = `
      <div class="custom-confirm-modal-backdrop"></div>
      <div class="custom-confirm-modal-content">
        <div class="custom-confirm-modal-message">${message}</div>
        <div class="custom-confirm-modal-actions">
          <button class="btn confirm-btn" type="button">Ja, verwijderen</button>
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
    };
    modal.querySelector('.cancel-btn').onclick =
      modal.querySelector('.custom-confirm-modal-backdrop').onclick = () => {
        confirmModalOpen = false;
        modal.remove();
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
      if (!code) {
        discountMsg.textContent = 'Vul een kortingscode in.';
        discountMsg.style.color = '#b30000';
        return;
      }

      if (loader) loader.style.display = 'inline-block';
      discountButton.disabled = true;

      axios.post('/winkel/checkout/apply-discount-code', { code }, {
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
        .catch(() => {
          discountMsg.textContent = 'Er is een fout opgetreden.';
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


  // --- MyParcel Delivery Options Widget Integration (event-based, robust) ---
  const WIDGET_SELECTOR = '#myparcel-delivery-options';

  function currentAddress() {
    const useAlt = document.getElementById('alt-shipping')?.checked;
    const p = useAlt ? 'shipping_' : 'billing_';
    const street = document.querySelector(`[name="${p}street"]`)?.value || '';
    const nr = document.querySelector(`[name="${p}housenumber"]`)?.value || '';
    return {
      cc: document.querySelector(`[name="${p}country"]`)?.value || 'NL',
      postalCode: (document.querySelector(`[name="${p}postal_code"]`)?.value || '').replace(/\s+/g,'').toUpperCase(),
      number: nr,
      street: street && nr ? `${street} ${nr}` : street,
      city: document.querySelector(`[name="${p}city"]`)?.value || '',
    };
  }

  function addressComplete(a) {
    return a.cc && a.postalCode && a.number && a.street && a.city;
  }

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

   const configuration = {
     selector: WIDGET_SELECTOR,
     address: addr, // verwacht object: { cc, postalCode, street, number, city }
     config: {
       platform: 'myparcel',
       locale: 'nl',
       packageType: 'package',
       dropOffDelay: 1,
       deliveryDaysWindow: 0, // 🔑 0 dagen = geen datum selectie
       allowPickupLocationsViewSelection: false, // geen lijst/kaart switch
       pickupLocationsDefaultView: 'list',
       showPriceZeroAsFree: true,

       carrierSettings: {
         postnl: {
           allowDeliveryOptions: true,
           allowStandardDelivery: true,

           // alle andere opties uit
           allowExpressDelivery: false,
           allowSameDayDelivery: false,
           allowSaturdayDelivery: false,
           allowMorningDelivery: false,
           allowEveningDelivery: false,
           allowMondayDelivery: false,

           allowOnlyRecipient: false,
           allowSignature: false,

           allowPickupLocations: true // zet dit op true als je pickup wilt toestaan
         }
       }
     },
     strings: {
       deliveryTitle: 'Bezorgopties',
       pickupTitle: 'Ophalen bij een afleverpunt',
       deliveryStandard: 'Standaard bezorging',
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
       deliveryStandardTitle: 'Standaard bezorging'
     }
   };

  // Dispatch naar de widget
  document.dispatchEvent(new CustomEvent('myparcel_update_delivery_options', { detail: configuration }));

  // Luister naar de update van de widget en vul hidden input
  document.addEventListener('myparcel_updated_delivery_options', (e) => {
    console.log('[MyParcel] updated_delivery_options event:', e.detail);
    const input = ensureMyParcelInput();
    input.value = e.detail ? JSON.stringify(e.detail) : '';
  });

}

  // Attach listeners to address fields
  [
    'billing_country','billing_postal-zip-code','billing_street','billing_housenumber','billing_city',
    'shipping_country','shipping_postal-zip-code','shipping_street','shipping_housenumber','shipping_city',
    'alt-shipping'
  ].forEach(name => {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) {
      el.addEventListener('input', dispatchMyParcel);
      el.addEventListener('change', dispatchMyParcel);
    }
  });

  dispatchMyParcel();

  // On form submit, ensure input is present and not empty
  const formCheck = document.querySelector('.form.checkout');
  if (formCheck) {
    formCheck.addEventListener('submit', function(e) {
      const input = ensureMyParcelInput();
      if (!input.value || input.value === '{}' || input.value === 'null') {
        e.preventDefault();
        alert('Kies een bezorgoptie voordat je de bestelling plaatst.');
        const submitBtn = formCheck.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
      }
    });
  }

  const formOrder = document.querySelector('.form.order');
  if (formOrder) {
    formOrder.addEventListener('submit', function(e) {
      const input = ensureMyParcelInput();
      if (!input.value || input.value === '{}' || input.value === 'null') {
        e.preventDefault();
        alert('Kies een bezorgoptie voordat je de bestelling plaatst.');
        const submitBtn = formOrder.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
      }
    });
  }

});
