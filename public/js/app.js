// TechHouse frontend — small progressive enhancements.

document.addEventListener('DOMContentLoaded', () => {
  // Auto-dismiss flash messages after 4s.
  document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
  });

  // Confirm destructive actions that didn't already get a confirm handler.
  document.querySelectorAll('a.danger:not([onclick])').forEach(a => {
    a.addEventListener('click', e => {
      if (!confirm('Are you sure?')) e.preventDefault();
    });
  });

  // Live filter form: auto-submit on select change inside .filters.
  document.querySelectorAll('.filters select').forEach(sel => {
    sel.addEventListener('change', () => sel.form.submit());
  });

  // Dual-handle price range slider.
  document.querySelectorAll('[data-price-slider]').forEach(slider => {
    const min  = parseInt(slider.dataset.min, 10);
    const max  = parseInt(slider.dataset.max, 10);
    const span = Math.max(1, max - min);
    const minR = slider.querySelector('[data-price-min]');
    const maxR = slider.querySelector('[data-price-max]');
    const minH = slider.querySelector('[data-price-min-input]');
    const maxH = slider.querySelector('[data-price-max-input]');
    const minL = slider.querySelector('[data-price-min-label]');
    const maxL = slider.querySelector('[data-price-max-label]');
    const fill = slider.querySelector('[data-price-fill]');
    const fmt  = (n) => n.toLocaleString('fr-FR');

    const apply = (e) => {
      let lo = parseInt(minR.value, 10);
      let hi = parseInt(maxR.value, 10);
      if (lo > hi) {
        if (e && e.target === minR) { lo = hi; minR.value = lo; }
        else                        { hi = lo; maxR.value = hi; }
      }
      fill.style.left  = (((lo - min) / span) * 100) + '%';
      fill.style.right = (100 - ((hi - min) / span) * 100) + '%';
      minL.textContent = fmt(lo);
      maxL.textContent = fmt(hi);
      minH.value = lo;
      maxH.value = hi;
    };

    // Lift active handle so overlapping thumbs stay grabbable.
    const lift = (active) => {
      minR.classList.toggle('is-top', active === minR);
      maxR.classList.toggle('is-top', active === maxR);
    };
    ['mousedown','touchstart','focus'].forEach(ev => {
      minR.addEventListener(ev, () => lift(minR));
      maxR.addEventListener(ev, () => lift(maxR));
    });

    ['input','change'].forEach(ev => {
      minR.addEventListener(ev, apply);
      maxR.addEventListener(ev, apply);
    });
    apply();
  });

  // Theme toggle: flips data-theme on <html> and persists to localStorage.
  const themeBtn = document.getElementById('themeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      try { localStorage.setItem('theme', next); } catch (e) {}
    });
  }

  // Checkout: live-recompute shipping fee + grand total when option changes.
  const checkoutForm = document.getElementById('checkoutForm');
  if (checkoutForm) {
    const feeEl   = document.getElementById('checkoutShippingFee');
    const totalEl = document.getElementById('checkoutTotal');
    const grandBtnEl = document.getElementById('checkoutGrandTotal');
    const subtotal = parseFloat(feeEl.dataset.subtotal) || 0;
    const discount = parseFloat(feeEl.dataset.discount) || 0;
    const freeShip = feeEl.dataset.freeship === '1';
    const fmt = (n) => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const sync = () => {
      const selected = checkoutForm.querySelector('input[name="shipping"]:checked');
      let fee = selected ? parseFloat(selected.dataset.fee) || 0 : 0;
      if (freeShip && selected && selected.value === 'standard') fee = 0;
      feeEl.textContent = fee === 0 ? 'Gratuit' : (fmt(fee) + ' DH');
      const total = Math.max(0, subtotal - discount) + fee;
      totalEl.textContent = fmt(total);
      if (grandBtnEl) grandBtnEl.textContent = fmt(total);
    };
    checkoutForm.querySelectorAll('input[name="shipping"]').forEach(r => r.addEventListener('change', sync));
    sync();
  }

  // Profile dropdown: open/close, outside-click & Escape to dismiss.
  const profileMenu = document.querySelector('[data-profile-menu]');
  if (profileMenu) {
    const trigger = profileMenu.querySelector('.profile-trigger');
    const dropdown = profileMenu.querySelector('.profile-dropdown');
    const open = () => {
      dropdown.hidden = false;
      requestAnimationFrame(() => profileMenu.classList.add('is-open'));
      trigger.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
      profileMenu.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
      setTimeout(() => { if (!profileMenu.classList.contains('is-open')) dropdown.hidden = true; }, 200);
    };
    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.contains('is-open') ? close() : open();
    });
    document.addEventListener('click', (e) => {
      if (profileMenu.classList.contains('is-open') && !profileMenu.contains(e.target)) close();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && profileMenu.classList.contains('is-open')) {
        close(); trigger.focus();
      }
    });
  }
});
