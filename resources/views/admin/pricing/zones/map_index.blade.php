@extends('layouts.admin')

@section('title','Zones Map')

@section('content')
<style>
  .zone-hover-disabled { cursor: not-allowed !important; }
  #zone-overlap-error { display:none; position:absolute; left:50%; transform:translateX(-50%); top:12px; z-index:9999; background:#ef4444; color:white; padding:8px 14px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.15); font-weight:600; }
  .leaflet-draw-tooltip-error { background:#ef4444 !important; color:white !important; border:1px solid #dc2626 !important; }
  
  /* Status text styling */
  #zones-map-status-text { 
    position: absolute; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%); 
    z-index: 1000; 
    background: rgba(255,255,255,0.95); 
    padding: 16px 24px; 
    border-radius: 8px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
    font-weight: 600; 
    color: #333;
    pointer-events: none;
  }
  
  /* overlay for blurring outside the UK - uses backdrop-filter; fallback will show semi-transparent wash */
  .uk-mask-overlay { backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }

  /* Sidebar layout: fixed header + scrollable list that fills remaining height */
  #zones-list-sidebar { display: flex; flex-direction: column; }
  #zones-list-sidebar .sidebar-header { flex: 0 0 auto; }
  #zones-list-ul { flex: 1 1 auto; overflow-y: auto; max-height: none; padding: 0.5rem; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.18) transparent; }

  /* Thin, subtle scrollbar for supported browsers */
  #zones-list-ul::-webkit-scrollbar { width: 8px; }
  #zones-list-ul::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 6px; }
  #zones-list-ul::-webkit-scrollbar-track { background: transparent; }

  /* Hide the default Leaflet draw toolbar inside the main map — we provide a header button instead */
  #zones-map .leaflet-draw { display: none !important; }
</style>
<div class="bg-white p-6 rounded shadow">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Zones Map</h1>
    <div class="flex items-center gap-2">
      @if (auth()->check() && auth()->user()->hasPermission('pricing.create'))
        <button id="start-draw-btn" class="px-3 py-2 bg-red-500 text-white border border-red-500 rounded text-sm">Draw Zone</button>
      @endif
      <button id="toggle-zones-list-btn" class="px-3 py-2 bg-indigo-600 text-white rounded">Show Zones</button>
    </div>
  </div>

  @include('admin.pricing._tabs') 

  <!-- Sidebar List (fixed overlay, outside map to avoid clipping and stacking issues) -->
  <div id="zones-list-sidebar" class="fixed top-20 right-6 h-[calc(100vh-6rem)] w-64 bg-white shadow-lg transform translate-x-full transition-transform duration-300 border-l border-gray-200" style="z-index:99999; pointer-events:auto;">
      <div class="flex items-center justify-between p-3 border-b border-gray-200 bg-gray-50 sidebar-header">
          <h3 class="font-semibold text-gray-700">All Zones</h3>
          <button id="close-zones-list-btn" class="text-gray-500 hover:text-red-500 text-lg font-bold px-2">&times;</button>
      </div>
      <ul id="zones-list-ul" class="p-2 space-y-1"></ul>
  </div>

  <div id="zones-map" style="height: 640px; border:1px solid #ddd; position:relative;">
      <div id="zones-map-status-text">Loading map...</div>
      <div id="zones-map-status-actions" style="display:none">
        <button id="zones-map-retry-local" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Retry using local assets</button>
      </div>
    </div>
    <div id="zone-overlap-error">Error: Cannot draw over existing zones!</div>
    @include('admin.pricing.zones.zone_edit_controls')
  </div>
</div>

<script>
(function(){
  console.log('=== MAP INITIALIZATION START ===');
  
  // GeoJSON supplied by server
  window.ZONES_GEOJSON = {!! json_encode($geojson) !!};
  console.log('GeoJSON data loaded:', window.ZONES_GEOJSON);
  console.log('Number of zones:', window.ZONES_GEOJSON?.features?.length || 0);
  
  // whether current user can delete zones
  window.CAN_DELETE_ZONES = {!! json_encode(auth()->check() && auth()->user()->hasPermission('pricing.edit')) !!};
  // whether current user can create/edit zones
  window.CAN_CREATE_ZONES = {!! json_encode(auth()->check() && auth()->user()->hasPermission('pricing.create')) !!};
  window.CAN_EDIT_ZONES = {!! json_encode(auth()->check() && auth()->user()->hasPermission('pricing.edit')) !!};
  
  console.log('Permissions - Delete:', window.CAN_DELETE_ZONES, 'Create:', window.CAN_CREATE_ZONES, 'Edit:', window.CAN_EDIT_ZONES);

  // Ensure Show Zones toggle works even if map initialization fails: attach lightweight handlers early
  (function(){
    console.log('Setting up sidebar toggle handlers');
    var toggleBtn = document.getElementById('toggle-zones-list-btn');
    var closeBtn = document.getElementById('close-zones-list-btn');
    var sidebar = document.getElementById('zones-list-sidebar');
    var listUl = document.getElementById('zones-list-ul');
    
    console.log('Sidebar elements found:', {toggleBtn: !!toggleBtn, closeBtn: !!closeBtn, sidebar: !!sidebar, listUl: !!listUl});

    // intentionally leave the list empty until the map populates it (avoid showing a loading placeholder)
    if (listUl && (!listUl.children || listUl.children.length === 0)) {
      listUl.innerHTML = '';
    }

    // Helper to position the sidebar below the toggle button (keeps position whether open or closed)
    function positionSidebar(){
      if (!sidebar || !toggleBtn) return;
      try {
        var rect = toggleBtn.getBoundingClientRect();
        var topPx = rect.bottom + 8; // 8px gap below button
        sidebar.style.top = topPx + 'px';
        // adjust max-height so it doesn't overflow viewport
        var maxHeight = Math.max(100, window.innerHeight - topPx - 20);
        sidebar.style.maxHeight = maxHeight + 'px';
      } catch(e) { }
    }

    function openSidebar(){
      positionSidebar();
      sidebar.classList.remove('translate-x-full');
    }

    function closeSidebar(){
      if (!sidebar) return;
      // ensure the sidebar remains positioned below the toggle even when hidden
      positionSidebar();
      sidebar.classList.add('translate-x-full');
      // keep inline positioning so hidden panel stays below the button
    }

    if (toggleBtn) toggleBtn.addEventListener('click', function(e){ e.preventDefault(); if (sidebar && sidebar.classList.contains('opacity-0')) openSidebar(); else closeSidebar(); });
    if (closeBtn) closeBtn.addEventListener('click', function(e){ e.preventDefault(); closeSidebar(); });

    // reposition on window resize (keeps position both open and closed)
    window.addEventListener('resize', function(){
      positionSidebar();
    });

    // ensure sidebar is positioned correctly on init
    positionSidebar();

    // allow user to press Escape to close
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeSidebar(); });

    // clicking outside the sidebar closes it
    document.addEventListener('click', function(e){
      if (!sidebar || sidebar.classList.contains('translate-x-full')) return;
      var p = e.target;
      var inside = false;
      while (p) { if (p === sidebar) { inside = true; break; } p = p.parentElement; }
      if (!inside) {
        // don't close when clicking the toggle button itself
        var tb = toggleBtn; var pt = e.target; var clickOnToggle = false;
        while (pt) { if (pt === tb) { clickOnToggle = true; break; } pt = pt.parentElement; }
        if (!clickOnToggle) closeSidebar();
      }
    });
  })();

  function loadCss(url, localFallback){
    console.log('loadCss called:', url);
    return new Promise(function(res){ var l=document.createElement('link'); l.rel='stylesheet'; l.href=url; l.onload=function(){ console.log('CSS loaded successfully:', url); res(true); }; l.onerror=function(){ console.warn('CSS failed from CDN:', url); if (localFallback){ console.log('Trying local fallback:', localFallback); var l2=document.createElement('link'); l2.rel='stylesheet'; l2.href=localFallback; l2.onload=function(){ console.log('CSS loaded from local:', localFallback); res(true); }; l2.onerror=function(){ console.error('CSS failed from both CDN and local'); res(false); }; document.head.appendChild(l2); } else res(false); }; document.head.appendChild(l); });
  }
  function loadScript(url, localFallback){
    console.log('loadScript called:', url);
    return new Promise(function(res,rej){ var s=document.createElement('script'); s.src=url; s.async=false; s.onload=function(){ console.log('Script loaded successfully:', url); res(true); }; s.onerror=function(){ console.warn('Script failed from CDN:', url); if (localFallback){ console.log('Trying local fallback:', localFallback); var s2=document.createElement('script'); s2.src=localFallback; s2.async=false; s2.onload=function(){ console.log('Script loaded from local:', localFallback); res(true); }; s2.onerror=function(){ console.error('Script failed from both CDN and local'); rej(new Error('Failed both CDN and local')); }; document.head.appendChild(s2); } else { console.error('Script failed, no fallback'); rej(new Error('Failed script '+url)); } }; document.head.appendChild(s); });
  }

  // Load leaflet and draw as needed with verbose status updates
  console.log('Starting Leaflet library loading chain...');
  Promise.resolve()
    .then(function(){
      console.log('Step 1: Checking local Leaflet availability');
      var statusEl = document.getElementById('zones-map-status-text');
      if (statusEl) statusEl.textContent = 'Checking local Leaflet availability...';
      // quick HEAD check for local fallback availability
      return fetch('/vendor/leaflet/leaflet.js', { method: 'HEAD' }).then(function(res){
        console.log('Local Leaflet check response:', res.status);
        if (statusEl) statusEl.textContent = 'Loading Leaflet CSS (CDN -> local fallback)';
        return loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', '/vendor/leaflet/leaflet.css');
      }).catch(function(err){ 
        console.log('Local Leaflet not available, using CDN only:', err); 
        if (statusEl) statusEl.textContent = 'Local Leaflet not present, using CDN'; 
        return loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', '/vendor/leaflet/leaflet.css'); 
      });
    })
    .then(function(cssLoaded){ 
      console.log('Step 2: CSS loaded, result:', cssLoaded);
      var statusEl = document.getElementById('zones-map-status-text'); 
      if (statusEl) statusEl.textContent = 'Loading Leaflet JS (CDN -> local fallback)'; 
      return loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', '/vendor/leaflet/leaflet.js'); 
    })
    .then(function(jsLoaded){
      console.log('Step 3: Leaflet JS loaded, result:', jsLoaded);
      console.log('Checking if L (Leaflet) is available:', typeof window.L);
      var statusEl = document.getElementById('zones-map-status-text'); 
      if (statusEl) statusEl.textContent = 'Leaflet loaded, initializing map...';
      try {
        console.log('Step 4: Starting map initialization');
        var mapEl = document.getElementById('zones-map');
        var statusEl = document.getElementById('zones-map-status-text');
        console.log('Map container element:', mapEl);
        if (statusEl) statusEl.textContent = 'Initializing map...';

        // If Leaflet failed to load for any reason, show a helpful message
        if (typeof window.L === 'undefined') {
          console.error('CRITICAL ERROR: Leaflet (L) is undefined - script failed to load');
          if (statusEl) statusEl.textContent = 'Leaflet library not available. Check console for errors.';
          return;
        }

        console.log('Step 5: Creating Leaflet map instance');
        var ukBounds = [[49.5, -8.6],[61.0, 2.1]];
        console.log('UK bounds:', ukBounds);
        var map = L.map('zones-map', { maxBounds: ukBounds, maxBoundsViscosity: 0.9, minZoom: 5, maxZoom: 19 });
        console.log('Map instance created:', map);
        
        // Initial default view: London (first-time load)
        console.log('Setting initial view to London');
        map.setView([51.5074, -0.1278], 13);
        
        console.log('Adding tile layer from OpenStreetMap');
        var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap contributors' }).addTo(map);
        console.log('Tile layer added:', tileLayer);

        // hide status when tiles finished loading for this layer
        tileLayer.on('load', function(){ 
          console.log('Tiles loaded successfully'); 
          if (statusEl) statusEl.style.display = 'none'; 
        });
        
        tileLayer.on('tileerror', function(err){
          console.error('Tile loading error:', err);
        });

        // if tiles don't load within 7s, show an informative message
        setTimeout(function(){
          if (statusEl && statusEl.style.display !== 'none') {
            console.warn('Map tiles taking longer than expected to load');
            statusEl.textContent = 'Map is taking longer than expected to load — check your network or console for errors.';
          }
        }, 7000);

        var geo = window.ZONES_GEOJSON || { type: 'FeatureCollection', features: [] };
        console.log('GeoJSON to render:', geo);

        // feature group which will hold editable polygons
        console.log('Creating drawn items feature group');
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);
        console.log('Drawn items layer added to map');

        // --- UK mask overlay: blur everything outside the UK bbox ---
        // Add a top-layer div and apply an SVG mask to the element itself so the UK hole is transparent
        console.log('Creating UK mask overlay');
        (function(){
          var maskEl = L.DomUtil.create('div', 'uk-mask-overlay', map.getContainer());
          // ensure overlay sits on top but doesn't block interactions
          maskEl.style.position = 'absolute'; maskEl.style.inset = '0'; maskEl.style.pointerEvents = 'none'; maskEl.style.zIndex = 650;
          // apply blur to backdrop (supported browsers)
          maskEl.style.backdropFilter = 'blur(6px)'; maskEl.style.webkitBackdropFilter = 'blur(6px)';
          // subtle translucent wash for fallback browsers
          maskEl.style.background = 'rgba(255,255,255,0.08)';

          // create SVG mask (white = visible, black = transparent mask hole)
          var svgNS = 'http://www.w3.org/2000/svg';
          var svg = document.createElementNS(svgNS, 'svg');
          svg.setAttribute('width', '100%'); svg.setAttribute('height', '100%'); svg.setAttribute('preserveAspectRatio', 'none'); svg.style.position='absolute'; svg.style.inset='0'; svg.style.pointerEvents='none';

          var defs = document.createElementNS(svgNS, 'defs');
          var mask = document.createElementNS(svgNS, 'mask');
          var maskId = 'uk-mask-' + String(Math.random()).slice(2);
          mask.setAttribute('id', maskId);

          // white rect = visible overlay; black path = transparent hole
          var rect = document.createElementNS(svgNS, 'rect');
          rect.setAttribute('x', '0'); rect.setAttribute('y', '0'); rect.setAttribute('width', '100%'); rect.setAttribute('height', '100%'); rect.setAttribute('fill', 'white');
          mask.appendChild(rect);

          var holePath = document.createElementNS(svgNS, 'path');
          holePath.setAttribute('fill', 'black');
          mask.appendChild(holePath);

          defs.appendChild(mask);
          svg.appendChild(defs);
          maskEl.appendChild(svg);

          // apply the mask to the overlay element so masked region becomes transparent (no backdrop blur)
          maskEl.style.mask = 'url(#' + maskId + ')';
          maskEl.style.webkitMask = 'url(#' + maskId + ')';

          // UK bbox corners (lat, lng)
          var ukTop = L.latLng(61.0, -8.6); // northwest corner approx
          var ukRight = L.latLng(61.0, 2.1); // northeast
          var ukBottomRight = L.latLng(49.5, 2.1); // southeast
          var ukBottomLeft = L.latLng(49.5, -8.6); // southwest

          function updateHole() {
            try {
              var nw = map.latLngToContainerPoint(ukTop);
              var ne = map.latLngToContainerPoint(ukRight);
              var se = map.latLngToContainerPoint(ukBottomRight);
              var sw = map.latLngToContainerPoint(ukBottomLeft);
              var d = 'M ' + nw.x + ' ' + nw.y + ' L ' + ne.x + ' ' + ne.y + ' L ' + se.x + ' ' + se.y + ' L ' + sw.x + ' ' + sw.y + ' Z';
              holePath.setAttribute('d', d);
            } catch (e) {
              // ignore during init
            }
          }

          // update on view changes
          map.on('move zoom resize', updateHole);
          // initial
          updateHole();
          console.log('UK mask overlay created successfully');
        })();
        // --- end UK mask overlay ---

        // add existing zones to the feature group
        console.log('Adding existing zones to map, feature count:', geo.features.length);
        var layer = L.geoJSON(geo, {
          style: function(f){ 
            console.log('Styling feature:', f);
            return { color: '#2b6cb0', weight: 2, fillOpacity: 0.2 }; 
          },
          onEachFeature: function(feature, lyr){
            console.log('Processing feature:', feature);
            var title = (feature.properties && feature.properties.name) ? feature.properties.name : 'Zone';
            var html = '<strong>'+title+'</strong>';
            // Inline edit: show 'Edit Zone' button which edits the zone on this map
            if (feature.properties && feature.properties.id) {
              html += '<div class="mt-2"><a href="#" class="text-sm text-indigo-600 mr-2 zone-edit-btn" data-zone-id="'+feature.properties.id+'">Edit Zone</a>';
            } else {
              html += '<div class="mt-2"><a href="#" class="text-sm text-indigo-600 mr-2">Edit Zone</a>';
            }
            if (window.CAN_DELETE_ZONES) {
              html += '<a href="#" class="text-sm text-red-600" onclick="event.preventDefault(); deleteZone('+feature.properties.id+');">Delete</a>';
            }
            html += '</div>';
            lyr.bindPopup(html);
            // ensure the feature has properties available and add to drawnItems for editing
            drawnItems.addLayer(lyr);
            console.log('Feature added to drawnItems:', title);
          }
        }).addTo(map);
        console.log('GeoJSON layer created and added to map:', layer);

        // Edit-on-map helper
        function editZoneOnMap(zoneId) {
          var found = null;
          drawnItems.eachLayer(function(l){ if (l.feature && l.feature.properties && l.feature.properties.id == zoneId) found = l; });
          if (!found) return alert('Zone not found on this map');
          if (window.currentlyEditing && window.currentlyEditing !== found) return alert('Finish current edit first');
          window.currentlyEditing = found;
          // store original geometry for cancel
          try { found._originalGeo = found.toGeoJSON(); } catch(e) { found._originalGeo = null; }

          // enable editing (try built-in editing, otherwise attach L.Edit.Poly fallback)
          var editEnabled = false;
          try {
            if (found.editing && typeof found.editing.enable === 'function') {
              found.editing.enable();
              editEnabled = true;
            } else if (window.L && L.Edit && L.Edit.Poly) {
              try {
                found._leaflet_edit = new L.Edit.Poly(found);
                found._leaflet_edit.enable();
                editEnabled = true;
              } catch(e) { console.warn('Leaflet.Edit.Poly fallback failed', e); }
            }
          } catch(e) { console.warn('enable editing failed', e); }

          // show controls and position them *inside* the zone being edited
          var ctrl = document.getElementById('zone-edit-controls');
          if (ctrl) {
            ctrl.style.display = 'block';
            ctrl.style.pointerEvents = 'auto';
            ctrl.style.position = 'absolute';

            // helper: point-in-polygon (ray-casting) using [lng,lat] coords
            function pointInPoly(pt, vs) {
              var x = pt[0], y = pt[1];
              var inside = false;
              for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
                var xi = vs[i][0], yi = vs[i][1];
                var xj = vs[j][0], yj = vs[j][1];
                var intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi + 0) + xi);
                if (intersect) inside = !inside;
              }
              return inside;
            }

            function computeInnerLatLng() {
              try {
                // Prefer Turf's pointOnFeature (guaranteed to be on surface) when available
                if (typeof turf !== 'undefined' && found.toGeoJSON) {
                  try {
                    var g = found.toGeoJSON();
                    var pOn = turf.pointOnFeature(g);
                    if (pOn && pOn.geometry && pOn.geometry.coordinates) {
                      return L.latLng(pOn.geometry.coordinates[1], pOn.geometry.coordinates[0]);
                    }
                  } catch (e) { /* ignore turf failure */ }
                }

                // Fallback: use first polygon ring average and test it lies within polygon
                var gj = null;
                try { gj = found.toGeoJSON && found.toGeoJSON(); } catch(e) { gj = null; }
                if (gj && gj.geometry) {
                  var geom = gj.geometry;

                  if (geom.type === 'Polygon' && geom.coordinates && geom.coordinates[0]) {
                    var coords = geom.coordinates[0]; // [ [lng,lat], ... ]
                    var sumLng = 0, sumLat = 0, cnt = 0;
                    for (var k = 0; k < coords.length; k++) { sumLng += coords[k][0]; sumLat += coords[k][1]; cnt++; }
                    if (cnt) {
                      var avg = [sumLng / cnt, sumLat / cnt];
                      if (pointInPoly(avg, coords)) return L.latLng(avg[1], avg[0]);
                    }
                  }

                  if (geom.type === 'MultiPolygon' && geom.coordinates && geom.coordinates[0] && geom.coordinates[0][0]) {
                    var coords = geom.coordinates[0][0];
                    var sumLng = 0, sumLat = 0, cnt = 0;
                    for (var k = 0; k < coords.length; k++) { sumLng += coords[k][0]; sumLat += coords[k][1]; cnt++; }
                    if (cnt) return L.latLng(sumLat / cnt, sumLng / cnt);
                  }
                }

                // final fallback: use bounds center
                if (found.getBounds && typeof found.getBounds === 'function') return found.getBounds().getCenter();
                if (found.getLatLng && typeof found.getLatLng === 'function') return found.getLatLng();
              } catch (e) { /* ignore */ }

              return null;
            }

            function positionCtrl() {
              try {
                var inner = computeInnerLatLng();
                if (!inner) return;
                var p = map.latLngToContainerPoint(inner);
                var left = Math.round(p.x - (ctrl.offsetWidth / 2));
                var top = Math.round(p.y - (ctrl.offsetHeight / 2));

                // clamp inside map container
                var size = map.getSize();
                var maxLeft = size.x - ctrl.offsetWidth - 4;
                var maxTop = size.y - ctrl.offsetHeight - 4;
                if (left < 4) left = 4;
                if (top < 4) top = 4;
                if (left > maxLeft) left = maxLeft;
                if (top > maxTop) top = maxTop;

                ctrl.style.left = left + 'px';
                ctrl.style.top = top + 'px';
              } catch (e) { /* ignore */ }
            }

            // initial position and attach map listeners while editing
            positionCtrl();
            map.on('move zoom resize mousemove', positionCtrl);
            found._posHandler = positionCtrl;
          }

          // wire save/cancel
          document.getElementById('zone-edit-save').onclick = function(){
            // disable editing first
            try { if (found.editing && typeof found.editing.disable === 'function') found.editing.disable(); } catch(e){}
            try { if (found._leaflet_edit && typeof found._leaflet_edit.disable === 'function') found._leaflet_edit.disable(); } catch(e){}

            // remove position listener and hide controls
            try { if (found._posHandler) { map.off('move zoom resize mousemove', found._posHandler); found._posHandler = null; } } catch(e) {}
            updateZone(found);
            if (ctrl) ctrl.style.display = 'none';
            window.currentlyEditing = null;
          };
          document.getElementById('zone-edit-cancel').onclick = function(){
            // revert geometry
            try {
              if (found._originalGeo && found._originalGeo.geometry && found._originalGeo.geometry.coordinates) {
                var coords = found._originalGeo.geometry.coordinates[0].map(function(pt){ return [pt[1], pt[0]]; });
                found.setLatLngs([coords]);
              }
            } catch (e) { console.warn('failed to revert', e); }
            try { if (found.editing && typeof found.editing.disable === 'function') found.editing.disable(); } catch(e){}
            try { if (found._leaflet_edit && typeof found._leaflet_edit.disable === 'function') found._leaflet_edit.disable(); } catch(e){}

            // remove position listener and hide controls
            try { if (found._posHandler) { map.off('move zoom resize mousemove', found._posHandler); found._posHandler = null; } } catch(e) {}
            if (ctrl) ctrl.style.display = 'none';
            window.currentlyEditing = null;
          };
        }

        // helper to submit a polygon to create a zone
        function submitNewZone(geometry, name, layer) {
          var fd = new URLSearchParams();
          fd.append('zone_name', name);
          fd.append('polygon', JSON.stringify(geometry));

          fetch('{{ route('admin.pricing.zones.store_map') }}', {
            method: 'POST', 
            credentials: 'same-origin', 
            headers: { 
              'X-CSRF-TOKEN': '{{ csrf_token() }}', 
              'Accept': 'application/json',
              'Content-Type': 'application/x-www-form-urlencoded'
            }, 
            body: fd.toString()
          }).then(function(res){ 
            if (!res.ok) return res.text().then(function(t){ throw new Error(t || res.statusText); }); 
            return res.json(); 
          }).then(function(json){
            if (json && json.success && json.item) {
              // attach id to the layer so edits/deletes use it
              layer.feature = layer.feature || { type: 'Feature', properties: {} };
              layer.feature.properties.id = json.item.id;
              layer.feature.properties.name = json.item.zone_name || name;
              if (typeof window.showToast === 'function') window.showToast('Zone saved');

              // Soft refresh after save: update list and map without full reload
              setTimeout(function(){
                try {
                  if (typeof window.refreshZones === 'function') window.refreshZones();
                  if (typeof window.loadMap === 'function' && document.getElementById('map-container')) window.loadMap();
                } catch (e) {
                  console.warn('Soft refresh failed, falling back to full reload', e);
                  try { window.location.reload(); } catch(e) { console.warn('Full reload also failed', e); }
                }
              }, 250);
            }
          }).catch(function(err){ 
            console.error('Save zone failed', err); 
            alert('Failed to save zone: ' + (err.message || err)); 
            // remove the created layer
            drawnItems.removeLayer(layer);
          });
        }

        // helper to update existing zone polygon
        function updateZone(layer){
          if (!window.CAN_EDIT_ZONES) { alert('You do not have permission to edit zones.'); return; }
          var id = layer.feature && layer.feature.properties && layer.feature.properties.id;
          if (!id) return;
          var geometry = layer.toGeoJSON().geometry;
          var name = (layer.feature.properties && layer.feature.properties.name) ? layer.feature.properties.name : '';
          var fd = new URLSearchParams(); 
          fd.append('zone_name', name); 
          fd.append('polygon', JSON.stringify(geometry));
          var url = '{{ url('admin/pricing/zones') }}' + '/' + id + '/store-map';
          fetch(url, { 
            method: 'POST', 
            credentials: 'same-origin', 
            headers: { 
              'X-CSRF-TOKEN': '{{ csrf_token() }}', 
              'Accept': 'application/json',
              'Content-Type': 'application/x-www-form-urlencoded'
            }, 
            body: fd.toString() 
          }).then(function(res){ 
            if (!res.ok) return res.text().then(function(t){ throw new Error(t || res.statusText); }); 
            return res.json(); 
          }).then(function(json){ 
            if (json && json.success) { 
              if (typeof window.showToast === 'function') window.showToast('Zone updated'); 

              // Soft refresh after update
              setTimeout(function(){
                try {
                  if (typeof window.refreshZones === 'function') window.refreshZones();
                  if (typeof window.loadMap === 'function' && document.getElementById('map-container')) window.loadMap();
                } catch(e) {
                  console.warn('Soft refresh failed, falling back to reload', e);
                  try { window.location.reload(); } catch(e) { console.warn('Reload failed', e); }
                }
              }, 250);
            } 
          }).catch(function(err){ 
            console.error('Update failed', err); 
            alert('Failed to update zone: ' + (err.message || err)); 
          });
        }

        // Always load leaflet.draw so the polygon icon is available like in the modal
        console.log('Loading Leaflet.draw library for drawing tools');
        loadCss('https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css', '/vendor/leaflet/leaflet.draw.css')
          .then(function(cssLoaded){
            console.log('Leaflet.draw CSS loaded:', cssLoaded);
            return loadScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js', '/vendor/leaflet/leaflet.draw.js');
          })
          .then(function(jsLoaded){
            console.log('Leaflet.draw JS loaded:', jsLoaded);
            console.log('L.Draw available:', typeof L.Draw);
            
            // configure the draw control: polygon icon always visible, but edit/remove only if permitted
            console.log('Creating draw control');
            var drawControl = new L.Control.Draw({
              draw: {
                polygon: { allowIntersection: false },
                polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false
              },
              // disable the edit/remove toolbar (we keep popup-based edit/delete controls)
              edit: {
                featureGroup: drawnItems,
                edit: false,
                remove: false
              }
            });

            console.log('Adding draw control to map');
            map.addControl(drawControl);

            // hide the default draw toolbar placed inside the map (we'll provide a header button instead)
            try {
              var controlEl = map.getContainer().querySelector('.leaflet-draw');
              if (controlEl) {
                controlEl.style.display = 'none';
                console.log('Default draw toolbar hidden');
              }
            } catch(e) {
              console.warn('Failed to hide draw toolbar:', e);
            }

            console.log('=== MAP FULLY INITIALIZED ===');
            console.log('Map object:', map);
            console.log('Map container dimensions:', {
              width: document.getElementById('zones-map').offsetWidth,
              height: document.getElementById('zones-map').offsetHeight
            });

            // Wire the header draw button to the polygon tool
            var drawBtn = document.getElementById('start-draw-btn');
            if (drawBtn) {
              drawBtn.dataset.drawing = '0';
              drawBtn.addEventListener('click', function(e){
                e.preventDefault();
                try {
                  var handler = drawControl && drawControl._toolbars && drawControl._toolbars.draw && drawControl._toolbars.draw._modes && drawControl._toolbars.draw._modes.polygon && drawControl._toolbars.draw._modes.polygon.handler;
                  if (!handler) handler = new L.Draw.Polygon(map, drawControl.options.draw && drawControl.options.draw.polygon ? drawControl.options.draw.polygon : { allowIntersection: false });
                  if (drawBtn.dataset.drawing === '1') {
                    if (handler && handler.disable) handler.disable();
                    drawBtn.dataset.drawing = '0';
                    drawBtn.textContent = 'Draw Polygon';
                  } else {
                    if (handler && handler.enable) handler.enable();
                    drawBtn.dataset.drawing = '1';
                    drawBtn.textContent = 'Cancel';
                  }
                } catch(err) { console.warn('Draw toggle failed', err); alert('Draw tool unavailable'); }
              });

              // keep the button label/state in sync with global draw events
              map.on(L.Draw.Event.DRAWSTART, function(){ drawBtn.dataset.drawing = '1'; drawBtn.textContent = 'Cancel'; });
              map.on(L.Draw.Event.DRAWSTOP, function(){ drawBtn.dataset.drawing = '0'; drawBtn.textContent = 'Draw Polygon'; });
              map.on(L.Draw.Event.CREATED, function(){ drawBtn.dataset.drawing = '0'; drawBtn.textContent = 'Draw Polygon'; });
            }

            // We'll obtain the polygon draw handler and use its temporary `_poly` to show real-time feedback
            var polygonDrawHandler = null;
            var isDrawingDisabled = false;
            var mouseOverExistingZone = false;
            var originalPolygonOnClick = null;

            // ensure Turf is available for robust geometry checks (CDN, no local fallback expected)
            (function(){
              // click handler for popup edit buttons (delegated)
              document.addEventListener('click', function(e){
                var el = e.target;
                if (!el) return;
                if (el.classList && el.classList.contains('zone-edit-btn')) {
                  e.preventDefault();
                  var id = el.getAttribute('data-zone-id');
                  if (id) editZoneOnMap(id);
                }
              });
              if (typeof turf === 'undefined') {
                loadScript('https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js').then(function(){
                  console.log('Turf loaded');
                }).catch(function(err){
                  console.warn('Failed to load Turf.js; overlap checks will be limited', err);
                });
              }

              // helper to check whether a geometry overlaps or is contained by any existing zone
              function findOverlappingLayer(geometry, excludeLayer) {
                if (typeof turf === 'undefined') return null; // cannot check
                try {
                  var newFeature = { type: 'Feature', geometry: geometry };
                  var found = null;

                  drawnItems.eachLayer(function(existing){
                    // exclude the same layer instance (useful when creating where new layer is already added)
                    if (excludeLayer && (existing === excludeLayer)) return;
                    var existingGeo = existing.toGeoJSON && existing.toGeoJSON().geometry;
                    if (!existingGeo) return;
                    var existingFeature = { type: 'Feature', geometry: existingGeo };

                    // Prefer booleanIntersects which is robust across types
                    try {
                      if (turf.booleanIntersects(newFeature, existingFeature)) { found = existing; return; }
                    } catch (ie) {
                      // sometimes booleanIntersects can fail for degenerate geometries; fall back
                      console.warn('turf.booleanIntersects failed for features', ie);
                    }

                    // fallback: use type-aware checks guarded by try/catch
                    try {
                      var tNew = (newFeature.geometry && newFeature.geometry.type) || null;
                      var tExisting = (existingFeature.geometry && existingFeature.geometry.type) || null;

                      // booleanOverlap requires same geometry types (Polygon vs Polygon), so only call when types match
                      if (tNew && tExisting && tNew === tExisting) {
                        if (turf.booleanOverlap(newFeature, existingFeature)) { found = existing; return; }
                      }
                    } catch (boe) {
                      console.warn('turf.booleanOverlap threw', boe);
                    }

                    // other containment/within checks also guarded
                    try {
                      if (turf.booleanContains(existingFeature, newFeature) || turf.booleanWithin(newFeature, existingFeature)) { found = existing; return; }
                    } catch (oth) {
                      // ignore
                    }

                  });

                  return found;
                } catch (e) { console.error('Turf check failed', e); return null; }
              }

              // helper: check whether a point (latlng) lies inside any existing zone
              function isPointInExistingZone(latlng) {
                if (typeof turf === 'undefined') return false;
                try {
                  var pt = turf.point([latlng.lng, latlng.lat]);
                  var inside = false;
                  drawnItems.eachLayer(function(existing){
                    var existingGeo = existing.toGeoJSON && existing.toGeoJSON().geometry;
                    if (!existingGeo) return;
                    var existingFeature = { type: 'Feature', geometry: existingGeo };
                    try {
                      if (turf.booleanPointInPolygon(pt, existingFeature)) { inside = existing; return; }
                    } catch (err) { /* ignore */ }
                  });
                  return inside;
                } catch (err) { console.error('Point check failed', err); return false; }
              }

              // Helper: check whether a geometry is fully inside the UK bounding box
              var UK_BOUNDS = { minLat: 49.5, maxLat: 61.0, minLng: -8.6, maxLng: 2.1 };
              var UK_BBOX_POLYGON = { type: 'Feature', geometry: { type: 'Polygon', coordinates: [[[-8.6,49.5],[2.1,49.5],[2.1,61.0],[-8.6,61.0],[-8.6,49.5]]] } };

              function isGeometryInsideUK(geometry) {
                if (!geometry) return false;
                // try Turf robust check if available
                if (typeof turf !== 'undefined') {
                  try {
                    return !!turf.booleanWithin({ type: 'Feature', geometry: geometry }, UK_BBOX_POLYGON);
                  } catch (e) {
                    // fall back
                  }
                }
                // fallback: check every coordinate in first ring lies within bounds
                try {
                  var coords = geometry.coordinates && geometry.coordinates[0];
                  if (!coords) return false;
                  for (var i = 0; i < coords.length; i++) {
                    var pt = coords[i];
                    var lng = pt[0]; var lat = pt[1];
                    if (lat < UK_BOUNDS.minLat || lat > UK_BOUNDS.maxLat || lng < UK_BOUNDS.minLng || lng > UK_BOUNDS.maxLng) return false;
                  }
                  return true;
                } catch (e) { return false; }
              }

              // When drawing starts, capture the polygon handler instance
              map.on(L.Draw.Event.DRAWSTART, function(e){
                try {
                  polygonDrawHandler = drawControl && drawControl._toolbars && drawControl._toolbars.draw && drawControl._toolbars.draw._modes && drawControl._toolbars.draw._modes.polygon && drawControl._toolbars.draw._modes.polygon.handler;
                  isDrawingDisabled = false;
                  mouseOverExistingZone = false;

                  if (polygonDrawHandler && polygonDrawHandler._onClick) {
                    // store original handler
                    originalPolygonOnClick = polygonDrawHandler._onClick;
                    polygonDrawHandler._onClick = function(ev) {
                      // when drawing disabled due to hover/overlap, ignore clicks
                      if (isDrawingDisabled || mouseOverExistingZone) {
                        // show a brief tooltip or flash
                        return false;
                      }
                      return originalPolygonOnClick.call(this, ev);
                    };
                  }
                } catch (err) { polygonDrawHandler = null; }
              });

              // When a vertex is added, update feedback using the handler's _poly
              map.on('draw:drawvertex', function(e){
                if (!polygonDrawHandler) return;
                var tempPoly = polygonDrawHandler._poly;

                // Update style first
                updateHandlerStyle(tempPoly);

                // If new vertex causes overlap, remove it immediately to prevent creating a line
                try {
                  var geo = tempPoly && tempPoly.toGeoJSON && tempPoly.toGeoJSON().geometry;
                  if (geo) {
                    // use a small tolerance so near-miss polygons (1 inch) are not flagged as overlaps
                    var overlappingNow = findOverlappingLayer(geo, null, 0.0254);
                    var insideNow = isGeometryInsideUK(geo);
                    if (!insideNow) {
                      // remove the last vertex and show message
                      var latlngs = tempPoly.getLatLngs && tempPoly.getLatLngs();
                      if (latlngs && latlngs[0] && latlngs[0].length) {
                        latlngs[0].pop();
                        tempPoly.setLatLngs(latlngs);
                        updateHandlerStyle(tempPoly);
                        var banner = document.getElementById('zone-overlap-error');
                        if (banner) { banner.textContent = 'Cannot draw outside the UK!'; banner.style.display = 'block'; }
                        if (polygonDrawHandler._tooltip && polygonDrawHandler._tooltip.updateContent) {
                          try { polygonDrawHandler._tooltip.updateContent({ text: 'Invalid point — outside UK' }); } catch (tu) {}
                        }
                      }
                    } else if (overlappingNow) {
                      // remove the most recent vertex (last point in the first ring)
                      var latlngs = tempPoly.getLatLngs && tempPoly.getLatLngs();
                      if (latlngs && latlngs[0] && latlngs[0].length) {
                        latlngs[0].pop();
                        tempPoly.setLatLngs(latlngs);
                        updateHandlerStyle(tempPoly);

                        // show banner (keeps visible from mousemove handler as well)
                        var banner = document.getElementById('zone-overlap-error');
                        if (banner) {
                          var name = overlappingNow.feature && overlappingNow.feature.properties && overlappingNow.feature.properties.name ? overlappingNow.feature.properties.name : null;
                          banner.textContent = name ? ('Cannot draw here — overlaps "' + name + '"') : 'Cannot draw over existing zones!';
                          banner.style.display = 'block';
                        }

                        // briefly update tooltip text
                        if (polygonDrawHandler._tooltip && polygonDrawHandler._tooltip.updateContent) {
                          try {
                            polygonDrawHandler._tooltip.updateContent({ text: 'Invalid point — overlaps existing zone' });
                          } catch (tu) {}
                        }
                      }
                    }
                  }
                } catch (err) {
                  console.warn('drawvertex overlap check failed', err);
                }
              });

              // Also update while mouse moves to get more immediate feedback
              map.on('draw:mousemove', function(e){
                if (!polygonDrawHandler) return;
                var tempPoly = polygonDrawHandler._poly;

                // check hover over existing zone
                var hovered = isPointInExistingZone(e.latlng);
                mouseOverExistingZone = !!hovered;

                var banner = document.getElementById('zone-overlap-error');
                if (mouseOverExistingZone) {
                  // show banner and disable drawing
                  if (banner) {
                    var name = (hovered.feature && hovered.feature.properties && hovered.feature.properties.name) ? hovered.feature.properties.name : null;
                    banner.textContent = name ? ('Cannot draw here — overlaps "' + name + '"') : 'Cannot draw over existing zones!';
                    banner.style.display = 'block';
                  }
                  L.DomUtil.addClass(map._container, 'zone-hover-disabled');
                  isDrawingDisabled = true;

                  // update tooltip if available
                  if (polygonDrawHandler && polygonDrawHandler._tooltip && polygonDrawHandler._tooltip.updateContent) {
                    try {
                      polygonDrawHandler._tooltip.updateContent({ text: 'Error: Cannot draw over existing zones!' });
                      if (polygonDrawHandler._tooltip._container) polygonDrawHandler._tooltip._container.classList.add('leaflet-draw-tooltip-error');
                    } catch (tu) { /* ignore */ }
                  }
                } else {
                  if (banner) banner.style.display = 'none';
                  L.DomUtil.removeClass(map._container, 'zone-hover-disabled');
                  isDrawingDisabled = false;

                  // restore tooltip
                  if (polygonDrawHandler && polygonDrawHandler._tooltip && polygonDrawHandler._tooltip.updateContent) {
                    try { polygonDrawHandler._tooltip.updateContent({ text: polygonDrawHandler._tooltip._initialText || '' }); } catch (tu) {}
                    if (polygonDrawHandler._tooltip._container) polygonDrawHandler._tooltip._container.classList.remove('leaflet-draw-tooltip-error');
                  }
                }

                updateHandlerStyle(tempPoly);
              });

              // Reset when drawing stops
              map.on(L.Draw.Event.DRAWSTOP, function(e){
                if (!polygonDrawHandler) return;
                var tempPoly = polygonDrawHandler._poly;
                if (tempPoly && tempPoly.setStyle) tempPoly.setStyle({ color: '#3388ff', weight: 3, fillColor: '#3388ff', fillOpacity: 0.2 });
                // restore any overridden handler
                try {
                  if (originalPolygonOnClick && polygonDrawHandler && polygonDrawHandler._onClick) {
                    polygonDrawHandler._onClick = originalPolygonOnClick;
                  }
                } catch (err) { /* ignore */ }
                polygonDrawHandler = null;
                isDrawingDisabled = false;
                mouseOverExistingZone = false;
                L.DomUtil.removeClass(map._container, 'zone-hover-disabled');
                var banner = document.getElementById('zone-overlap-error'); if (banner) banner.style.display = 'none';
              });

              function updateHandlerStyle(tempPoly) {
                if (!tempPoly || typeof turf === 'undefined' || typeof tempPoly.getLatLngs !== 'function') return;
                try {
                  var latlngs = tempPoly.getLatLngs();
                  if (!latlngs || !latlngs[0] || latlngs[0].length < 3) return; // Need at least 3 points
                  var geo = tempPoly.toGeoJSON().geometry;
                  if (!geo || !geo.coordinates || !geo.coordinates[0]) return;
                  var overlapping = findOverlappingLayer(geo, null);
                  var insideUK = isGeometryInsideUK(geo);
                  var banner = document.getElementById('zone-overlap-error');
                  if (!insideUK) {
                    tempPoly.setStyle({ color: '#ef4444', weight: 3, fillColor: '#ef4444', fillOpacity: 0.2 });
                    isDrawingDisabled = true;
                    if (banner) { banner.textContent = 'Error: Polygon must be entirely within the UK'; banner.style.display = 'block'; }
                  } else if (overlapping) {
                    tempPoly.setStyle({ color: '#ef4444', weight: 3, fillColor: '#ef4444', fillOpacity: 0.2 });
                    isDrawingDisabled = true;
                    if (banner) {
                      var name = overlapping.feature && overlapping.feature.properties && overlapping.feature.properties.name ? overlapping.feature.properties.name : null;
                      banner.textContent = name ? ('Cannot create here — overlaps "' + name + '"') : 'Error: Overlapping with existing zone!';
                      banner.style.display = 'block';
                    }
                  } else {
                    tempPoly.setStyle({ color: '#3388ff', weight: 3, fillColor: '#3388ff', fillOpacity: 0.2 });
                    isDrawingDisabled = false;
                    if (banner && !mouseOverExistingZone) banner.style.display = 'none';
                  }
                } catch (e) { console.error('updateHandlerStyle error', e); }
              }

              // when a new shape is created
              map.on(L.Draw.Event.CREATED, function (e) {
                if (!window.CAN_CREATE_ZONES) { alert('You do not have permission to create zones.'); return; }
                var newLayer = e.layer;

                // if drawing is disabled due to hover/overlap, reject immediately
                if (isDrawingDisabled || mouseOverExistingZone) {
                  alert('Cannot create zone: drawing is disabled because it overlaps an existing zone.');
                  return;
                }

                // check overlap before adding/ prompting
                // include a small tolerance so tiny gaps (1 inch) are tolerated
                var overlapping = findOverlappingLayer(newLayer.toGeoJSON().geometry, null, 0.0254);
                if (overlapping) {
                  var msg = 'This zone overlaps an existing zone' + (overlapping.feature && overlapping.feature.properties && overlapping.feature.properties.name ? (': ' + overlapping.feature.properties.name) : '.');
                  alert(msg);
                  return;
                }

                // safe to add
                drawnItems.addLayer(newLayer);
                // prompt for name, then submit
                var name = prompt('Zone name');
                if (!name) { drawnItems.removeLayer(newLayer); return; }
                newLayer.feature = newLayer.feature || { type: 'Feature', properties: {} };
                newLayer.feature.properties.name = name;

                submitNewZone(newLayer.toGeoJSON().geometry, name, newLayer);
              });

              // when layers are edited
              map.on(L.Draw.Event.EDITED, function(e){ e.layers.eachLayer(function(l){
                // check overlap excluding the edited layer itself
                var overlapping = findOverlappingLayer(l.toGeoJSON().geometry, l);
                if (overlapping) {
                  alert('Edited polygon overlaps existing zone. Changes will be reverted.');
                  // reload layer from server by refreshing the page for simplicity (or reload data)
                  window.location.reload();
                  return;
                }
                updateZone(l);
              }); });

              // when layers are deleted via edit toolbar
              map.on(L.Draw.Event.DELETED, function(e){ 
                e.layers.eachLayer(function(l){ 
                  var id = l.feature && l.feature.properties && l.feature.properties.id; 
                  if (id) { deleteZone(id); } 
                }); 
              });

            })();
          })
          .catch(function(err){ 
            console.warn('Leaflet.draw resources failed to load', err); 
          });

        // Delete helper: sends POST with _method=DELETE to delete endpoint then removes layer
        window.deleteZone = function(id){
          if (!confirm('Delete zone? This cannot be undone.')) return;
          var url = '{{ url('admin/pricing/zones') }}/' + id + '/remove';
          fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: '_method=DELETE'
          }).then(function(res){
            if (!res.ok) {
              return res.text().then(function(t){ throw new Error('Delete failed (status '+res.status+'): '+ (t || res.statusText)); });
            }
            return res.json();
          }).then(function(json){
            if (json && json.success) {
              // remove feature from map
              drawnItems.eachLayer(function(l){ if (l.feature && l.feature.properties && l.feature.properties.id == id) { drawnItems.removeLayer(l); } });
              if (typeof window.showToast === 'function') window.showToast('Zone deleted');
            } else {
              alert((json && json.message) ? json.message : 'Delete failed');
            }
          }).catch(function(err){ console.error('Delete error', err); alert(err.message || 'Failed to delete zone.'); });
        };



        // --- ZONES LIST LOGIC ---
        (function(){
            var toggleBtn = document.getElementById('toggle-zones-list-btn');
            var closeBtn = document.getElementById('close-zones-list-btn');
            var sidebar = document.getElementById('zones-list-sidebar');
            var listUl = document.getElementById('zones-list-ul');

            if (toggleBtn && sidebar && listUl) {
                // Populate List
                var features = (geo && geo.features) ? geo.features : [];
                // sort by name
                features.sort(function(a,b){
                    var na = (a.properties && a.properties.name) ? a.properties.name.toLowerCase() : '';
                    var nb = (b.properties && b.properties.name) ? b.properties.name.toLowerCase() : '';
                    if(na<nb) return -1; if(na>nb) return 1; return 0;
                });

                if (features.length === 0) {
                    listUl.innerHTML = '<li class="text-gray-400 text-sm p-2 text-center">No zones found</li>';
                } else {
                    features.forEach(function(f){
                        if (!f.properties || !f.properties.id) return;
                        var li = document.createElement('li');
                        li.className = 'cursor-pointer hover:bg-indigo-50 p-2 rounded text-sm text-gray-700 transition-colors border border-transparent hover:border-indigo-100 flex items-center justify-between';
                        li.innerHTML = '<span>' + (f.properties.name || 'Set Name') + '</span>';
                        li.onclick = function(){
                            var id = f.properties.id;
                            var foundLayer = null;
                            drawnItems.eachLayer(function(l){
                                if (l.feature && l.feature.properties && l.feature.properties.id == id) {
                                    foundLayer = l;
                                }
                            });
                            if (foundLayer) {
                                // Close sidebar on mobile/small screens if needed, or keep open. User didn't specify. Keeping open.
                                map.fitBounds(foundLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
                                // open popup slightly below the zone center so it doesn't overlap inline controls
                                try {
                                    var center = foundLayer.getBounds().getCenter();
                                    var p = map.latLngToContainerPoint(center);
                                    p.y += 40; // move popup 40px down
                                    var openAt = map.containerPointToLatLng(p);
                                    foundLayer.openPopup(openAt);
                                } catch (e) {
                                    // fallback
                                    foundLayer.openPopup();
                                }

                                // temporary highlight
                                var originalStyle = { color: '#2b6cb0', weight: 2, fillOpacity: 0.2 };
                                foundLayer.setStyle({ color: '#f59e0b', weight: 4, fillOpacity: 0.4 }); // yellow/orange highlight
                                setTimeout(function(){
                                    if (window.currentlyEditing !== foundLayer) { // don't revert if currently editing
                                         foundLayer.setStyle(originalStyle);
                                    }
                                }, 2000);
                            } else {
                                alert('Layer found in list but not on map (sync error)');
                            }
                        };
                        listUl.appendChild(li);
                    });

                    // list is now a flex-fill container so no special class is required for scroll behaviour
                    // the list will scroll naturally when it exceeds the sidebar height
                }

                // Toggle Events
                toggleBtn.onclick = function(){
                    sidebar.classList.remove('translate-x-full');
                    // When the list is shown, fit the map to show all zones if present; otherwise re-center on London
                    try {
                      if (geo && geo.features && geo.features.length && layer && typeof layer.getBounds === 'function') {
                        map.fitBounds(layer.getBounds());
                      } else if (map && typeof map.setView === 'function') {
                        map.setView([51.5074, -0.1278], 13);
                      }
                    } catch (e) { /* ignore */ }
                };
                if (closeBtn) {
                     closeBtn.onclick = function() {
                        sidebar.classList.add('translate-x-full');
                     };
                }
            }
        })();

      } catch (err) { 
        console.error('=== MAP INITIALIZATION ERROR ===');
        console.error('Error details:', err); 
        console.error('Error stack:', err.stack);
      }
    }).catch(function(err){ 
      console.error('=== FAILED TO LOAD MAP LIBRARIES ===');
      console.error('Error:', err); 
      console.error('Error message:', err && err.message ? err.message : String(err));
      var mapEl = document.getElementById('zones-map'); 
      var statusEl = document.getElementById('zones-map-status-text'); 
      if (statusEl) statusEl.textContent = 'Failed to load map resources: ' + (err && err.message ? err.message : String(err)); 
      if (mapEl) mapEl.style.background = '#fff'; 
    });

    // global handler to surface any uncaught errors on the map container
    window.addEventListener('error', function(e){ 
      console.error('=== GLOBAL ERROR CAUGHT ===');
      console.error('Error event:', e);
      var statusEl = document.getElementById('zones-map-status-text'); 
      if (statusEl) statusEl.textContent = 'Map error: ' + (e && e.message ? e.message : 'see console'); 
    });

  console.log('=== MAP INITIALIZATION SCRIPT END ===');
})();
</script>
@endsection