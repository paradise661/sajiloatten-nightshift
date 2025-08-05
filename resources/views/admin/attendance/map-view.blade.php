@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')

    <div class="flex flex-col md:flex-row justify-center gap-8 mt-6">
        @if ($checkin)
            <div class="w-1/2 max-w-md bg-white rounded-2xl shadow-lg border border-gray-200 flex flex-col">
                <div class="p-2 border-b border-gray-100">
                    <h3 class="text-center font-semibold text-lg text-gray-700">Check-in Location</h3>
                </div>
                <div class="flex-grow rounded-b-2xl" id="map" style="height: 350px;"></div>
            </div>
        @endif

        @if ($checkout)
            <div class="w-1/2 max-w-md bg-white rounded-2xl shadow-lg border border-gray-200 flex flex-col">
                <div class="p-2 border-b border-gray-100">
                    <h3 class="text-center font-semibold text-lg text-gray-700">Check-out Location</h3>
                </div>
                <div class="flex-grow rounded-b-2xl" id="checkoutmap" style="height: 350px;"></div>
            </div>
        @endif
    </div>

    <div class="text-center mt-4 text-sm text-gray-600 space-x-6">
        @if ($checkin)
            <span>
                <img class="inline w-5 h-5 mr-1" src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Checkin">
                Check-in Location
            </span>
        @endif
        @if ($checkout)
            <span>
                <img class="inline w-5 h-5 mr-1" src="https://cdn-icons-png.flaticon.com/512/684/684908.png" alt="Checkout">
                Check-out Location
            </span>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            @if ($checkin)
                const lat = parseFloat({{ $checkin['latitude'] }});
                const lng = parseFloat({{ $checkin['longitude'] }});

                const map = L.map('map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                const checkinIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -30]
                });

                L.marker([lat, lng], {
                    icon: checkinIcon
                }).addTo(map).bindPopup('Checkin Location').openPopup();
            @endif

            @if ($checkout)
                const checkoutlat = parseFloat({{ $checkout['latitude'] }});
                const checkoutlng = parseFloat({{ $checkout['longitude'] }});

                const checkoutmap = L.map('checkoutmap').setView([checkoutlat, checkoutlng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(checkoutmap);

                const checkoutIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -30]
                });

                L.marker([checkoutlat, checkoutlng], {
                    icon: checkoutIcon
                }).addTo(checkoutmap).bindPopup('Checkout Location').openPopup();
            @endif
        });
    </script>
@endsection
