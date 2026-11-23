@csrf

<div class="grid gap-6 sm:grid-cols-2">
    <div><label for="first-name" class="font-medium">First name</label><input id="first-name" name="first_name" value="{{ old('first_name', $member->first_name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required></div>
    <div><label for="last-name" class="font-medium">Last name</label><input id="last-name" name="last_name" value="{{ old('last_name', $member->last_name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required></div>
    <div><label for="member-email" class="font-medium">Email</label><input id="member-email" type="email" name="email" value="{{ old('email', $member->email ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required></div>
    <div><label for="member-phone" class="font-medium">Phone</label><input id="member-phone" name="phone" value="{{ old('phone', $member->phone ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200"></div>
    <div><label for="member-category" class="font-medium">Category</label><select id="member-category" name="membership_category_id" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required><option value="">Choose category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('membership_category_id', $member->membership_category_id ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div><label for="member-level" class="font-medium">Level</label><select id="member-level" name="membership_level_id" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required><option value="">Choose level</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected(old('membership_level_id', $member->membership_level_id ?? '') == $level->id)>{{ $level->name }}</option>@endforeach</select></div>
    <div><label for="joined-on" class="font-medium">Joined on</label><input id="joined-on" type="date" name="joined_on" value="{{ old('joined_on', isset($member) ? $member->joined_on?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" required></div>
    <div class="sm:col-span-2"><label for="member-address" class="font-medium">Address <span class="font-normal text-slate-500">(optional)</span></label><input id="member-address" name="address" value="{{ old('address', $member->address ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-200" placeholder="Street, city, or learning center"></div>
</div>

<div class="mt-8 border-t border-slate-200 pt-6">
    <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-lg font-semibold">Location and live weather</h2><p class="mt-1 text-sm text-slate-500">Choose the member's location on the map. Coordinates are stored with the member record.</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Optional</span></div>
    <input id="latitude" type="hidden" name="latitude" value="{{ old('latitude', $member->latitude ?? '') }}"><input id="longitude" type="hidden" name="longitude" value="{{ old('longitude', $member->longitude ?? '') }}">
    @if (config('services.google_maps.key'))
        <div id="member-map" class="mt-4 h-72 overflow-hidden rounded-xl border border-slate-200 bg-slate-100"></div>
        <p id="coordinates" class="mt-3 text-sm text-slate-500">Click the map to choose a location.</p>
        <p id="weather" class="mt-3 rounded-lg bg-sky-50 p-3 text-sm text-sky-800">Weather will appear after a location is selected.</p>
        <script>
            let memberMap;
            let memberMarker;
            function loadMemberWeather(lat, lon) {
                const weather = document.getElementById('weather');
                weather.textContent = 'Loading weather...';
                fetch(`{{ route('weather.show') }}?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`, { headers: { Accept: 'application/json' } }).then(response => response.ok ? response.json() : Promise.reject()).then(data => weather.textContent = `${data.place}: ${data.temperature ?? '—'}°C, ${data.description ?? 'Weather unavailable'}, humidity ${data.humidity ?? '—'}%`).catch(() => weather.textContent = 'Weather is currently unavailable. Add OPENWEATHER_KEY to enable it.');
            }
            function setMemberLocation(lat, lon) {
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lon.toFixed(7);
                document.getElementById('coordinates').textContent = `Latitude: ${lat.toFixed(5)}, Longitude: ${lon.toFixed(5)}`;
                if (memberMarker) memberMarker.setMap(null);
                memberMarker = new google.maps.Marker({ position: { lat, lng: lon }, map: memberMap });
                loadMemberWeather(lat, lon);
            }
            function initMemberMap() {
                const savedLat = Number(document.getElementById('latitude').value);
                const savedLon = Number(document.getElementById('longitude').value);
                const center = { lat: savedLat || 14.5995, lng: savedLon || 120.9842 };
                memberMap = new google.maps.Map(document.getElementById('member-map'), { center, zoom: savedLat && savedLon ? 13 : 11 });
                memberMap.addListener('click', event => setMemberLocation(event.latLng.lat(), event.latLng.lng()));
                if (savedLat && savedLon) setMemberLocation(savedLat, savedLon);
            }
            window.initMemberMap = initMemberMap;
        </script>
        <script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initMemberMap"></script>
    @else
        <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">Google Maps is not configured yet. Add a restricted <code>GOOGLE_MAPS_KEY</code> to enable map selection.</div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2"><div><label for="latitude-fallback" class="text-sm font-medium">Latitude</label><input id="latitude-fallback" type="number" step="any" min="-90" max="90" value="{{ old('latitude', $member->latitude ?? '') }}" oninput="document.getElementById('latitude').value = this.value" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5"></div><div><label for="longitude-fallback" class="text-sm font-medium">Longitude</label><input id="longitude-fallback" type="number" step="any" min="-180" max="180" value="{{ old('longitude', $member->longitude ?? '') }}" oninput="document.getElementById('longitude').value = this.value" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5"></div></div>
    @endif
</div>

<p class="mt-6 text-sm text-slate-500">Membership number is generated automatically.</p><button class="rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white hover:bg-indigo-700">{{ $buttonText }}</button>
