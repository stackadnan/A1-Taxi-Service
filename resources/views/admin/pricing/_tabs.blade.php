<style>
  /* Prevent hover effects on active pricing tabs */
  .pricing-tab.bg-indigo-600.text-white:hover {
    background-color: rgb(79 70 229) !important;
    color: white !important;
    border-color: rgb(79 70 229) !important;
  }
</style>
<nav class="mb-4">
  <ul class="flex flex-wrap gap-3" role="tablist">
    <li><a href="{{ route('admin.pricing.postcodes.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.postcodes.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="postcode">Postcode Charges</a></li>
    <li><a href="{{ route('admin.pricing.mileage.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.mileage.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="mileage">Mileage Charges</a></li>
    <li><a href="{{ route('admin.pricing.zones.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.zones.index') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="zone">Zone Charges</a></li>
    <li><a href="{{ route('admin.pricing.zones.map') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.zones.map') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="map">Zones (Map)</a></li>
    <li><a href="{{ route('admin.pricing.others.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.others.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="other">Other Charges</a></li>
  </ul>
</nav>

@include('admin.pricing._modals')

<script>
// Make tabs perform in-page switches without full page reload when on the pricing index
(function(){
  var tabs = document.querySelectorAll('.pricing-tab');
  if (!tabs || !tabs.length) return;
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(e){
      try {
        var name = tab.getAttribute('data-tab');
        
        // If we're on the pricing index page (or any page that contains tab panes), intercept and activate in-page
        var panesExist = document.querySelector('[data-pane]');
        if (panesExist) {
          e.preventDefault();
          // If a pane with that name exists, trigger the existing tab mechanism
          var targetBtn = document.querySelector('[data-tab="'+name+'"]');
          if (targetBtn) {
            targetBtn.click();
            // update browser URL without reloading
            history.replaceState(null, '', '#'+name);
            return;
          }
          // Fallback: if the pane isn't pre-rendered, attempt to call the load function used in index page
          if (name === 'postcode' && typeof window.loadPostcodes === 'function') { window.loadPostcodes(); history.replaceState(null, '', '#postcode'); return; }
          if (name === 'mileage' && typeof window.loadMileage === 'function') { window.loadMileage(); history.replaceState(null, '', '#mileage'); return; }
          if (name === 'zone' && typeof window.loadZones === 'function') { window.loadZones(); history.replaceState(null, '', '#zone'); return; }
          if (name === 'other' && typeof window.loadOther === 'function') { window.loadOther(); history.replaceState(null, '', '#other'); return; }
          if (name === 'map' && typeof window.loadMap === 'function') { window.loadMap(); history.replaceState(null, '', '#map'); return; }
        }
        // Otherwise, allow normal navigation (full page load) to the route (e.g., when on a different page)
      } catch (err) { console.error('Tab switch failed', err); }
    });
  });
})();
</script>