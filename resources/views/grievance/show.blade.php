@extends('layouts.admin')

@section('title', 'Grievance Details')

@section('action_buttons')
<span class="text-gray-600 mr-4">
    <i class="far fa-clock"></i>
    @php
        $holidays = \App\Models\Holiday::pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        $lastTransaction = $grievance->transactions->last();
        $referenceDate = $lastTransaction ? $lastTransaction->created_at : $grievance->created_at;

        $currentDate = \Carbon\Carbon::now();

        $nonHolidayDays = 0;

        for ($date = clone $referenceDate; $date->lessThanOrEqualTo($currentDate); $date->addDay()) {
            if (!in_array($date->format('Y-m-d'), $holidays)) {
                $nonHolidayDays++;
            }
        }
    @endphp

    @if($nonHolidayDays > 0)
        @if(isset($lastTransaction->assigned_to))
            {{ $nonHolidayDays }} day{{ $nonHolidayDays > 1 ? 's' : '' }} have passed since {{ $lastTransaction->status }}
        @else
            {{ $nonHolidayDays }} day{{ $nonHolidayDays > 1 ? 's' : '' }} have passed since the submission
        @endif
    @else
        No days have passed since the reference date due to holidays.
    @endif
</span>

@if( $grievance->status === 'Forwarded' || $grievance->status === 'Assigned')
    @if(isset($grievance->transactions->last()->assigned_to))
        @if($grievance->transactions->last()->assigned_to == auth()->user()->id)
            @if($grievance->status !== 'Assigned')
                <button onclick="openAsignModal()"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-user-plus"></i>
                </button>
                <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-forward"></i>
                </button>
            @endif
        @endif

        @if($grievance->transactions->last()->assigned_to == auth()->user()->id || auth()->user()->hasRole('admin'))
            @if($grievance->status !== 'Closed' && $grievance->status !== 'Resolved')
                {{-- @can('can_close')
                    <button onclick="openCloseModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-close"></i>
                    </button>
                @endcan --}}
                @can('can_resolve')
                    <button onclick="openResolveModal()"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-check"></i>
                    </button>
                @endcan
            @endif
        @endif
    @elseif (auth()->user()->hasRole('admin'))
        @if($grievance->status !== 'Assigned')
            <button onclick="openAsignModal()"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-user-plus"></i>
            </button>
            <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-forward"></i>
            </button>
        @endif

        @if($grievance->status !== 'Closed' && $grievance->status !== 'Resolved')
            {{-- @can('can_close')
                <button onclick="openCloseModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-close"></i>
                </button>
            @endcan --}}
            @can('can_resolve')
                <button onclick="openResolveModal()"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-check"></i>
                </button>
            @endcan
        @endif
    @endif
@elseif($grievance->status === 'Pending' )
    <button onclick="openAsignModal()"
        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2">
            <i class="fas fa-user-plus"></i>
    </button>
    <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
        <i class="fas fa-forward"></i>
    </button>
@endif

<button onclick="exportTransactions()"
    class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded mr-2">
    <i class="fas fa-file-excel"></i>
</button>

<button onclick="printGrievanceCard()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
    <i class="fas fa-print"></i>
</button>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Grievance Details for {{ $grievance->ticket_number }}</h1>

    <div class="flex flex-col lg:flex-row gap-6">
        <div id="grievanceCard" class="bg-white shadow-md rounded-lg overflow-hidden lg:w-2/3">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $grievance->category }}</h2>
                <p><strong>Subcategory:</strong> {{ $grievance->subcategory ?? 'N/A' }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p><strong>Consumer No:</strong> {{ $grievance->consumer_no }}</p>
                        <p><strong>CA No:</strong> {{ $grievance->ca_no }}</p>
                        <p><strong>Name:</strong> {{ $grievance->name }}</p>
                        <p><strong>Phone:</strong> {{ $grievance->phone }}</p>
                        <p><strong>Email:</strong> {{ $grievance->email }}</p>
                    </div>
                    <div>
                        <p><strong>Status:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $grievance->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($grievance->status === 'Forwarded' ? 'bg-blue-100 text-blue-800' : 
                                   ($grievance->status === 'Resolved' ? 'bg-green-100 text-green-800' : 
                                   ($grievance->status === 'Assigned' ? 'bg-orange-500 text-white-800' : 
                                   'bg-red-100 text-red-800'))) }}">
                                {{ $grievance->status }}
                            </span>
                        </p>
                        <p><strong>Priority:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $grievance->priority === 'High' ? 'bg-red-100 text-red-800' : 
                                   ($grievance->priority === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-green-100 text-green-800') }}">
                                {{ $grievance->priority }}
                            </span>
                        </p>
                        <p><strong>Submitted on:</strong> {{ $grievance->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Description:</h3>
                    <p class="text-gray-600">{{ $grievance->description }}</p>
                </div>

                <!-- Display admin remark -->
                @if(isset($grievance->admin_remark))
                <div class="mt-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Remark:</h3>
                    <p class="text-gray-600">{{ $grievance->admin_remark }}</p>
                </div>
                @endif

                @if(isset($grievance->file_path))
                <div class="mt-4">
                    <a href="{{ asset($grievance->file_path) }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-download"></i> Download File
                    </a>
                </div>
                @endif

                @if(isset($grievance->resolved_file_path))
                <div class="mt-4">
                    <a href="{{ asset($grievance->resolved_file_path) }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-download"></i> Download Resolved Proof
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Notesheet Timeline -->
        @include('grievance.partials.timeline')
    </div>

    {{-- <div class="mt-6 flex justify-between">
        <a href="{{ route('grievances.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to List
        </a>
    </div> --}}
</div>

<!-- Asign Modal -->
@include('grievance.partials.modals.asign-modal')
<!-- Forward Modal -->
@include('grievance.partials.modals.forward-modal')

<!-- Close Modal -->
@include('grievance.partials.modals.close-modal')

<!-- Resolve     Modal -->
@include('grievance.partials.modals.resolve-modal')


<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #grievanceCard,
        #grievanceCard * {
            visibility: visible;
        }

        #grievanceCard {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
        }

        .lg\:w-2\/3 {
            width: 100% !important;
        }
    }

    .flow-root ul::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 2px;
        background: #e5e7eb;
    }
</style>

<script>
    function openAsignModal(){
        document.getElementById('asignModal').classList.remove('hidden');
    }

    function closeAsignModal(){
        document.getElementById('asignModal').classList.add('hidden');
    }

    function openForwardModal() {
        document.getElementById('forwardModal').classList.remove('hidden');
    }

    function closeForwardModal() {
        document.getElementById('forwardModal').classList.add('hidden');
    }

    function openCloseModal() {
        document.getElementById('closeModal').classList.remove('hidden');
    }

    function closeCloseModal() {
        document.getElementById('closeModal').classList.add('hidden');
    }

    function openResolveModal() {
        document.getElementById('resolveModal').classList.remove('hidden');
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
    }

    function printGrievanceCard() {
        window.print();
    }
</script>

<script>
    function printGrievanceCard() {
        window.print();
    }
    
    function exportTransactions() {
        // Prepare data for export
        var data = [
            ['Transactions'],
            ['Status', 'Description', 'Current User', 'Assigned To (Employee)','Date']
        ];

        // Add initial grievance submission
        data.push(['Grievance Received', 'Grievance received by the admin', '', '', '{{ $grievance->created_at->format('M d, Y') }}']);
        
        // Add all transactions
        @foreach($grievance->transactions->sortBy('created_at') as $transaction)
            data.push([
                '{{ $transaction->status }}',
                '{{ $transaction->description }}',
               '{{ $transaction->status === "Resolved" || $transaction->status === "Closed" ? ($transaction->createdBy ? $transaction->createdBy->username : "") : ($transaction->assignedTo ? $transaction->assignedTo->username : "") }}',
                '{{ $transaction->employee_id == 0 ? "" : $transaction->employee_id }}',
                '{{ $transaction->created_at->format('M d, Y') }}'
            ]);
        @endforeach

        // Create workbook and worksheet
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(data);

        // Set column widths
        ws['!cols'] = [
            {wch: 15}, // Status
            {wch: 50}, // Description
            {wch: 15}  // Date
        ];

        // Apply bold formatting to the header rows
        var range = XLSX.utils.decode_range(ws['!ref']);
        for (var R = range.s.r; R <= 1; ++R) { // Apply to first two rows
            for (var C = range.s.c; C <= range.e.c; ++C) {
                var address = XLSX.utils.encode_cell({r: R, c: C});
                if (!ws[address]) continue;
                ws[address].s = { font: { bold: true } };
            }
        }

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Transactions");

        // Generate Excel file with cell styles
        XLSX.writeFile(wb, "{{ $grievance->ticket_number }}_transactions.xlsx", {
            cellStyles: true
        });
    }

    $(document).ready(function() {


});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        showActionButtonsInfo();
    });

    function showActionButtonsInfo() {
        Swal.fire({
            title: 'Action Buttons Information',
            html: `
                <ul style="text-align: left;">
                    <li><strong>Assign <i class="fas fa-user-plus" style="color: #F59E0B;"></i> :</strong> Assign the grievance to a user.</li>
                    <li><strong>Forward <i class="fas fa-forward" style="color: #3B82F6;"></i> :</strong> Forward the grievance to another user.</li>
                    <li><strong>Close <i class="fas fa-close" style="color: #EF4444;"></i> :</strong> Close the grievance and mark it as rejected.</li>
                    <li><strong>Resolve <i class="fas fa-check" style="color: #22C55E;"></i> :</strong> Mark the grievance as resolved.</li>
                    <li><strong>Export <i class="fas fa-file-excel" style="color: #A855F7;"></i> :</strong> Export transaction details to Excel.</li>
                    <li><strong>Print <i class="fas fa-print" style="color: #22C55E;"></i> :</strong> Print the grievance details.</li>
                </ul>
            `,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }
</script>





@endsection




