<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grievance Redressal System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <style>
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }

        .sidebar-collapsed .menu-text {
            display: none;
        }

        .sidebar-collapsed .sidebar-icon {
            margin-right: 0;
        }

        .sidebar-collapsed #adminPanelTitle {
            display: none;
        }

        #toggleSidebar {
            display: block;
            /* Ensure the toggle button is always displayed */
        }
    </style>
    @yield('styles')
</head>

<body class="bg-gray-100">
    <div class="flex h-screen" id="app">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gradient-to-b from-blue-400 to-blue-500 text-white w-64 flex-shrink-0 overflow-y-auto sidebar-transition">
            <div class="p-4 flex items-center justify-between">
                <h1 id="adminPanelTitle" class="text-lg font-bold">Admin Panel</h1>
                <button id="toggleSidebar" class="text-white focus:outline-none">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            <nav class="mt-4">
                {{-- @can('view dashboard') --}}
                    <a href="{{ route('dashboard') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-home mr-2 sidebar-icon"></i><span class="menu-text">Dashboard</span>
                    </a>
                {{-- @endcan --}}
                    <a href="{{ route('meter_uploads.index') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-file-upload mr-2 sidebar-icon"></i><span class="menu-text">Meter Uploads</span>
                    </a>

            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header id="topNav" class="bg-white shadow-md rounded-b-lg flex items-center justify-between p-4">
                <div class="flex items-center">
                    <input type="text" placeholder="Search..."
                        class="border rounded px-2 py-1 focus:outline-none focus:ring focus:border-blue-200">
                    <button
                        class="ml-2 bg-blue-400 text-white px-4 py-1 rounded hover:bg-blue-500 focus:outline-none transition duration-200">Search</button>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center mr-4">
                        <span id="currentTime" class="text-gray-600"></span>
                    </div>

                    <div class="relative">
                        <button onclick="toggleDropdown()" class="flex items-center focus:outline-none">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User"
                                class="w-8 h-8 rounded-full mr-2"> <!-- Default user avatar from CDN -->
                            <span id="userName">{{auth()->user()->name}}</span> <!-- Replace with authenticated user's name -->
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="userDropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10 hidden">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>

                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="bg-white rounded-lg shadow-md p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        }

        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const topNav = document.getElementById('topNav');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-16');
            sidebar.classList.toggle('sidebar-collapsed');

            // Hide or show the top navigation bar
            if (sidebar.classList.contains('sidebar-collapsed')) {
                topNav.classList.add('hidden'); // Hide top navigation
                toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>'; // Change icon to expand
            } else {
                topNav.classList.remove('hidden'); // Show top navigation
                toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>'; // Change icon to collapse
            }
        });

        // Function to update the current time
        function updateTime() {
            const now = new Date();
            const options = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('currentTime').textContent = now.toLocaleTimeString([], options);
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial call to set the time immediately

        function toggleNotificationDropdown() {
            document.getElementById('notificationDropdown').classList.toggle('hidden');
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.fas.fa-bell')) {
                var dropdowns = document.getElementsByClassName("notificationDropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('hidden') === false) {
                        openDropdown.classList.add('hidden');
                    }
                }
            }
        }
    </script>
    @yield('scripts')
</body>

</html>