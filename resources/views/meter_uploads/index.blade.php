@extends('layouts.admin')
@section('title', 'Meter Uploads')
@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Meter Uploads</h2>
    <table class="min-w-full bg-white border border-gray-200" id="meter_uploads_table">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Consumer No</th>
                <th class="py-2 px-4 border-b">Phone Number</th>
                <th class="py-2 px-4 border-b">Year Month</th>
                <th class="py-2 px-4 border-b">Reading</th>
                <th class="py-2 px-4 border-b" style="width: 100px;">Image</th> <!-- Set fixed width for image column -->
                <th class="py-2 px-4 border-b">Location</th> <!-- Changed to Location -->
            </tr>
        </thead>
    </table>
</div>

<!-- Modal for displaying larger image -->
<div id="imageModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-md w-full max-h-screen"> <!-- Added max-h-screen -->
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-lg font-bold">Image</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="p-4 overflow-auto"> <!-- Added overflow-auto -->
            <img id="modalImage" src="" class="w-full h-auto" alt="Meter Image">
        </div>
    </div>
</div>

<!-- Modal for displaying location -->
<div id="locationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-md w-full max-h-screen"> <!-- Added max-h-screen -->
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-lg font-bold">Location</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeLocationModal()">&times;</button>
        </div>
        <div id="map" class="p-4" style="height: 400px;"></div> <!-- Added map container -->
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#meter_uploads_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('meter_uploads.index') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'consumer_no', name: 'consumer_no' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'yearMonth', name: 'yearMonth' },
                { data: 'reading', name: 'reading' },
                { data: 'image', name: 'image', render: function(data, type, full, meta) {
                    var imageUrl = 'https://assamgas.co.in/' + data;
                    return '<img src="' + imageUrl + '" class="w-12 h-12 object-cover cursor-pointer" onclick="showImageModal(\'' + imageUrl + '\')">';
                }},
                { data: null, name: 'location', orderable: false, searchable: false, render: function(data, type, full, meta) {
                    var latitude = full.latitude;
                    var longitude = full.longitude;
                    return '<span class="cursor-pointer" title="Lat: ' + latitude + ', Lon: ' + longitude + '" onclick="showLocationModal(' + latitude + ', ' + longitude + ')">View Location</span>';
                }},
            ],
            dom: 'lBfrtip', // 'l' for length changing input control, 'B' for buttons, 'f' for filtering input, 'r' for processing display element, 't' for the table, 'i' for table information summary, 'p' for pagination control
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

    });

    function showImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    var map; // Declare map variable outside the function

    function showLocationModal(latitude, longitude) {
        if (map) {
            map.remove(); // Remove the existing map instance
        }
        map = L.map('map').setView([latitude, longitude], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup('Loading location details...')
            .openPopup();

        // Fetch location details using Nominatim API
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
            .then(response => response.json())
            .then(data => {
                var locationName = data.display_name;
                L.marker([latitude, longitude]).addTo(map)
                    .bindPopup('<b>Location:</b> ' + locationName + '<br><b>Latitude:</b> ' + latitude + '<br><b>Longitude:</b> ' + longitude)
                    .openPopup();
            })
            .catch(error => {
                console.error('Error fetching location details:', error);
            });

        document.getElementById('locationModal').classList.remove('hidden');
        setTimeout(function() {
            map.invalidateSize(); // Ensure the map is properly resized
            map.setView([latitude, longitude], 13); // Center the map on the marker
        }, 100);
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.add('hidden');
        document.getElementById('map').innerHTML = ""; // Clear the map container
    }
</script>
@endsection
