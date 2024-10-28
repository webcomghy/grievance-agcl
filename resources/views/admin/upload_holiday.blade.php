@extends('layouts.admin')

@section('title', 'Upload Holidays')

@section('content')
<style>
    .container {
        max-width: 1200px;

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
        overflow: hidden; 
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
        position: relative; 
    }

    .holiday:hover::after {
        content: attr(title); 
        position: absolute;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap; 
        top: 100%; 
        left: 50%; 
        transform: translateX(-50%); 
        z-index: 10; 
    }

    .form-container {
        padding-right: 20px;
        flex: 1 1 20%; 
    }

    .calendar-container {
        flex: 1 1 78%; 
    }
</style>
<h2 class="text-3xl font-bold text-gray-800">Upload Holidays</h2>
<div class="container">

    <div class="form-container">
        <form action="{{ route('holidays.import') }}" method="POST" enctype="multipart/form-data" class="mt-4" onsubmit="disableSubmitButton(this)">
            @csrf
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">Select File</label>
                <input type="file" name="file" id="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">Upload</button>
            
            <div class="mt-4 flex items-center">
                <span class="bg-green-200 text-green-800 font-semibold py-1 px-2 rounded mr-2">Download:</span>
                <a href="{{ asset('static/holiday.csv') }}" class="text-blue-500 hover:underline">Sample File</a>
            </div>
        </form>
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
    function disableSubmitButton(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerText = 'Uploading...'; 
    }

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
