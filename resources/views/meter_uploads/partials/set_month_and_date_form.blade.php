
<form action="{{ route('meter_uploads.set_month_and_date') }}" method="POST">
    @csrf
    <div class="space-y-4">
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
            <select id="month" name="month" autocomplete="month"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Select Month</option>
                @foreach ($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
            <select id="year" name="year" autocomplete="year"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Select Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
            <input type="date" id="from_date" name="from_date" autocomplete="from_date"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
            <input type="date" id="to_date" name="to_date" autocomplete="to_date"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>


        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                Submit
            </button>
        </div>
    </div>
</form>