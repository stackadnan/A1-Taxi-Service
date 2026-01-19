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

  /* Scroll behaviour for zones list when many items */
  /* Constrain to a sensible max so scroll appears reliably when items > 5 */
  #zones-list-ul.zones-list-scroll { overflow-y: auto !important; max-height: min(260px, calc(100vh - 14rem)); }
  /* Thin, subtle scrollbar for supported browsers */
  #zones-list-ul.zones-list-scroll::-webkit-scrollbar { width: 8px; }
  #zones-list-ul.zones-list-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.16); border-radius: 6px; }

  /* Hide the default Leaflet draw toolbar inside the main map — we provide a header button instead */
  #zones-map .leaflet-draw { display: none !important; }
</style>

<div class="flex items-center justify-between mb-4">
  <div class="flex items-center gap-2">
    @if (auth()->check() && auth()->user()->hasPermission('pricing.create'))
      <button id="start-draw-btn" class="px-3 py-2 bg-red-500 text-white border border-red-500 rounded text-sm">Draw Zone</button>
    @endif
    <button id="toggle-zones-list-btn" class="px-3 py-2 bg-indigo-600 text-white rounded">Show Zones</button>
  </div>
</div>

<!-- Sidebar List (fixed overlay, outside map to avoid clipping and stacking issues) -->
<div id="zones-list-sidebar" class="fixed top-20 right-6 h-[calc(100vh-6rem)] w-64 bg-white shadow-lg overflow-y-auto transform translate-x-full transition-transform duration-300 border-l border-gray-200" style="z-index:99999; pointer-events:auto;">
    <div class="flex items-center justify-between p-3 border-b border-gray-200 bg-gray-50">
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

    if (toggleBtn) toggleBtn.addEventListener('click', function(e){ e.preventDefault(); openSidebar(); });
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

        console.log('=== MAP FULLY INITIALIZED ===');
        console.log('Map should now be visible');

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

  console.log('=== MAP INITIALIZATION SCRIPT END ===');
})();
</script>
