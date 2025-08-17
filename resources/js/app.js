document.addEventListener('DOMContentLoaded', function () {

  // -------------------- Menu side bar --------------------
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

  // -------------------- Dashboard side bar --------------------
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

  // -------------------- Image pickers (1..4) --------------------
  for (let i = 1; i <= 4; i++) {
    const input = document.getElementById('image_' + i);
    const label = document.getElementById('image_' + i + '_label_text');
    const removeBtn = document.querySelector('[data-input="image_' + i + '"]');
    const preview = document.getElementById('image_' + i + '_preview');
    const deleteCheckbox = document.getElementById('delete_image_' + i);

    if (!input) continue;

    input.addEventListener('change', function (e) {
      if (e.target.files.length) {
        const file = e.target.files[0];
        if (label) label.textContent = file.name;
        const reader = new FileReader();
        reader.onload = function (ev) {
          if (preview) preview.innerHTML = '<img src="' + ev.target.result + '" style="max-width:60px;max-height:60px;">';
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
      removeBtn.addEventListener('click', function () {
        input.value = "";
        if (label) label.textContent = "Kies afbeelding...";
        if (preview) preview.innerHTML = '';
        removeBtn.style.display = 'none';
        if (deleteCheckbox) deleteCheckbox.checked = true;
      });
    }
  }

  // -------------------- Loader button logic --------------------
  document.querySelectorAll('form').forEach(function (form) {
    const button = form.querySelector('button[type="submit"]');
    const loader = button ? button.querySelector('.loader') : null;

    if (button && loader) {
      form.addEventListener('submit', function () {
        loader.style.display = 'inline-block';
        button.disabled = true;
      });
      // On page load, hide loader and enable button
      loader.style.display = 'none';
      button.disabled = false;
    }
  });

  // -------------------- Alt shipping panel --------------------
  const altInput = document.querySelector('#alt-shipping');
  const altShipping = document.querySelector('.customer-details.alternate');

  if (altInput && altShipping) {
    altInput.addEventListener('change', function () {
      altShipping.classList.toggle('open');

      // Clear all shipping fields when unchecked
      if (!altInput.checked) {
        altShipping.querySelectorAll('input, select').forEach(function (field) {
          if (field.type === 'checkbox' || field.type === 'radio') {
            field.checked = false;
          } else {
            field.value = '';
          }
        });
      }

  // Hard reset and schedule a single debounced rebuild
  try { hardResetMyParcelState(); } catch (_) {}
  schedulePickupRebuild(260);
    });
  }

  // -------------------- Order calc (instant) --------------------
  (function initOrderCalc() {
    const qtyInputs = document.querySelectorAll('.qty-input');
    if (!qtyInputs.length) return; // Not on order create page

    const totalEl = document.getElementById('total-price');
    const discountedEl = document.getElementById('discounted-total');
    const discountValueEl = document.getElementById('discount_value');
    const discountTypeEl = document.getElementById('discount_type');

    function formatEuro(val) {
      return '€ ' + val.toFixed(2).replace('.', ',');
    }

    function updatePrices() {
      let total = 0;
      qtyInputs.forEach(input => {
        const qty = parseFloat(input.value) || 0;
        const price = parseFloat(input.getAttribute('data-price')) || 0;
        const id = input.getAttribute('data-id');
        const subtotal = qty * price;
        total += subtotal;
        const subItem = document.getElementById('sub-item-price-' + id);
        if (subItem) subItem.innerText = qty > 0 ? formatEuro(subtotal) : '';
      });

      if (totalEl) totalEl.innerText = total > 0 ? 'Totaal: ' + formatEuro(total) : '';

      if (discountValueEl && discountTypeEl && discountedEl) {
        const discountValue = parseFloat(discountValueEl.value) || 0;
        const discountType = discountTypeEl.value;
        let discountedTotal = total;
        if (discountValue > 0) {
          discountedTotal = discountType === 'percent'
            ? total - (total * (discountValue / 100))
            : total - discountValue;
          if (discountedTotal < 0) discountedTotal = 0;
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
    if (discountValueEl) { discountValueEl.addEventListener('input', updatePrices); }
    if (discountTypeEl) { discountTypeEl.addEventListener('change', updatePrices); }
    updatePrices();
  })();

  // ==================== MyParcel: robust init & refresh ====================

  // Config from backend (same as in Blade): platform + locale
  const PLATFORM = (typeof window.MyParcelPlatform !== 'undefined')
    ? window.MyParcelPlatform
    : (document.querySelector('meta[name="myparcel-platform"]')?.content || 'myparcel');
  const LOCALE = 'nl';
  // Toggle between map and list-only. Set to false to completely avoid Leaflet.
  const USE_MAP = false;

    // Debounce + in-flight guard to prevent multiple mounts/loads
    let __mpRefreshTimer = null;
    let __mpLoading = false; // true while a pickup widget load is in-flight
    function schedulePickupRebuild(delay = 240) {
      if (__mpLoading) return; // skip scheduling while loading
      if (__mpRefreshTimer) clearTimeout(__mpRefreshTimer);
      __mpRefreshTimer = setTimeout(() => {
        if (__mpLoading) return; // re-check guard
        toggleShippingBlocks();
        const mode = document.querySelector('input[name="ship_mode"]:checked')?.value || 'delivery';
        if (mode === 'pickup') {
          if (USE_CUSTOM_PICKUP) { mountCustomPickupWidget(); }
          else { mountPickupWidget(); }
        } else {
          setDeliverySelection();
        }
      }, delay);
    }
  // Use our own simple pickup widget that loads locations from a JSON file.
  const USE_CUSTOM_PICKUP = true;

  const $ = (sel, root = document) => root.querySelector(sel);

  function getTrim(name) {
    const el = document.querySelector(`[name="${name}"]`);
    return (el?.value || '').trim();
  }

  function currentAddress() {
    const alt = document.querySelector('input[name="alt-shipping"]')?.checked;
    const cc       = alt ? (getTrim('shipping_country') || 'NL') : (getTrim('billing_country') || 'NL');
    const postal   = (alt ? getTrim('shipping_postal-zip-code') : getTrim('billing_postal-zip-code')).replace(/\s+/g, '').toUpperCase();
    const street   = alt ? getTrim('shipping_street') : getTrim('billing_street');
    const number   = alt ? getTrim('shipping_housenumber') : getTrim('billing_housenumber');
    const suffix   = alt ? getTrim('shipping_housenumber-add') : getTrim('billing_housenumber-add');
    const city     = alt ? getTrim('shipping_city') : getTrim('billing_city');
    // Gate on required fields for pickup lookup: cc + postal + number
    if (!number || !postal || !cc) return null;
    return {
      cc,
      postalCode: postal,
      number,
      street: street ? `${street} ${number}${suffix ? ' ' + suffix : ''}` : '',
      city: city || ''
    };
  }

  function setDeliverySelection() {
    const hidden = document.getElementById('myparcel_delivery_options');
    if (hidden) hidden.value = JSON.stringify({ carrier: 'postnl', deliveryType: 'standard', isPickup: false });
  }

  function resetMyParcelContainer() {
    const widget = document.getElementById('myparcel-delivery-options');
    if (widget) {
      widget.innerHTML = '';      // drop old DOM
      widget.style.display = '';
      widget.style.minHeight = '340px';
    }
        // Strong reset then schedule a single debounced rebuild
        try { hardResetMyParcelState(); } catch (_) {}
        schedulePickupRebuild(260);
  }

  // Strong reset similar to WooCommerce behavior: replace node, clear state & storage
  function hardResetMyParcelState() {
    // 1) Clear hidden selection + messages
    const hidden = document.getElementById('myparcel_delivery_options');
    const msg = document.getElementById('myparcel-address-message');
    const err = document.getElementById('myparcel-error');
    if (hidden) hidden.value = '';
    if (msg) { msg.style.display = 'none'; msg.textContent = ''; }
    if (err) err.textContent = '';

    // 2) Replace the widget node to drop any listeners/state held by the lib
    const oldNode = document.getElementById('myparcel-delivery-options');
    if (oldNode && oldNode.parentNode) {
      const fresh = document.createElement('div');
      fresh.id = 'myparcel-delivery-options';
      // no enforced min-height; let content control height
      oldNode.parentNode.replaceChild(fresh, oldNode);
    }

    // 3) Drop in-memory config if present
    try { if (window.MyParcelConfig) delete window.MyParcelConfig; } catch (_) {}

    // 4) Purge likely related storage keys
    try {
      const clearKeys = (storage) => {
        if (!storage) return;
        const keys = [];
        for (let i = 0; i < storage.length; i++) keys.push(storage.key(i));
        keys.forEach((k) => {
          if (!k) return;
          if (/myparcel|delivery\-options|pdk/i.test(k)) storage.removeItem(k);
        });
      };
      clearKeys(window.localStorage);
      clearKeys(window.sessionStorage);
    } catch (_) {}
  }

  function isElementVisible(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    const style = window.getComputedStyle(el);
    return (
      style.display !== 'none' &&
      style.visibility !== 'hidden' &&
      rect.width > 0 &&
      rect.height > 0
    );
  }

  function mountPickupWidget(retry = 0) {
  if (typeof __mpLoading !== 'undefined' && __mpLoading) return;
  if (typeof __mpLoading !== 'undefined') __mpLoading = true;
    const wrap = document.getElementById('myparcel-wrapper');
    const msg  = document.getElementById('myparcel-address-message');
    const addr = currentAddress();

    if (wrap) {
      wrap.style.display = '';
      wrap.style.visibility = 'visible';
      wrap.style.minHeight = '340px';
    }

    // If wrapper is not visible yet (e.g., due to CSS transitions), retry shortly
    if (wrap && !isElementVisible(wrap)) {
      if (retry < 12) {
  if (typeof __mpLoading !== 'undefined') __mpLoading = false;
  return setTimeout(() => mountPickupWidget(retry + 1), 100 + retry * 50);
      }
    }
  // Fully reset before every mount to avoid stale, non-interactive instances
  hardResetMyParcelState();

    if (!addr) {
      if (msg) { msg.style.display = ''; msg.textContent = 'Vul eerst een volledig adres in om afhaalpunten te tonen.'; }
      if (typeof __mpLoading !== 'undefined') __mpLoading = false;
      return;
    }
    if (msg) { msg.style.display = 'none'; msg.textContent = ''; }

    const containerEl = document.getElementById('myparcel-delivery-options');
    if (typeof __mpLoading !== 'undefined' && __mpLoading) return;
    if (typeof __mpLoading !== 'undefined') __mpLoading = true;
    if (containerEl) {
      containerEl.style.minHeight = '340px';
      containerEl.style.visibility = 'visible';
      containerEl.style.opacity = '1';
    }

  const configuration = {
      selector: '#myparcel-delivery-options',
      address: addr,
      config: {
        platform: PLATFORM,
        locale: LOCALE,
        allowDeliveryOptions: false,
        allowPickupLocations: true,
  // Let the widget handle small address corrections internally (like Woo)
  allowRetry: true,
    pickupLocationsDefaultView: USE_MAP ? 'map' : 'list',
    allowPickupLocationsViewSelection: USE_MAP,
        carrierSettings: { postnl: {} },
        showDeliveryDate: false,
        showPrices: false,
        cutoffTime: '16:00',
        deliveryDaysWindow: 7,
      },
      // Nonce helps avoid stale internal caching after rapid toggles
      nonce: Date.now() + ':' + Math.random().toString(36).slice(2),
      strings: {
        pickupTitle: 'Ophalen bij een gekozen afhaalpunt',
        list: 'Lijst', map: 'Kaart', backToList: 'Terug naar lijst', loadMore: 'Meer laden', retry: 'Opnieuw proberen'
      }
    };

    // Dispatch to BOTH (create separate events; an Event cannot be dispatched twice)
    try {
      const evWin = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
      window.dispatchEvent(evWin);
    } catch (_) {}
    try {
      const evDoc = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
      document.dispatchEvent(evDoc);
    } catch (_) {}

    // Only do resize nudges if we use the map view
    if (USE_MAP) {
      [50, 120, 240, 400, 800].forEach((t) => setTimeout(() => window.dispatchEvent(new Event('resize')), t));
    }
    // Re-dispatch configuration once after layout settles (helps after toggling alt-shipping)
    setTimeout(() => {
      try {
        const ev2w = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
        window.dispatchEvent(ev2w);
      } catch (_) {}
      try {
        const ev2d = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
        document.dispatchEvent(ev2d);
      } catch (_) {}
  if (USE_MAP) setTimeout(() => window.dispatchEvent(new Event('resize')), 120);
    }, 350);

    // Verify render: if container still empty, try a few more times
    const container = document.getElementById('myparcel-delivery-options');
    const ensureRendered = (attempt = 0) => {
      if (!container) return;
      if (container.childElementCount > 0) return; // rendered
      if (attempt >= 6) return;
      // re-dispatch and nudge again
      try {
        const evW = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
        window.dispatchEvent(evW);
      } catch (_) {}
      try {
        const evD = new CustomEvent('myparcel_update_delivery_options', { detail: configuration });
        document.dispatchEvent(evD);
      } catch (_) {}
      if (USE_MAP) window.dispatchEvent(new Event('resize'));
      setTimeout(() => ensureRendered(attempt + 1), 200 + attempt * 100);
    };
  setTimeout(() => { try { ensureRendered(0); } finally { if (typeof __mpLoading !== 'undefined') __mpLoading = false; } }, 180);
  }

  function applyMode() {
    const mode = document.querySelector('input[name="ship_mode"]:checked')?.value || 'delivery';
    const wrap = document.getElementById('myparcel-wrapper');
    const manual = document.getElementById('manual-pickup');

    if (mode === 'delivery') {
      setDeliverySelection();
      if (wrap) wrap.style.display = 'none'; // hide widget on delivery
      if (manual) manual.style.display = 'none';
      return;
    }
    // pickup
    if (!USE_CUSTOM_PICKUP) {
      try { hardResetMyParcelState(); } catch (_) {}
      if (wrap) {
        wrap.style.display = '';
        wrap.style.visibility = 'visible';
        // no minHeight here
        wrap.style.width = '100%';
      }
      if (manual) manual.style.display = '';
      mountPickupWidget();
      return;
    }

    // Our custom widget path
    try { hardResetMyParcelState(); } catch (_) {}
    if (wrap) {
      wrap.style.display = '';
      wrap.style.visibility = 'visible';
      // no minHeight here; spinner/list will define height
      wrap.style.width = '100%';
    }
    if (manual) manual.style.display = 'none';
    mountCustomPickupWidget();
  }

  function toggleShippingBlocks() {
    const choice = document.getElementById('mp-choice-block');
    const wrap = document.getElementById('myparcel-wrapper');

    const addr = currentAddress();

    // Show the choice radios only when we have enough address info
    if (choice) choice.style.display = addr ? '' : 'none';

    const mode = document.querySelector('input[name="ship_mode"]:checked')?.value || 'delivery';
    if (mode === 'pickup' && addr) {
      if (wrap) {
        wrap.style.display = '';
        wrap.style.visibility = 'visible';
        wrap.style.width = '100%';
      }
    } else {
      if (wrap) {
        wrap.style.display = 'none';
        // Drop widget completely when leaving pickup mode or when address becomes invalid
        try { hardResetMyParcelState(); } catch (_) {}
      }
      // Clear any previous selection if we hide the widget
      const hidden = document.getElementById('myparcel_delivery_options');
      if (hidden) hidden.value = '';
    }
  }

  // Expose public API (your other code calls this)
  window.updateDeliveryOptionsFromForm = function () {
    schedulePickupRebuild(220);
  };

  // MyParcel events → persist selection / show error
  document.addEventListener('myparcel_updated_delivery_options', (e) => {
    const hidden = document.getElementById('myparcel_delivery_options');
    if (hidden) hidden.value = JSON.stringify(e.detail);
    const box = document.getElementById('myparcel-error'); if (box) box.textContent = '';
  });

  // Bind address change listeners → refresh widget when pickup active
  (function bindAddressListeners() {
    const names = [
      'shipping_country','shipping_postal-zip-code','shipping_housenumber','shipping_housenumber-add','shipping_street','shipping_city',
      'billing_country','billing_postal-zip-code','billing_housenumber','billing_housenumber-add','billing_street','billing_city',
      'alt-shipping'
    ];
    names.forEach(name => {
      const el = document.querySelector(`[name="${name}"]`) || document.getElementById(name);
      if (!el) return;
  const handler = () => schedulePickupRebuild(220);
      el.addEventListener('input', handler);
      el.addEventListener('change', handler);
    });
  })();

  // Ship mode radios
  document.querySelectorAll('input[name="ship_mode"]').forEach(r => r.addEventListener('change', function () {
    applyMode();
    toggleShippingBlocks();
  }));

  // Initial
  applyMode();
  toggleShippingBlocks();

  // Submit guard: if pickup → require selected pickup point
  const form = document.querySelector('form.form');
  form?.addEventListener('submit', (e) => {
    const mode = document.querySelector('input[name="ship_mode"]:checked')?.value;
    if (mode === 'pickup') {
      try {
        const data = JSON.parse(document.getElementById('myparcel_delivery_options').value || '{}');
        if (!data.pickup && !data.pickupLocation) {
          if (!USE_CUSTOM_PICKUP) {
            // MyParcel/manual fallback path (existing behavior)
            const p = {
              locationName: (document.getElementById('pickup_location_name')?.value || '').trim(),
              street:       (document.getElementById('pickup_street')?.value || '').trim(),
              number:       (document.getElementById('pickup_number')?.value || '').trim(),
              numberSuffix: (document.getElementById('pickup_numberSuffix')?.value || '').trim(),
              postalCode:   (document.getElementById('pickup_postalCode')?.value || '').trim(),
              city:         (document.getElementById('pickup_city')?.value || '').trim(),
            };
            const valid = p.street && p.number && p.postalCode && p.city;
            if (!valid) {
              e.preventDefault();
              document.getElementById('myparcel-error').textContent = 'Kies een afhaalpunt of vul de velden hieronder in.';
              return;
            }
            const hidden = document.getElementById('myparcel_delivery_options');
            if (hidden) hidden.value = JSON.stringify({ carrier: 'postnl', isPickup: true, deliveryType: 'pickup', pickup: p, shipmentOptions: {} });
          } else {
            e.preventDefault();
            document.getElementById('myparcel-error').textContent = 'Kies eerst een afhaalpunt uit de lijst.';
          }
        }
      } catch (_) {
        e.preventDefault();
        document.getElementById('myparcel-error').textContent = 'Kies eerst een afhaalpunt.';
      }
    }
  });

  // -------------------- Custom pickup widget (JSON data) --------------------
  async function mountCustomPickupWidget() {
    if (typeof __mpLoading !== 'undefined' && __mpLoading) return;
    if (typeof __mpLoading !== 'undefined') __mpLoading = true;
    let container = document.getElementById('myparcel-delivery-options');
    const errorBox = document.getElementById('myparcel-error');
    if (!container) return;

    // Replace the container node to ensure a clean slate
    try {
      const fresh = document.createElement('div');
      fresh.id = 'myparcel-delivery-options';
      container.parentNode.replaceChild(fresh, container);
      // use the fresh one from here
      container = fresh;
    } catch (_) {
      container.innerHTML = '';
    }

    // load initial locations based on current address only (no manual search)
    const addr = currentAddress();
    let locations = [];
    if (!addr || !addr.postalCode || !addr.number) {
      // hide entire wrapper when insufficient data
      const wrap = document.getElementById('myparcel-wrapper');
      if (wrap) wrap.style.display = 'none';
    if (typeof __mpLoading !== 'undefined') __mpLoading = false;
      return;
    }

    // Defensive: remove any stray duplicate containers/lists before proceeding
    try {
      const allContainers = document.querySelectorAll('#myparcel-delivery-options');
      if (allContainers.length > 1) {
        // keep the last one in DOM, remove earlier ones
        allContainers.forEach((el, idx) => { if (idx < allContainers.length - 1) el.remove(); });
      }
      document.querySelectorAll('#custom-pickup-list').forEach(el => el.remove());
    } catch (_) {}

    // Ensure wrapper is visible but do not enforce min-height; show a spinner while loading
    const wrap = document.getElementById('myparcel-wrapper');
    if (wrap) {
      wrap.style.display = '';
      wrap.style.visibility = 'visible';
      wrap.style.width = '100%';
    }

    // Inject spinner styles once
    (function ensureSpinnerStyles(){
      if (document.getElementById('pickup-spinner-style')) return;
      const style = document.createElement('style');
      style.id = 'pickup-spinner-style';
      style.textContent = `@keyframes mp-spin{to{transform:rotate(360deg)}} .mp-spinner{display:inline-block;width:22px;height:22px;border:3px solid #ddd;border-top-color:#b30000;border-radius:50%;animation:mp-spin 0.8s linear infinite;margin-right:.5rem;vertical-align:middle}`;
      document.head.appendChild(style);
    })();

    // Inject pickup card styles once
    (function ensurePickupCardStyles(){
      if (document.getElementById('pickup-card-style')) return;
      const style = document.createElement('style');
      style.id = 'pickup-card-style';
      style.textContent = `#custom-pickup-list .pickup-card{border:1px solid #e5e5e5;border-radius:6px;padding:.5rem .75rem;margin-bottom:.5rem;display:flex;justify-content:space-between;align-items:center;transition:border-color .15s, box-shadow .15s, background-color .15s} #custom-pickup-list .pickup-card.selected{border-color:#b30000;background:#fff8f8;box-shadow:0 0 0 2px rgba(179,0,0,0.08) inset}`;
      document.head.appendChild(style);
    })();

    const loadingRow = document.createElement('div');
    loadingRow.style.display = 'flex';
    loadingRow.style.alignItems = 'center';
    loadingRow.style.gap = '.5rem';
    const spinner = document.createElement('span');
    spinner.className = 'mp-spinner';
    const text = document.createElement('span');
    text.textContent = 'Afhaalpunten laden…';
    loadingRow.appendChild(spinner);
    loadingRow.appendChild(text);
    container.appendChild(loadingRow);

    let hadError = false;
    try {
      const params = new URLSearchParams();
      params.set('cc', (addr?.cc || 'NL'));
      params.set('postalCode', addr.postalCode);
      params.set('number', addr.number);
      params.set('carrier', '1');
      const res = await fetch('/pickup-locations?' + params.toString(), { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      locations = json.locations || [];
    } catch (err) {
      hadError = true;
      if (errorBox) errorBox.textContent = 'Kan afhaalpunten niet laden.';
    }

    // Remove spinner
    loadingRow.remove();

    if (hadError) {
      // Keep wrapper visible to show error message
        if (typeof __mpLoading !== 'undefined') __mpLoading = false;
      return;
    }

    // If we have no locations, hide the wrapper
    if (!locations.length) {
      if (wrap) wrap.style.display = 'none';
        if (typeof __mpLoading !== 'undefined') __mpLoading = false;
      return;
    }

    const list = document.createElement('div');
    list.id = 'custom-pickup-list';
    list.style.marginTop = '.5rem';
    list.style.maxHeight = '320px';
    list.style.overflowY = 'auto';
    list.style.paddingRight = '4px'; // keep content readable next to scrollbar
    container.appendChild(list);

  const render = () => {
      list.innerHTML = '';
      locations.slice(0, 25).forEach((p) => {
        const card = document.createElement('div');
        card.className = 'pickup-card';
        const left = document.createElement('div');
        left.innerHTML = '<div style="font-weight:600;">' + (p.locationName || '') + '</div>' +
          '<div style="color:#444;">' + [p.street, p.number, p.numberSuffix].filter(Boolean).join(' ') + '</div>' +
          '<div style="color:#666;">' + (p.postalCode || '') + ' ' + (p.city || '') + '</div>';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Kies';
        btn.className = 'btn';
        btn.style.whiteSpace = 'nowrap';
        btn.addEventListener('click', () => {
          // Persist full selection (incl. cc and retail network) and update UI state
          try { setPickupSelection(p); } catch(_) {}
          if (errorBox) errorBox.textContent = '';
          // Ensure only one selection: clear previous
          list.querySelectorAll('.pickup-card.selected').forEach(el => {
            el.classList.remove('selected');
            const b = el.querySelector('button');
            if (b) { b.textContent = 'Kies'; b.disabled = false; }
          });
          // Mark current as selected
          card.classList.add('selected');
          btn.textContent = 'Geselecteerd';
          btn.disabled = true;
        });
        card.appendChild(left);
        card.appendChild(btn);
        list.appendChild(card);
      });
    };

    render();
      if (typeof __mpLoading !== 'undefined') __mpLoading = false;
  }

  // When a card is selected, persist full pickup data (including cc and retail network) to hidden input
  function setPickupSelection(p) {
      const hidden = document.getElementById('myparcel_delivery_options');
      if (!hidden) return;
      const payload = {
          carrier: 'postnl',
          isPickup: true,
          deliveryType: 'pickup',
          pickup: {
              locationName: p.locationName,
              street: p.street,
              number: p.number,
              numberSuffix: p.numberSuffix || '',
              postalCode: p.postalCode,
              city: p.city,
              cc: p.cc || 'NL',
              retail_network_id: p.retail_network_id || '',
              location_code: p.location_code || '',
          }
      };
      hidden.value = JSON.stringify(payload);
      hidden.dispatchEvent(new Event('change'));
  }

  // Scroll effect header
  var header = document.querySelector('.header');
  var logo = document.querySelector('.logo-container');

  function handleScroll() {
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
      if (window.innerWidth > 992 && logo) {
        logo.style.display = 'none';
      }
    } else {
      header.classList.remove('scrolled');
      if (logo) {
        logo.style.display = '';
      }
    }
  }

  window.addEventListener('scroll', handleScroll);
  window.addEventListener('resize', function() {
    // Show logo again if resizing back above 992px and not scrolled
    if (window.innerWidth > 992 && window.scrollY <= 10 && logo) {
      logo.style.display = '';
    }
  });

});
