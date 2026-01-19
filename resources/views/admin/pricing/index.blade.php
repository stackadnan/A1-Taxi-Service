@extends('layouts.admin')

@section('title', 'Pricing')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Pricing</h1>

  @include('admin.pricing._tabs')

  <div id="pricing-tabs">
    <section data-pane="postcode" class="tab-pane">
      <h2 class="text-lg font-semibold mb-2">Postcode Charges</h2>
      <div id="postcode-container">
        <div class="text-gray-600">Loading postcode charges...</div>
      </div>
    </section>

    <script>
    (function(){
      var postcodeLoaded = false;

      function loadPostcodes(q) {
        var url = '{{ route('admin.pricing.postcodes.index') }}?partial=1' + (q ? '&q=' + encodeURIComponent(q) : '');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){
          if (!r.ok) return r.text().then(function(t){ throw new Error((r.status === 401 ? 'Authentication required' : ('Failed to load (status '+r.status+')')) + '\n' + t.slice(0,200)); });
          return r.text();
        }).then(function(html){
          var container = document.getElementById('postcode-container');
          container.innerHTML = html;
          postcodeLoaded = true;

          // attach search form submit handler (ajax)
          var form = container.querySelector('#postcode-search-form');
          if (form) {
            form.addEventListener('submit', function(e){
              e.preventDefault();
              var fd = new FormData(form);
              loadPostcodes(fd.get('q'));
            });
          }

          // unobtrusively handle pagination links
          container.querySelectorAll('.pagination a').forEach(function(a){
            a.addEventListener('click', function(e){
              var href = a.getAttribute('href');
              if (!href) return;
              e.preventDefault();
              fetch(href + '&partial=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){ container.innerHTML = html; });
            });
          });

          // attach delete handlers to do AJAX deletes so we stay inside tab
          container.querySelectorAll('form').forEach(function(form){
            attachDeleteHandler(form);
          });

          // small toast helper
          window.showToast = function(message){
            var container = document.getElementById('toast-container');
            if (!container) return;
            var t = document.createElement('div');
            t.className = 'bg-black text-white px-4 py-2 rounded shadow';
            t.style.opacity = '0';
            t.textContent = message;
            container.appendChild(t);
            // fade in
            requestAnimationFrame(function(){ t.style.opacity = '1'; t.style.transition = 'opacity 200ms'; });
            setTimeout(function(){ t.style.opacity = '0'; setTimeout(function(){ t.remove(); }, 300); }, 2500);
          }

          // attach Add Postcode event to open modal
          var addBtn = container.querySelector('#postcode-create-button');
          if (addBtn) {
            addBtn.addEventListener('click', function(e){
              e.preventDefault();
              openPostcodeModal(addBtn.getAttribute('href'));
            });
          }

          // attach edit handlers (open modal with edit form)
          container.querySelectorAll('.postcode-edit-button').forEach(function(btn){
            btn.addEventListener('click', function(e){
              e.preventDefault();
              openPostcodeModal(btn.getAttribute('href'));
            });
          });
        }).catch(function(err){
            console.error('Load postcodes error', err);
            var container = document.getElementById('postcode-container');
            container.innerHTML = '<div class="text-red-600">Failed to load postcode charges. <button id="postcode-retry" class="ml-2 px-2 py-1 border rounded">Retry</button></div>';
            var btn = document.getElementById('postcode-retry');
            if (btn) btn.addEventListener('click', function(){ loadPostcodes(); });
        });
      }

      // helper to execute inline scripts when we inject HTML
      window.runInjectedScripts = function(container){
        try {
          var scripts = container.querySelectorAll('script');
          console.debug('runInjectedScripts: found', scripts.length, 'scripts');
          scripts.forEach(function(s, i){
            var ns = document.createElement('script');
            if (s.src) {
              console.debug('runInjectedScripts: appending script src=', s.src);
              ns.src = s.src; ns.async = false; document.head.appendChild(ns);
              ns.onload = function(){ console.debug('runInjectedScripts: loaded', s.src); };
              ns.onerror = function(e){ console.error('runInjectedScripts: failed to load', s.src, e); };
            } else {
              console.debug('runInjectedScripts: inlining script #'+i);
              ns.text = s.textContent; document.head.appendChild(ns);
            }
            s.parentNode.removeChild(s);
          });
        } catch(e) { console.error('runInjectedScripts error', e); }
      };

      // expose refresh globally so modal can trigger it
      window.refreshPostcodes = loadPostcodes;

      window.openPostcodeModal = function(url, title) {
        var modal = document.getElementById('postcode-modal');
        var body = document.getElementById('postcode-modal-body');
        var modalTitle = document.getElementById('modal-title');
        modal.classList.remove('hidden');
        modalTitle.textContent = title || 'Add';
        body.innerHTML = '<div class="text-gray-600">Loading form...</div>';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){
          var frag = document.createElement('div'); frag.innerHTML = html;
          var form = frag.querySelector('#postcode-create-form') || frag.querySelector('#mileage-form') || frag.querySelector('form');
          // If HTML contains scripts or map container, set innerHTML so scripts run; otherwise append the form node only
          if (form) {
            if (frag.querySelector('script') || frag.querySelector('#zone-map')) {
              body.innerHTML = html;
              var appendedForm = body.querySelector('#postcode-create-form') || body.querySelector('#mileage-form') || body.querySelector('form');
              if (appendedForm) attachModalFormHandlers(appendedForm);
            } else {
              body.innerHTML = '';
              body.appendChild(form);
              attachModalFormHandlers(form);
            }
          } else {
            body.innerHTML = html; // fallback
            runInjectedScripts(body);
          }
        }).catch(function(){ body.innerHTML = '<div class="text-red-600">Failed to load form.</div>'; });

        // close handlers
        modal.querySelectorAll('[data-action="close-modal"]').forEach(function(el){ el.addEventListener('click', function(){ modal.classList.add('hidden'); body.innerHTML = ''; }); });
      }

      // expose attachModalFormHandlers globally so map modal and other modals can use it
      window.attachModalFormHandlers = function(form) {
        if (form.dataset.ajaxAttached) return; // avoid double-binding
        form.dataset.ajaxAttached = '1';

        // submit via AJAX
        form.addEventListener('submit', function(e){
          e.preventDefault();
          var url = form.getAttribute('action');
          var method = (form.querySelector('input[name="_method"]') || {value: 'POST'}).value || 'POST';
          var fd = new FormData(form);

          // clear previous errors
          form.querySelectorAll('.text-red-600.text-sm').forEach(function(el){ el.remove(); });

          fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }).then(function(res){
            if (res.status === 201 || res.status === 200) {
              // success
              var modal = document.getElementById('postcode-modal');
              modal.classList.add('hidden');
              document.getElementById('postcode-modal-body').innerHTML = '';

              // try to update any zone selects in-place (when a zone is created/updated via map)
              res.json().then(function(json){
                try {
                  if (json && json.option_html && json.item) {
                    var id = json.item.id;
                    var optionHtml = json.option_html;
                    document.querySelectorAll('select[name="from_zone_id"], select[name="to_zone_id"]').forEach(function(sel){
                      var existing = sel.querySelector('option[value="'+id+'"]');
                      if (existing) {
                        // replace option to update name
                        existing.outerHTML = optionHtml;
                      } else {
                        // append
                        sel.insertAdjacentHTML('beforeend', optionHtml);
                      }
                    });
                    if (typeof window.showToast === 'function') window.showToast('Zone saved');
                  }
                } catch(e){ console.error('Option insert error', e); }

                // refresh the appropriate list (mileage, zone, postcode, or other)
                try {
                  var fid = form && form.id ? form.id.toLowerCase() : '';
                  if (fid.indexOf('mileage') !== -1 && typeof window.refreshMileage === 'function') {
                    window.refreshMileage();
                  } else if (fid.indexOf('zone') !== -1) {
                    try {
                      if (typeof window.refreshZones === 'function') window.refreshZones();
                      if (typeof window.loadMap === 'function' && document.getElementById('map-container')) window.loadMap();
                    } catch (e) {
                      try { window.location.reload(); } catch(e) { console.warn('Reload failed', e); }
                    }
                  } else if (fid.indexOf('postcode') !== -1 && typeof window.refreshPostcodes === 'function') {
                    window.refreshPostcodes();
                  } else if (fid.indexOf('other') !== -1 && typeof window.refreshOther === 'function') {
                    window.refreshOther();
                  }
                } catch(e) { console.error('Refresh error', e); }
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
      }

      // Attach a reusable delete handler that shows the confirm modal and performs the AJAX delete
      window.attachDeleteHandler = function(form) {
        // prevent attaching multiple listeners to the same form
        if (form.dataset.deleteAttached) return;
        form.dataset.deleteAttached = '1';

        var methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput || methodInput.value.toUpperCase() !== 'DELETE') return;
        form.addEventListener('submit', function(e){
          e.preventDefault();

          var confirmModal = document.getElementById('confirm-modal');
          var confirmBody = document.getElementById('confirm-body');
          var okBtn = document.getElementById('confirm-ok');
          var cancelBtn = document.getElementById('confirm-cancel');

          var confirmBtn = form.querySelector('button[data-confirm]');
          var confirmMsg = (confirmBtn && confirmBtn.dataset && confirmBtn.dataset.confirm) ? confirmBtn.dataset.confirm : 'Delete?';
          confirmBody.textContent = confirmMsg;

          confirmModal.classList.remove('hidden');

          var done = false;
          function doDelete(){
            if (done) return; done = true;
            // disable the OK button to avoid double-click sends
            okBtn.disabled = true;

            var fd = new FormData(form);
            console.debug('Deleting', form.getAttribute('action'));
            fetch(form.getAttribute('action'), { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }).then(function(res){
              if (res.ok) {
                var row = form.closest('tr');
                if (row) row.parentNode.removeChild(row);
                showToast('Deleted');
              } else {
                res.json().then(function(json){ alert((json && json.message) ? json.message : 'Delete failed'); console.error('Delete failed', res.status, json); }).catch(function(){ alert('Delete failed'); console.error('Delete failed', res.status); });
              }
            }).catch(function(err){ alert('Delete failed'); console.error('Delete error', err); })
            .finally(function(){ okBtn.disabled = false; confirmModal.classList.add('hidden'); });
          }

          function cleanup(){ okBtn.removeEventListener('click', okHandler); cancelBtn.removeEventListener('click', cancelHandler); }
          function okHandler(e){ e.preventDefault(); cleanup(); doDelete(); }
          function cancelHandler(e){ e.preventDefault(); cleanup(); confirmModal.classList.add('hidden'); }

          okBtn.addEventListener('click', okHandler);
          cancelBtn.addEventListener('click', cancelHandler);
        });
      }

      // Load on tab activate or if default
      var postcodeTab = document.querySelector('[data-tab="postcode"]');
      postcodeTab.addEventListener('click', function(){ if (!postcodeLoaded) loadPostcodes(); });

      // load initial if hash is postcode (or default)
      if (location.hash === '#postcode' || !location.hash) { loadPostcodes(); }
      // load mileage if requested by hash
      if (location.hash === '#mileage') { if (typeof window.openMileageModal === 'function') { /* leave */ } else { var mtab = document.querySelector('[data-tab="mileage"]'); mtab.click(); } }
    })();
    </script>

    <section data-pane="mileage" class="hidden tab-pane">
      <h2 class="text-lg font-semibold mb-2">Mileage Charges</h2>
      <div id="mileage-container"><div class="text-gray-600">Loading mileage charges...</div></div>

      <script>
      (function(){
        var loaded = false;
        function loadMileage(q){
          var url = '{{ route('admin.pricing.mileage.index') }}?partial=1' + (q ? '&q=' + encodeURIComponent(q) : '');
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){
            if (!r.ok) return r.text().then(function(t){ throw new Error((r.status === 401 ? 'Authentication required' : ('Failed to load (status '+r.status+')')) + '\n' + t.slice(0,200)); });
            return r.text();
          }).then(function(html){
            var container = document.getElementById('mileage-container');
            container.innerHTML = html;

            // attach create button
            var addBtn = container.querySelector('#mileage-create-button');
            if (addBtn) addBtn.addEventListener('click', function(e){ e.preventDefault(); openPostcodeModal(addBtn.getAttribute('href'), addBtn.dataset.title || 'Add Mileage Charge'); });

            // attach edit buttons
            container.querySelectorAll('.mileage-edit-button').forEach(function(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); openPostcodeModal(btn.getAttribute('href'), 'Edit Mileage Charge'); }); });

            // attach delete handlers (reuse existing confirm modal helpers)
            container.querySelectorAll('form').forEach(function(form){ attachDeleteHandler(form); });

          }).catch(function(err){
            console.error('Load mileage error', err);
            var container = document.getElementById('mileage-container');
            container.innerHTML = '<div class="text-red-600">Failed to load mileage charges. <button id="mileage-retry" class="ml-2 px-2 py-1 border rounded">Retry</button></div>';
            var btn = document.getElementById('mileage-retry');
            if (btn) btn.addEventListener('click', function(){ loadMileage(); });
          });
        }

        // expose refreshMileage for global use (called after create/update)
        window.refreshMileage = loadMileage;

        window.openMileageModal = function(url){
          var modal = document.getElementById('postcode-modal');
          var body = document.getElementById('postcode-modal-body');
          modal.classList.remove('hidden');
          body.innerHTML = '<div class="text-gray-600">Loading form...</div>';
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){ var frag = document.createElement('div'); frag.innerHTML = html; var form = frag.querySelector('#mileage-form'); if (form) { if (frag.querySelector('script') || frag.querySelector('#zone-map')) { body.innerHTML = html; var appendedForm = body.querySelector('#mileage-form') || body.querySelector('form'); if (appendedForm) attachModalFormHandlers(appendedForm); } else { body.innerHTML = ''; body.appendChild(form); attachModalFormHandlers(form); } } else { body.innerHTML = html; runInjectedScripts(body); } });

          modal.querySelectorAll('[data-action="close-modal"]').forEach(function(el){ el.addEventListener('click', function(){ modal.classList.add('hidden'); body.innerHTML = ''; }); });
        }

        if (location.hash === '#mileage') loadMileage();

        // wire up tab activation
        var t = document.querySelector('[data-tab="mileage"]');
        t.addEventListener('click', function(){ if (!loaded) { loadMileage(); loaded=true; } });
      })();
      </script>
    </section>

    <section data-pane="zone" class="hidden tab-pane">
      <h2 class="text-lg font-semibold mb-2">Zone Charges</h2>
      <div id="zone-container">
        <div class="text-gray-600">Loading zone charges...</div>
      </div>

      <script>
      (function(){
        var loaded = false;
        function loadZones(q){
          var url = '{{ route('admin.pricing.zones.index') }}?partial=1' + (q ? '&q=' + encodeURIComponent(q) : '');
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){
            if (!r.ok) return r.text().then(function(t){ throw new Error((r.status === 401 ? 'Authentication required' : ('Failed to load (status '+r.status+')')) + '\n' + t.slice(0,200)); });
            return r.text();
          }).then(function(html){
            var container = document.getElementById('zone-container');
            container.innerHTML = html;

            var addBtn = container.querySelector('#zones-create-button');
            if (addBtn) addBtn.addEventListener('click', function(e){ e.preventDefault(); openZoneModal(addBtn.getAttribute('href'), addBtn.dataset.title || 'Add Zone Price'); });

            // edit handlers for zone rows
            container.querySelectorAll('.zones-edit-button').forEach(function(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); openZoneModal(btn.getAttribute('href'), 'Edit Zone Price'); }); });

            container.querySelectorAll('.zones-edit-button').forEach(function(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); openZoneModal(btn.getAttribute('href'), 'Edit Zone Price'); }); });

            container.querySelectorAll('form').forEach(function(form){ attachDeleteHandler(form); });

          }).catch(function(err){
            console.error('Load zones error', err);
            var container = document.getElementById('zone-container');
            container.innerHTML = '<div class="text-red-600">Failed to load zone charges. <button id="zones-retry" class="ml-2 px-2 py-1 border rounded">Retry</button></div>';
            var btn = document.getElementById('zones-retry');
            if (btn) btn.addEventListener('click', function(){ loadZones(); });
          });
        }

        window.refreshZones = loadZones;

        window.openZoneModal = function(url){
          var modal = document.getElementById('postcode-modal');
          var body = document.getElementById('postcode-modal-body');
          modal.classList.remove('hidden');
          body.innerHTML = '<div class="text-gray-600">Loading form...</div>';
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){ var frag = document.createElement('div'); frag.innerHTML = html; var form = frag.querySelector('#zone-form'); if (form) { body.innerHTML=''; body.appendChild(form); attachModalFormHandlers(form); } else { body.innerHTML = html; runInjectedScripts(body); } });

          modal.querySelectorAll('[data-action="close-modal"]').forEach(function(el){ el.addEventListener('click', function(){ modal.classList.add('hidden'); body.innerHTML = ''; }); });
        }



        if (location.hash === '#zone') loadZones();

        var t = document.querySelector('[data-tab="zone"]');
        t.addEventListener('click', function(){ if (!loaded) { loadZones(); loaded=true; } });
      })();
      </script>
    </section>

    <section data-pane="other" class="hidden tab-pane">
      <h2 class="text-lg font-semibold mb-2">Other Charges</h2>
      <div id="other-container"><div class="text-gray-600">Loading other charges...</div></div>

      <script>
      (function(){
        var loaded = false;
        function loadOther(q){
          var url = '{{ route('admin.pricing.others.index') }}?partial=1' + (q ? '&q=' + encodeURIComponent(q) : '');
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){ 
            if (!r.ok) return r.text().then(function(t){ throw new Error('Failed to load ('+r.status+')\n'+t.slice(0,200)); }); 
            return r.text(); 
          }).then(function(html){ 
            var container = document.getElementById('other-container'); 
            container.innerHTML = html; 
            
            // attach search form
            var searchForm = container.querySelector('#other-search-form');
            if (searchForm) {
              searchForm.addEventListener('submit', function(e){
                e.preventDefault();
                var fd = new FormData(searchForm);
                loadOther(fd.get('q'));
              });
            }
            
            // attach create button
            var addBtn = container.querySelector('#other-create-button'); 
            if (addBtn) addBtn.addEventListener('click', function(e){ 
              e.preventDefault(); 
              openOtherModal(addBtn.getAttribute('href'), addBtn.dataset.title || 'Add Other Charge'); 
            }); 
            
            // attach edit buttons
            container.querySelectorAll('.other-edit-button').forEach(function(btn){ 
              btn.addEventListener('click', function(e){ 
                e.preventDefault(); 
                openOtherModal(btn.getAttribute('href'), 'Edit Other Charge'); 
              }); 
            }); 
            
            // attach delete handlers
            container.querySelectorAll('form').forEach(function(form){ 
              attachDeleteHandler(form); 
            }); 
            
            // handle pagination
            container.querySelectorAll('.pagination a').forEach(function(a){
              a.addEventListener('click', function(e){
                var href = a.getAttribute('href');
                if (!href) return;
                e.preventDefault();
                fetch(href + '&partial=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r){ return r.text(); }).then(function(html){ container.innerHTML = html; loadOther(); });
              });
            });
          }).catch(function(err){ 
            console.error('Load other error', err); 
            var container = document.getElementById('other-container'); 
            container.innerHTML = '<div class="text-red-600">Failed to load other charges. <button id="other-retry" class="ml-2 px-2 py-1 border rounded">Retry</button></div>'; 
            var btn = document.getElementById('other-retry'); 
            if (btn) btn.addEventListener('click', function(){ loadOther(); }); 
          });
        }

        window.refreshOther = loadOther;

        window.openOtherModal = function(url, title){ 
          var modal = document.getElementById('postcode-modal'); 
          var body = document.getElementById('postcode-modal-body'); 
          var modalTitle = document.getElementById('modal-title');
          modal.classList.remove('hidden'); 
          modalTitle.textContent = title || 'Add Other Charge';
          body.innerHTML = '<div class="text-gray-600">Loading form...</div>'; 
          
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){ 
            return r.text(); 
          }).then(function(html){ 
            var frag = document.createElement('div'); 
            frag.innerHTML = html; 
            var form = frag.querySelector('#other-charge-form') || frag.querySelector('form'); 
            if (form) { 
              body.innerHTML = ''; 
              body.appendChild(form); 
              attachModalFormHandlers(form); 
            } else { 
              body.innerHTML = html; 
              runInjectedScripts(body); 
            } 
          }).catch(function(){ 
            body.innerHTML = '<div class="text-red-600">Failed to load form.</div>'; 
          }); 
          
          modal.querySelectorAll('[data-action="close-modal"]').forEach(function(el){ 
            el.addEventListener('click', function(){ 
              modal.classList.add('hidden'); 
              body.innerHTML = ''; 
            }); 
          }); 
        }

        var t = document.querySelector('[data-tab="other"]');
        if (t) {
          t.addEventListener('click', function(){ 
            if (!loaded) { 
              loadOther(); 
              loaded=true; 
            } 
          });
        }
        
        if (location.hash === '#other') loadOther();
      })();
      </script>
    </section>

    <section data-pane="map" class="hidden tab-pane">
      <h2 class="text-lg font-semibold mb-2">Zones Map</h2>
      <div id="map-container">
        <div class="text-gray-600">Loading map...</div>
      </div>

      <script>
      (function(){
        var loaded = false;
        
        function loadMap(){
          console.log('Loading map via AJAX');
          var container = document.getElementById('map-container');
          container.innerHTML = '<div class="text-gray-600">Loading map...</div>';
          
          var url = '{{ route('admin.pricing.zones.map') }}?partial=1';
          fetch(url, { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' }, 
            credentials: 'same-origin' 
          }).then(function(r){ 
            if (!r.ok) return r.text().then(function(t){ 
              throw new Error('Failed to load map ('+r.status+')\n'+t.slice(0,200)); 
            }); 
            return r.text(); 
          }).then(function(html){ 
            console.log('Map HTML received, injecting into container');
            container.innerHTML = html; 
            
            // Run any scripts that were injected
            var scripts = container.querySelectorAll('script');
            scripts.forEach(function(oldScript){
              var newScript = document.createElement('script');
              if (oldScript.src) {
                newScript.src = oldScript.src;
              } else {
                newScript.textContent = oldScript.textContent;
              }
              oldScript.parentNode.replaceChild(newScript, oldScript);
            });
            
            console.log('Map loaded successfully');
          }).catch(function(err){ 
            console.error('Load map error', err); 
            container.innerHTML = '<div class="text-red-600">Failed to load map. <button id="map-retry" class="ml-2 px-2 py-1 border rounded">Retry</button></div>'; 
            var btn = document.getElementById('map-retry'); 
            if (btn) btn.addEventListener('click', function(){ loadMap(); }); 
          });
        }

        window.loadMap = loadMap;

        var t = document.querySelector('[data-tab="map"]');
        if (t) {
          t.addEventListener('click', function(){ 
            if (!loaded) { 
              loadMap(); 
              loaded=true; 
            } 
          });
        }
        
        if (location.hash === '#map') loadMap();
      })();
      </script>
    </section>
  </div>

  <!-- Global Modals and Toasts (moved outside tab panes so they render regardless of active tab) -->
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

  <!-- Toast container -->
  <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-2"></div>
</div>

<script>
(function(){
  var tabs = document.querySelectorAll('[data-tab]');
  var panes = document.querySelectorAll('[data-pane]');
  function activate(tabName){
    tabs.forEach(function(t){
      if(t.getAttribute('data-tab') === tabName){
        t.classList.add('border-indigo-600','text-indigo-700');
        t.classList.remove('text-gray-600');
        t.setAttribute('aria-selected','true');
      } else {
        t.classList.remove('border-indigo-600','text-indigo-700');
        t.classList.add('text-gray-600');
        t.setAttribute('aria-selected','false');
      }
    });
    panes.forEach(function(p){
      if(p.getAttribute('data-pane') === tabName) p.classList.remove('hidden'); else p.classList.add('hidden');
    });
    // update hash
    history.replaceState(null, '', '#'+tabName);
  }

  tabs.forEach(function(t){
    t.addEventListener('click', function(){ activate(t.getAttribute('data-tab')); });
  });

  // initial activation from hash or default to postcode
  var initial = (location.hash || '#postcode').substring(1);
  var found = Array.from(tabs).some(function(t){ return t.getAttribute('data-tab')===initial; });
  activate(found ? initial : 'postcode');

  // global delegated handler: close modal when any element with data-action="close-modal" is clicked
  document.addEventListener('click', function(e){
    var el = e.target.closest('[data-action="close-modal"]');
    if (!el) return;
    var modal = document.getElementById('postcode-modal');
    if (modal && !modal.classList.contains('hidden')) {
      modal.classList.add('hidden');
      var body = document.getElementById('postcode-modal-body');
      if (body) body.innerHTML = '';
    }
  });
})();
</script>
@endsection
