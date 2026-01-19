<!-- Shared modals and helpers for pricing pages -->
<div id="postcode-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black opacity-50" data-action="close-modal"></div>
  <div class="bg-white rounded shadow-lg w-full max-w-2xl mx-4 z-10 overflow-auto">
    <div class="p-4 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold" id="modal-title">Add</h3>
      <button data-action="close-modal" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>
    <div class="p-4" id="postcode-modal-body">
      <!-- form injected here -->
    </div>
  </div>
</div>

<!-- Confirmation modal (used for deletes) -->
<div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black opacity-50" data-action="close-confirm"></div>
  <div class="bg-white rounded shadow-lg w-full max-w-md mx-4 z-10">
    <div class="p-4 border-b">
      <h3 class="text-lg font-semibold">Confirm</h3>
    </div>
    <div class="p-4" id="confirm-body">Are you sure?</div>
    <div class="p-4 border-t flex justify-end gap-2">
      <button id="confirm-cancel" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
      <button id="confirm-ok" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
    </div>
  </div>
</div>

<!-- Alert modal (used for important validation messages like overlapping mileage) -->
<div id="alert-modal" class="hidden fixed inset-0 flex items-center justify-center" style="z-index:999999;">
  <div class="absolute inset-0 bg-black opacity-50" data-action="close-alert"></div>
  <div class="bg-white rounded shadow-lg w-full max-w-md mx-4" style="z-index:100000;">
    <div class="p-4 border-b">
      <h3 id="alert-title" class="text-lg font-semibold">Notice</h3>
    </div>
    <div class="p-4" id="alert-body">Message</div>
    <div class="p-4 border-t flex justify-end">
      <button data-action="close-alert" class="px-4 py-2 bg-indigo-600 text-white rounded">Close</button>
    </div>
  </div>
</div>

<!-- Toast container -->
<div id="toast-container" class="fixed top-6 right-6 z-50 space-y-2"></div>

<script>
(function(){
  // runInjectedScripts: execute inline scripts found in injected HTML
  if (typeof window.runInjectedScripts === 'undefined') {
    window.runInjectedScripts = function(container){
      try {
        var scripts = container.querySelectorAll('script');
        scripts.forEach(function(s, i){
          var ns = document.createElement('script');
          if (s.src) { ns.src = s.src; ns.async = false; document.head.appendChild(ns); ns.onload=function(){}; ns.onerror=function(e){ console.error('runInjectedScripts failed to load', s.src, e); } }
          else { ns.text = s.textContent; document.head.appendChild(ns); }
          s.parentNode.removeChild(s);
        });
      } catch(e){ console.error('runInjectedScripts error', e); }
    };
  }

  // showToast helper
  if (typeof window.showToast === 'undefined') {
    window.showToast = function(message){
      var container = document.getElementById('toast-container'); if (!container) return;
      var t = document.createElement('div'); t.className = 'bg-black text-white px-4 py-2 rounded shadow'; t.style.opacity='0'; t.textContent = message; container.appendChild(t);
      requestAnimationFrame(function(){ t.style.opacity='1'; t.style.transition='opacity 200ms'; });
      setTimeout(function(){ t.style.opacity='0'; setTimeout(function(){ t.remove(); }, 300); }, 2500);
    };
  }

  // showAlert helper: displays the alert modal with title and message
  if (typeof window.showAlert === 'undefined') {
    window.showAlert = function(title, message){
      try {
        console.log('showAlert called', title, message);
        var modal = document.getElementById('alert-modal');
        var body = document.getElementById('alert-body');
        var head = document.getElementById('alert-title');
        if (!modal || !body || !head) { console.warn('Alert modal elements missing'); return; }

        // Append to document.body to avoid stacking context issues when other modals are present
        if (modal.parentNode !== document.body) {
          console.log('Moving alert modal to document.body (was under):', modal.parentNode && modal.parentNode.id);
          document.body.appendChild(modal);
        }

        // Force position and sizing to cover viewport and center the panel
        try {
          modal.style.position = 'fixed';
          modal.style.left = '0';
          modal.style.top = '0';
          modal.style.right = '0';
          modal.style.bottom = '0';
          modal.style.display = 'flex';
          modal.style.alignItems = 'center';
          modal.style.justifyContent = 'center';
          modal.style.zIndex = '2147483647';
        } catch(e){}

        var backdrop = modal.querySelector('.absolute.inset-0') || modal.querySelector('[data-action="close-alert"]');
        if (backdrop) try { backdrop.style.zIndex = '2147483646'; backdrop.style.position='absolute'; backdrop.style.left='0'; backdrop.style.top='0'; backdrop.style.right='0'; backdrop.style.bottom='0'; } catch(e){}
        var panel = modal.querySelector('.bg-white') || modal.children[1];
        if (panel) try { panel.style.zIndex = '2147483647'; panel.style.position='relative'; panel.style.margin = '0'; } catch(e){}

        // Diagnostics: log computed styles and rect
        try {
          var cs = window.getComputedStyle(modal);
          console.log('alert-modal computed display, visibility, zIndex, classes:', cs.display, cs.visibility, cs.zIndex, modal.className);
          var panelRect = panel ? panel.getBoundingClientRect() : null;
          console.log('alert-modal parent, appended:', modal.parentNode && modal.parentNode.tagName, 'panelRect:', panelRect);
        } catch(e){ console.warn('Diagnostics failed', e); }

        head.textContent = title || 'Notice';
        // allow simple HTML content for clarity
        body.innerHTML = message || '';

        // Remove 'hidden' and ensure visible
        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        // Move keyboard focus to the close button for accessibility
        var closeBtn = modal.querySelector('[data-action="close-alert"]');
        if (closeBtn && typeof closeBtn.focus === 'function') closeBtn.focus();

        console.log('showAlert completed; modal should be visible');
      } catch(e){ console.error('showAlert failed', e); }
    };

    // close alert by clicking overlay or close button
    document.addEventListener('click', function(e){ var el = e.target.closest('[data-action="close-alert"]'); if (!el) return; var modal = document.getElementById('alert-modal'); if (modal && !modal.classList.contains('hidden')) { modal.classList.add('hidden'); modal.style.display = 'none'; var body = document.getElementById('alert-body'); if (body) body.innerHTML = ''; } });
  }

  // attachModalFormHandlers: submit form via AJAX and handle responses
  if (typeof window.attachModalFormHandlers === 'undefined') {
    window.attachModalFormHandlers = function(form){
      if (!form || form.dataset.ajaxAttached) return; form.dataset.ajaxAttached = '1';
      form.addEventListener('submit', function(e){
        e.preventDefault();
        var url = form.getAttribute('action');
        var fd = new FormData(form);
        // clear previous errors
        form.querySelectorAll('.text-red-600.text-sm').forEach(function(el){ el.remove(); });
        fetch(url, { method: form.getAttribute('method') || 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' }).then(function(res){
          if (res.status === 201 || res.status === 200) {
            // success
            var modal = document.getElementById('postcode-modal'); if (modal) modal.classList.add('hidden'); var body = document.getElementById('postcode-modal-body'); if (body) body.innerHTML = '';
            res.json().then(function(json){
              try {
                if (json && json.option_html && json.item) {
                  var id = json.item.id; var optionHtml = json.option_html;
                  document.querySelectorAll('select[name="from_zone_id"], select[name="to_zone_id"]').forEach(function(sel){
                    var existing = sel.querySelector('option[value="'+id+'"]'); if (existing) existing.outerHTML = optionHtml; else sel.insertAdjacentHTML('beforeend', optionHtml);
                  });
                  if (typeof window.showToast === 'function') window.showToast('Saved');
                }
              } catch(e){ console.error('Option insert error', e); }

              try {
                // dispatch a generic success event so any page can react (e.g., refresh lists)
                try { window.dispatchEvent(new CustomEvent('modal:success', { detail: json })); } catch(e) { console.warn('Failed to dispatch modal:success event', e); }

                var fid = form && form.id ? form.id.toLowerCase() : '';
                if (fid.indexOf('mileage') !== -1 && typeof window.refreshMileage === 'function') window.refreshMileage();
                else if (fid.indexOf('zone') !== -1) {
                  try {
                    // Soft refresh: refresh the zone charge list and map if present
                    if (typeof window.refreshZones === 'function') window.refreshZones();
                    if (typeof window.loadMap === 'function' && document.getElementById('map-container')) window.loadMap();
                  } catch(e) {
                    try { window.location.reload(); } catch(e) { console.warn('Reload failed', e); }
                  }
                }
                else if (fid.indexOf('postcode') !== -1 && typeof window.refreshPostcodes === 'function') window.refreshPostcodes();
              } catch(e){ console.error('Refresh error', e); }
            }).catch(function(err){ console.error('JSON parse error', err); });
            return;
          }
          if (res.status === 422) {
            // Read response text first (works for JSON or plain text responses) and always show an alert
            return res.text().then(function(txt){
              console.warn('Received 422 response body:', txt);
              var parsed = null;
              try { parsed = txt && txt.length ? JSON.parse(txt) : null; } catch(e) { parsed = null; }

              var combined = [];
              if (parsed && parsed.errors) {
                Object.keys(parsed.errors).forEach(function(field){
                  var input = form.querySelector('[name="'+field+'"]');
                  if (input) { var err = document.createElement('div'); err.className='text-red-600 text-sm'; err.textContent = parsed.errors[field][0]; input.parentNode.insertBefore(err, input.nextSibling); }
                  parsed.errors[field].forEach(function(m){ if (combined.indexOf(m) === -1) combined.push(m); });
                });
              }

              var messageHtml = '';
              if (parsed && parsed.message) messageHtml = parsed.message;
              else if (combined.length) messageHtml = '<ul class="list-disc pl-4">' + combined.map(function(m){ return '<li>' + (m) + '</li>'; }).join('') + '</ul>';
              else messageHtml = txt || 'Validation failed';

              console.log('Showing alert for 422 with messageHtml:', messageHtml);
              if (typeof window.showAlert === 'function') window.showAlert('Validation Error', messageHtml);
              else alert(messageHtml);

              throw new Error('Validation');
            });
          }
          return res.text().then(function(t){ throw new Error(t || 'Error'); });
        }).catch(function(err){ console.error('Submit error', err); });
      });
    };
  }

  // attachDeleteHandler: show confirm modal and perform AJAX delete
  if (typeof window.attachDeleteHandler === 'undefined') {
    window.attachDeleteHandler = function(form){
      if (form.dataset.deleteAttached) return; form.dataset.deleteAttached = '1';
      var methodInput = form.querySelector('input[name="_method"]'); if (!methodInput || methodInput.value.toUpperCase() !== 'DELETE') return;
      form.addEventListener('submit', function(e){ e.preventDefault(); var confirmModal = document.getElementById('confirm-modal'); var confirmBody = document.getElementById('confirm-body'); var okBtn = document.getElementById('confirm-ok'); var cancelBtn = document.getElementById('confirm-cancel'); var confirmBtn = form.querySelector('button[data-confirm]'); var confirmMsg = (confirmBtn && confirmBtn.dataset && confirmBtn.dataset.confirm) ? confirmBtn.dataset.confirm : 'Delete?'; confirmBody.textContent = confirmMsg; confirmModal.classList.remove('hidden'); var done = false; function doDelete(){ if (done) return; done=true; okBtn.disabled=true; var fd = new FormData(form); fetch(form.getAttribute('action'), { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }).then(function(res){ if (res.ok) { var row = form.closest('tr'); if (row) row.parentNode.removeChild(row); showToast('Deleted'); } else { res.json().then(function(json){ alert((json && json.message) ? json.message : 'Delete failed'); }).catch(function(){ alert('Delete failed'); }); } }).catch(function(err){ alert('Delete failed'); }).finally(function(){ okBtn.disabled=false; confirmModal.classList.add('hidden'); }); }
      function cleanup(){ okBtn.removeEventListener('click', okHandler); cancelBtn.removeEventListener('click', cancelHandler); }
      function okHandler(e){ e.preventDefault(); cleanup(); doDelete(); }
      function cancelHandler(e){ e.preventDefault(); cleanup(); confirmModal.classList.add('hidden'); }
      okBtn.addEventListener('click', okHandler); cancelBtn.addEventListener('click', cancelHandler);
      });
    };
  }

  // modal openers
  if (typeof window.openPostcodeModal === 'undefined') {
    window.openPostcodeModal = function(url, title){
      var modal = document.getElementById('postcode-modal'); var body = document.getElementById('postcode-modal-body'); var modalTitle = document.getElementById('modal-title'); if (modalTitle && title) modalTitle.textContent = title || 'Add'; modal.classList.remove('hidden'); if (body) body.innerHTML = '<div class="text-gray-600">Loading form...</div>';
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){ var frag = document.createElement('div'); frag.innerHTML = html; var form = frag.querySelector('#postcode-create-form') || frag.querySelector('#mileage-form') || frag.querySelector('form'); if (form) { if (frag.querySelector('script') || frag.querySelector('#zone-map')) { body.innerHTML = html; runInjectedScripts(body); var appendedForm = body.querySelector('#postcode-create-form') || body.querySelector('#mileage-form') || body.querySelector('form'); if (appendedForm) attachModalFormHandlers(appendedForm); } else { body.innerHTML = ''; body.appendChild(form); attachModalFormHandlers(form); } } else { body.innerHTML = html; runInjectedScripts(body); } }).catch(function(){ if (body) body.innerHTML = '<div class="text-red-600">Failed to load form.</div>'; });
      modal.querySelectorAll('[data-action="close-modal"]').forEach(function(el){ el.addEventListener('click', function(){ modal.classList.add('hidden'); body.innerHTML = ''; }); });
    };
  }

  if (typeof window.openMileageModal === 'undefined') {
    window.openMileageModal = function(url){ window.openPostcodeModal(url, 'Mileage'); };
  }

  if (typeof window.openOtherModal === 'undefined') {
    window.openOtherModal = function(url){ window.openPostcodeModal(url, 'Other'); };
  }

  // delegate: close modal elements with data-action="close-modal"
  document.addEventListener('click', function(e){ var el = e.target.closest('[data-action="close-modal"]'); if (!el) return; var modal = document.getElementById('postcode-modal'); if (modal && !modal.classList.contains('hidden')) { modal.classList.add('hidden'); var body = document.getElementById('postcode-modal-body'); if (body) body.innerHTML = ''; } });
})();
</script>