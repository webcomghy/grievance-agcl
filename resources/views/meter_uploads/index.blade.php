@extends('layouts.admin')
@section('title', 'Meter Uploads')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Meter Uploads</h2>
    </div>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full" id="meter_uploads_table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Month</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Image</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal for displaying larger image -->
<div id="imageModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-3xl w-full max-h-screen">
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-lg font-bold">Meter Image</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="p-4 overflow-auto">
            <img id="modalImage" src="" class="w-full h-auto" alt="Meter Image">
        </div>
    </div>
</div>

<!-- Modal for displaying location -->
<div id="locationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-3xl w-full max-h-screen">
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-lg font-bold">Location</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeLocationModal()">&times;</button>
        </div>
        <div id="map" class="h-96"></div>
    </div>
</div>
@endsection

@section('scripts')


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
                    return '<img src="' + imageUrl + '" class="w-16 h-16 object-cover rounded-md cursor-pointer" onclick="showImageModal(\'' + imageUrl + '\')">';
                }},
                { data: null, name: 'location', orderable: false, searchable: false, render: function(data, type, full, meta) {
                    var latitude = full.latitude;
                    var longitude = full.longitude;
                    return '<button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-md text-sm" onclick="showLocationModal(' + latitude + ', ' + longitude + ')">View Location</button>';
                }},
            ],
            dom: 'lBfrtip',
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

    var map;

    function showLocationModal(latitude, longitude) {
        if (map) {
            map.remove();
        }
        map = L.map('map').setView([latitude, longitude], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup('Loading location details...')
            .openPopup();

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
            map.invalidateSize();
            map.setView([latitude, longitude], 13);
        }, 100);
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.add('hidden');
    }
</script>
@endsection
