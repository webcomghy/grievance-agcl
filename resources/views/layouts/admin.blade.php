<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    <title>@yield('title', 'Grievance Redressal System')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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

    <!-- Include Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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

        .alert-fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
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
                
                    <a href="{{ route('dashboard') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-home mr-2 sidebar-icon"></i><span class="menu-text">Dashboard</span>
                    </a>
           
                    @can('manage_roles_and_permissions')
                        <a href="{{ route('roles-permissions.index') }}"
                            class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                            <i class="fas fa-user-lock mr-2 sidebar-icon"></i><span class="menu-text">Roles & Permissions</span>
                        </a>
                    @endcan
                    @can('view_meter_uploads')
                        <a href="{{ route('meter_uploads.index') }}"
                            class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                            <i class="fas fa-file-upload mr-2 sidebar-icon"></i><span class="menu-text">Meter Uploads</span>
                        </a>
                    @endcan
                    @can('view_grivances')
                        <a href="{{ route('grievances.index') }}"
                            class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                            <i class="fas fa-list mr-2 sidebar-icon"></i><span class="menu-text">Grievances (All)</span>
                        </a>
                    @endcan

                    <a href="{{ route('grievances.create') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-plus mr-2 sidebar-icon"></i><span class="menu-text">Create Grievance</span>
                    </a>

                    <a href="{{ route('grievances.inbox') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-inbox mr-2 sidebar-icon"></i><span class="menu-text">Inbox</span>
                    </a>

                    <a href="{{ route('grievances.outbox') }}"
                        class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 flex items-center">
                        <i class="fas fa-paper-plane mr-2 sidebar-icon"></i><span class="menu-text">Outbox</span>
                    </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header id="topNav" class="bg-white shadow-md rounded-b-lg flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button onclick="window.history.back()" class="mr-4 bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 focus:outline-none">
                        Back
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    @yield('action_buttons')                   
                    <div class="flex items-center mr-4">
                        <span id="currentTime" class="text-gray-600"></span>
                    </div>

                    {{-- <div class="relative">
                        <button onclick="toggleNotificationDropdown()"
                            class="text-gray-600 hover:text-gray-800 mr-4 relative">
                            <i class="fas fa-bell"></i>
                            @if (Auth::user()->unreadNotifications->count())
                                <span
                                    class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </button>
                        <div id="notificationDropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10 hidden">
                            <div class="max-h-60 overflow-y-auto">
                                @if (Auth::user()->notifications->isEmpty())
                                    <p class="px-4 py-2 text-gray-500">No new notifications.</p>
                                @else
                                    @foreach (Auth::user()->notifications as $notification)
                                        <a href="{{ $notification->data['url'] ?? '#' }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ $notification->data['message'] }}
                                            <span
                                                class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div> --}}

                    <div class="relative">
                        <button onclick="toggleDropdown()" class="flex items-center focus:outline-none">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User"
                                class="w-8 h-8 rounded-full mr-2"> <!-- Default user avatar from CDN -->
                            <span id="userName">{{auth()->user()->name}}</span> <!-- Replace with authenticated user's name -->
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="userDropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10 hidden">
                            {{-- <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a> --}}
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
                <!-- Alert Messages -->
                @foreach(['success' => 'green', 'error' => 'red'] as $type => $color)
                    @if(session($type))
                        <div id="{{ $type }}Alert" class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">{{ ucfirst($type) }}!</strong>
                            <span class="block sm:inline">{{ session($type) }}</span>
                            <button onclick="closeAlert('{{ $type }}Alert')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-{{ $color }}-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </button>
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div id="validationAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Validation Error!</strong>
                        <span class="block sm:inline">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button onclick="closeAlert('validationAlert')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                        </button>
                    </div>
                @endif

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

            // Change the toggle button icon
            if (sidebar.classList.contains('sidebar-collapsed')) {
                toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>'; // Change icon to expand
            } else {
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

        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            alert.classList.add('alert-fade-out');
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }

        // Auto-close alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    closeAlert(alert.id);
                }, 5000);
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    @yield('scripts')

    <!--Start of Tawk.to Script-->
    {{-- <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/66d9397b50c10f7a00a42add/1i708h2t5';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script> --}}
    <!--End of Tawk.to Script-->

   
</body>

</html>