@extends('layouts.admin')
@section('title', 'Meter Uploads')
@section('action_buttons')
    @can('set_date_for_upload')
        <a href="{{ route('meter_uploads.set_dates') }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out"> 
            <i class="fas fa-calendar"></i>
        </a>
    @endcan
@endsection
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
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Month</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Image</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded At</th>
                    <th class="hidden">Latitude</th> <!-- Hidden Latitude Column -->
                    <th class="hidden">Longitude</th> <!-- Hidden Longitude Column -->
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal for displaying larger image -->
@include('meter_uploads.partials.modals.image_modal')

<!-- Modal for displaying location -->
@include('meter_uploads.partials.modals.location_modal')

@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function() {
        $('#meter_uploads_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('meter_uploads.index') }}',
            columns: [
                { data: null, name: 'id', orderable: false, searchable: false, render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; // Calculate serial number
                }},
                { data: 'meter_no', name: 'meter_no' },
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
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return new Date(data).toLocaleString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric', 
                        hour: '2-digit', minute: '2-digit', hour12: false 
                    }); // Format date to "Sep 21, 2024 06:44"
                }},
                { data: 'latitude', name: 'latitude', visible: false }, // Include hidden Latitude column
                { data: 'longitude', name: 'longitude', visible: false } // Include hidden Longitude column
            ],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':not(:nth-child(7), :nth-child(8))' // Exclude Image and Location columns
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':not(:nth-child(7), :nth-child(8))' // Exclude Image and Location columns
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:nth-child(7), :nth-child(8))' // Exclude Image and Location columns
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':not(:nth-child(7), :nth-child(8))' // Exclude Image and Location columns
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:nth-child(7), :nth-child(8))' // Exclude Image and Location columns
                    }
                }
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
