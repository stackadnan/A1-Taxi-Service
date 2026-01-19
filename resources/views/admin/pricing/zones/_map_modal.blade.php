<div>
  <form id="zone-map-form" method="POST" action="{{ isset($item) ? route('admin.pricing.zones.update_map', $item) : route('admin.pricing.zones.store_map') }}">
    @csrf

    <div>
      <div id="zone-map-status" class="mb-2 text-sm text-gray-600">Loading map...</div>
      <div id="zone-map" style="height: 400px; border:1px solid #ddd;"></div>
      <div class="mt-2 flex items-center gap-2">
        <button id="zone-map-retry" type="button" class="px-3 py-1 border rounded text-sm" style="display:none">Retry map</button>
        <button id="zone-map-reset" type="button" class="px-3 py-1 border rounded text-sm" style="display:none">Reset drawing</button>
        <div id="zone-map-debug" class="text-xs text-gray-500 ml-auto"></div>
      </div>
    </div>

    <input type="hidden" name="polygon" id="zone-polygon" value='{{ isset($item) && isset($item->meta["polygon"]) ? json_encode($item->meta["polygon"]) : "" }}' />

    <div class="mt-4">
      <label class="block text-sm">Zone Name</label>
      <input type="text" name="zone_name" id="zone-name" value="{{ $item->zone_name ?? '' }}" class="w-full border rounded p-2" />
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button type="button" data-action="close-modal" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
      <button type="submit" id="zone-map-save" class="px-4 py-2 bg-indigo-600 text-white rounded" disabled>Save</button>
    </div>
  </form>

  <script>
  (function(){
    // robust loader for CSS and scripts, with CDN -> local fallback
    function loadScript(url, localFallback){
      return new Promise(function(res,rej){
        var s=document.createElement('script'); s.src=url; s.async=false;
        s.onload=function(){ console.debug('Loaded script', url); res(url); };
        s.onerror=function(e){
          console.warn('Failed to load script', url, 'trying local', localFallback);
          if (localFallback) {
            var s2 = document.createElement('script'); s2.src = localFallback; s2.async=false;
            s2.onload=function(){ console.debug('Loaded local script', localFallback); res(localFallback); };
            s2.onerror=function(e2){ console.error('Failed local script', localFallback, e2); rej(new Error('Failed both CDN and local for '+url)); };
            document.head.appendChild(s2);
          } else rej(new Error('Failed script '+url));
        };
        document.head.appendChild(s);
      });
    }

    function loadCss(url, localFallback){
      return new Promise(function(res,rej){
        var l=document.createElement('link'); l.rel='stylesheet'; l.href=url;
        l.onload=function(){ console.debug('Loaded css', url); res(url); };
        l.onerror=function(e){
          console.warn('Failed to load css', url, 'trying local', localFallback);
          if (localFallback) {
            var l2=document.createElement('link'); l2.rel='stylesheet'; l2.href=localFallback;
            l2.onload=function(){ console.debug('Loaded local css', localFallback); res(localFallback); };
            l2.onerror=function(e2){ console.error('Failed local css', localFallback, e2); res(); };
            document.head.appendChild(l2);
          } else { console.warn('CSS failed and no local fallback configured'); res(); }
        };
        document.head.appendChild(l);
      });
    }

    var statusEl = document.getElementById('zone-map-status');
    var retryBtn = document.getElementById('zone-map-retry');
    var resetBtn = document.getElementById('zone-map-reset');
    var debugEl = document.getElementById('zone-map-debug');

    var map = null; var drawnItems = null;

    function setStatus(text, isError){ statusEl.textContent = text || ''; statusEl.className = isError ? 'mb-2 text-sm text-red-600' : 'mb-2 text-sm text-gray-600'; }
    function setDebug(t){ if (debugEl) debugEl.textContent = t; }

    function initMapOnce(){
      setStatus('Loading map resources...');
      setDebug('');
      retryBtn.style.display = 'none'; resetBtn.style.display = 'none';

      var needsLeaflet = !window.L;
      var needsDraw = !(window.L && (window.L.Draw || window.L.EditToolbar));

      console.debug('initMapOnce: needsLeaflet=', needsLeaflet, 'needsDraw=', needsDraw);
      var p = Promise.resolve();
      if (needsLeaflet) {
        p = p.then(function(){ console.debug('initMapOnce: loading leaflet css (CDN -> local)...'); return loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', '/vendor/leaflet/leaflet.css'); })
             .then(function(){ console.debug('initMapOnce: loading leaflet js (CDN -> local)...'); return loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', '/vendor/leaflet/leaflet.js'); });
      }
      p = p.then(function(){
        if (needsDraw) {
          console.debug('initMapOnce: loading leaflet.draw css (CDN -> local)...');
          return loadCss('https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css', '/vendor/leaflet/leaflet.draw.css').then(function(){ console.debug('initMapOnce: loading leaflet.draw js (CDN -> local)...'); return loadScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js', '/vendor/leaflet/leaflet.draw.js'); });
        }
      });

      return p.then(function(){
        try {
          var mapEl = document.getElementById('zone-map');
          mapEl.innerHTML = '';
          // Initialize the map focused on the UK and restrict panning
          var ukBounds = [[49.5, -8.6],[61.0, 2.1]]; // SW lat,lng and NE lat,lng bounding box roughly covering the UK
          var map = L.map('zone-map', {
            maxBounds: ukBounds,
            maxBoundsViscosity: 0.9,
            minZoom: 5,
            maxZoom: 19
          });

          // Initial default view: London (first-time load of modal map)
          map.setView([51.5074, -0.1278], 11);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap contributors' }).addTo(map);

          map.on('tileerror', function(e){ console.error('Tile error', e); setStatus('Map tiles failed to load (network).', true); retryBtn.style.display = ''; });

          // ensure the map lays out correctly after the modal is shown
          setTimeout(function(){ try { map.invalidateSize(); map.setView(map.getCenter()); } catch(e){} }, 250);

          drawnItems = new L.FeatureGroup();
          map.addLayer(drawnItems);

          var drawControl = new L.Control.Draw({
            draw: { polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false, polygon: { allowIntersection: false } },
            edit: { featureGroup: drawnItems }
          });
          map.addControl(drawControl);

          var saveBtn = document.getElementById('zone-map-save');
          var polygonInput = document.getElementById('zone-polygon');

          function setPolygon(geojson){ polygonInput.value = geojson ? JSON.stringify(geojson) : ''; saveBtn.disabled = !geojson; }

          // preload existing polygon if provided
          try {
            var existing = polygonInput.value ? JSON.parse(polygonInput.value) : null;
            if (existing && existing.coordinates && existing.coordinates.length) {
              var coords = existing.coordinates[0].map(function(pt){ return [pt[1], pt[0]]; }); // to [lat,lng]
              var poly = L.polygon(coords, {color:'#2b6cb0'});
              drawnItems.addLayer(poly);
              map.fitBounds(poly.getBounds());
              setPolygon(existing);
            }
          } catch (e){ console.error('Invalid preloaded polygon', e); }

          map.on(L.Draw.Event.CREATED, function (e) { drawnItems.clearLayers(); drawnItems.addLayer(e.layer); var gj = e.layer.toGeoJSON(); setPolygon(gj.geometry); resetBtn.style.display = ''; setStatus('Ready — draw polygon and Save'); });
          map.on(L.Draw.Event.EDITED, function(e){ var layers = e.layers; layers.eachLayer(function(layer){ var gj = layer.toGeoJSON(); setPolygon(gj.geometry); }); setStatus('Edited — Save when done'); });
          map.on(L.Draw.Event.DELETED, function(e){ setPolygon(null); resetBtn.style.display = 'none'; setStatus('No polygon drawn'); });

          // wire reset
          resetBtn.addEventListener('click', function(){ drawnItems.clearLayers(); document.getElementById('zone-polygon').value = ''; document.getElementById('zone-map-save').disabled = true; resetBtn.style.display = 'none'; setStatus('Reset — draw a polygon'); });

          // set handlers for retry to re-init
          retryBtn.addEventListener('click', function(){ initMapOnce().catch(function(err){ setStatus('Retry failed: '+(err && err.message), true); retryBtn.style.display=''; }); });

          setStatus('Map ready — draw a polygon');
          resetBtn.style.display = drawnItems.getLayers().length ? '' : 'none';
          runInjectedScripts(document.getElementById('zone-map')); // in case scripts were inline

          setDebug('Leaflet: ' + (!!window.L) + ' Draw: ' + (!!(window.L && window.L.Draw)));

          return map;
        } catch (err) {
          console.error('Map init error', err);
          setStatus('Failed to initialize map: '+(err && err.message), true);
          retryBtn.style.display = '';
          setDebug(String(err));
          throw err;
        }
      }).catch(function(err){ console.error('Map load error', err); setStatus('Failed to load map resources: '+(err && err.message), true); retryBtn.style.display = ''; setDebug(String(err)); throw err; });
    }

    // start initial attempt
    initMapOnce().catch(function(){ /* initial errors shown in UI */ });

    // ensure after injection we also attempt to call invalidateSize a few times (timing robustness)
    var tries = 0; var tryInvalidate = setInterval(function(){ try { if (map) map.invalidateSize(); } catch(e){} tries++; if (tries>6) clearInterval(tryInvalidate); }, 300);

  })();
  </script>
</div>