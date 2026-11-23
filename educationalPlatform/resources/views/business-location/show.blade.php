@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Business location</p>
        <h1 class="mt-1 text-3xl font-bold">Find Axiom Membership</h1>
        <p class="mt-2 text-slate-600">Our membership services are based at {{ $location['name'] }}.</p>
    </div>
    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location['name'].' '.$location['address']) }}" target="_blank" rel="noopener" class="rounded-lg border border-indigo-200 px-4 py-2.5 font-semibold text-indigo-700 hover:bg-indigo-50">Open in Google Maps</a>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
    <section class="overflow-hidden rounded-xl bg-white shadow-sm">
        @if (config('services.google_maps.key'))
            <div id="business-map" class="h-96 w-full"></div>
            <script>
                function initBusinessMap() {
                    const location = { lat: {{ $location['latitude'] }}, lng: {{ $location['longitude'] }} };
                    const map = new google.maps.Map(document.getElementById('business-map'), { center: location, zoom: 17 });
                    const marker = new google.maps.Marker({ position: location, map, title: @json($location['name']) });
                    const infoWindow = new google.maps.InfoWindow({ content: '<strong>{{ $location['name'] }}</strong><br>{{ $location['address'] }}' });
                    marker.addListener('click', () => infoWindow.open({ anchor: marker, map }));
                }
                window.initBusinessMap = initBusinessMap;
            </script>
            <script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initBusinessMap"></script>
        @else
            <div class="flex h-96 items-center justify-center bg-slate-100 p-6 text-center text-slate-600">
                <div><p class="font-semibold">Map preview unavailable</p><p class="mt-2 text-sm">Add a restricted <code>GOOGLE_MAPS_KEY</code> to enable the interactive map.</p></div>
            </div>
        @endif
    </section>

    <aside class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Address</p>
        <h2 class="mt-3 text-xl font-bold">{{ $location['name'] }}</h2>
        <p class="mt-3 text-slate-600">{{ $location['address'] }}</p>
        <dl class="mt-6 space-y-4 border-t border-slate-200 pt-5 text-sm">
            <div><dt class="text-slate-500">Latitude</dt><dd class="mt-1 font-mono text-slate-700">{{ $location['latitude'] }}</dd></div>
            <div><dt class="text-slate-500">Longitude</dt><dd class="mt-1 font-mono text-slate-700">{{ $location['longitude'] }}</dd></div>
        </dl>
    </aside>
</div>
@endsection
