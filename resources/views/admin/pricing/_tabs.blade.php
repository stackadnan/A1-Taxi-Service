<style>
  /* Prevent hover effects on active pricing tabs */
  .pricing-tab.bg-indigo-600.text-white:hover {
    background-color: rgb(79 70 229) !important;
    color: white !important;
    border-color: rgb(79 70 229) !important;
  }
</style>
@php($canCreatePricing = auth()->check() && auth()->user()->hasPermission('pricing.create'))
@php($canEditPricing = auth()->check() && auth()->user()->hasPermission('pricing.edit'))

<nav class="mb-4">
  <ul class="flex flex-wrap gap-3" role="tablist">
    <li><a href="{{ route('admin.pricing.postcodes.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.postcodes.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="postcode">Postcode Charges</a></li>
    <li><a href="{{ route('admin.pricing.mileage.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.mileage.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="mileage">Mileage Charges</a></li>
    <li><a href="{{ route('admin.pricing.index') }}#zone" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.zones.index') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="zone">Zone Charges</a></li>
    <li><a href="{{ route('admin.pricing.index') }}#map" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.zones.map') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="map">Zones (Map)</a></li>
    <li><a href="{{ route('admin.pricing.others.index') }}" class="pricing-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ request()->routeIs('admin.pricing.others.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="other">Other Charges</a></li>
  </ul>
</nav>

@if(!$canCreatePricing && !$canEditPricing)
<div class="mb-4 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
  You are in read-only mode for pricing. You can view records, but cannot add, edit, or delete.
</div>
@endif

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
        
        // If we're on the pricing index page (or any page that contains tab panes), intercept and keep the navigation in-page.
        var panesExist = document.querySelector('[data-pane]');
        if (panesExist) {
          e.preventDefault();
          return;
        }
        // Otherwise, allow normal navigation (full page load) to the route (e.g., when on a different page)
      } catch (err) { console.error('Tab switch failed', err); }
    });
  });
})();
</script>