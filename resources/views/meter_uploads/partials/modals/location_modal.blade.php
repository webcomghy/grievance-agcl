<div id="locationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-3xl w-full max-h-screen">
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-lg font-bold">Location</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeLocationModal()">&times;</button>
        </div>
        <div id="map" class="h-96"></div>
    </div>
</div>