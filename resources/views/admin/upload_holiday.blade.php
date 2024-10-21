@extends('layouts.admin')

@section('title', 'Upload Holidays')

@section('content')
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .calendar-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-top: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        padding: 16px;
        border-bottom: 1px solid #ddd;
    }

    .calendar-title {
        font-size: 1.5rem;
        color: #333;
    }

    .nav-button {
        padding: 8px 16px;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .nav-button:hover {
        background-color: #e0e0e0;
    }

    .calendar-body {
        padding: 16px;
    }

    .weekday-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        margin-bottom: 8px;
    }

    .weekday {
        font-weight: bold;
        color: #666;
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-auto-rows: 100px;
        gap: 2px;
    }

    .day {
        position: relative;
        padding: 8px;
        background-color: #fff;
        border: 1px solid #ddd;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .today {
        background-color: #f0f8ff;
        border-color: #007bff;
    }

    .empty {
        background-color: #f8f8f8;
    }

    .holiday {
        margin-top: auto;
        background-color: #ffe4e1;
        color: #d9534f;
        padding: 4px;
        font-size: 0.8rem;
        border-radius: 4px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }


    .form-container {
        padding-right: 20px;
        flex: 1 1 20%; /* Updated to take up 20% width */
    }

    .calendar-container {
        flex: 1 1 78%; /* Adjusted to take up remaining width */
    }
</style>
<h2 class="text-3xl font-bold text-gray-800">Upload Holidays</h2>
<div class="container">

    <div class="form-container">
        
        <form action="{{ route('holidays.import') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">Select File</label>
                <input type="file" name="file" id="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">Upload</button>
        </form>

        @if(session('success'))
            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth" class="nav-button">Previous</button>
            <h2 id="calendarHeader" class="calendar-title"></h2>
            <button id="nextMonth" class="nav-button">Next</button>
        </div>

        <div class="calendar-body">
            <div class="weekday-grid">
                <div class="weekday">Sun</div>
                <div class="weekday">Mon</div>
                <div class="weekday">Tue</div>
                <div class="weekday">Wed</div>
                <div class="weekday">Thu</div>
                <div class="weekday">Fri</div>
                <div class="weekday">Sat</div>
            </div>

            <div id="days" class="days-grid"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const holidays = @json($holidays);
        const calendarHeader = document.getElementById('calendarHeader');
        const daysElement = document.getElementById('days');
        const prevMonthButton = document.getElementById('prevMonth');
        const nextMonthButton = document.getElementById('nextMonth');
        let currentDate = new Date();

        function getHolidaysForDate(date) {
            return holidays.filter(holiday => {
                const holidayDate = new Date(holiday.date);
                return holidayDate.getDate() === date.getDate() && 
                       holidayDate.getMonth() === date.getMonth() && 
                       holidayDate.getFullYear() === date.getFullYear();
            });
        }

        function createCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthNames = ["January", "February", "March", "April", "May", "June", 
                                "July", "August", "September", "October", "November", "December"];
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const firstDay = new Date(year, month, 1).getDay();

            calendarHeader.textContent = `${monthNames[month]} ${year}`;
            daysElement.innerHTML = '';

            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'day empty';
                daysElement.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayHolidays = getHolidaysForDate(date);
                const dayCell = document.createElement('div');
                const isToday = new Date().toDateString() === date.toDateString();
                
                dayCell.className = 'day';
                if (isToday) dayCell.classList.add('today');

                let cellContent = `<span>${day}</span>`;

                if (dayHolidays.length > 0) {
                    cellContent += `
                        <div class="holiday" title="${dayHolidays.map(h => h.name).join(', ')}">
                            ${dayHolidays[0].name}${dayHolidays.length > 1 ? ` (+${dayHolidays.length - 1})` : ''}
                        </div>
                    `;
                }

                dayCell.innerHTML = cellContent;
                daysElement.appendChild(dayCell);
            }
        }

        prevMonthButton.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            createCalendar();
        });

        nextMonthButton.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            createCalendar();
        });

        createCalendar();
    });
</script>
@endsection
