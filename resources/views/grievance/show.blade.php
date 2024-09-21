@extends('layouts.admin')

@section('title', 'Grievance Details')

@section('action_buttons')
    <span class="text-gray-600 mr-4">
        <i class="far fa-clock"></i>
        @if(isset($grievance->transactions->last()->assigned_to))
            {{ $grievance->transactions->last()->created_at->diffForHumans() }} has passed since {{ $grievance->transactions->last()->status }}
        @else
            {{ $grievance->created_at->diffForHumans() }} has passed since the submission
        @endif
    </span>

    @if($grievance->status === 'Pending' || $grievance->status === 'Forwarded' || $grievance->status === 'Assigned')
        @if(isset($grievance->transactions->last()->assigned_to))
            @if($grievance->transactions->last()->assigned_to == auth()->user()->id)
                @if($grievance->status !== 'Assigned')
                    <button onclick="openAsignModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-user-plus"></i>
                    </button>
                    <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-forward"></i>
                    </button>
                @endif
            @endif
            @if($grievance->transactions->last()->assigned_to == auth()->user()->id || auth()->user()->hasRole('admin'))
                @if($grievance->status !== 'Closed' && $grievance->status !== 'Resolved')
                    <button onclick="openCloseModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-close"></i>
                    </button>
                    <button onclick="openResolveModal()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-check"></i>
                    </button>
                @endif
            @endif
        @elseif (auth()->user()->hasRole('admin'))
            @if($grievance->status !== 'Assigned')
                <button onclick="openAsignModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-user-plus"></i>
                </button>
                <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-forward"></i>
                </button>
            @endif
            @if($grievance->status !== 'Closed' && $grievance->status !== 'Resolved')
                <button onclick="openCloseModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-close"></i>
                </button>
                <button onclick="openResolveModal()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-check"></i>
                </button>
            @endif
        @endif
    @endif
    
    <button onclick="exportTransactions()" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded mr-2">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        {{-- <p><strong>Ticket No:</strong> {{ $grievance->ticket_number }}</p> --}}
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
            </div>
        </div>

        <!-- Notesheet Timeline -->
        @include('grievance.partials.timeline')
    </div>

    {{-- <div class="mt-6 flex justify-between">
        <a href="{{ route('grievances.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
        #grievanceCard, #grievanceCard * {
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
    function exportTransactions() {
        // Prepare data for export
        var data = [
            ['Transactions'],
            ['Status', 'Description', 'Date']
        ];

        // Add initial grievance submission
        data.push(['Grievance Received', 'Grievance received by the grid admin', '{{ $grievance->created_at->format('M d, Y') }}']);
        
        // Add all transactions
        @foreach($grievance->transactions->sortBy('created_at') as $transaction)
            data.push([
                '{{ $transaction->status }}',
                '{{ $transaction->description }}',
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
</script>


@endsection
