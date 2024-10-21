<x-consumer-layout>
    @section('title', 'Grievance Details')
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



    <div class="container mx-auto px-4 py-8">
        
        <div class="flex justify-between mb-4">
            <div class="text-left">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Grievance Details for {{ $grievance->ticket_number }}</h1>
            </div>
            <div class="">
                {{-- <button onclick="exportTransactions()" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-excel"></i>
                </button> --}}
                
                <button onclick="printGrievanceCard()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-print"></i>
                </button>
                <button onclick="openWithdrawModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Withdraw Complaint
                </button>
            </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-6">
            <div id="grievanceCard" class="bg-white shadow-md rounded-lg overflow-hidden lg:w-2/3">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $grievance->category }}</h2>
                    <p><strong>Subcategory:</strong> {{ $grievance->subcategory ?? 'N/A' }}</p>
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
    </div>

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
                    '{{ ($transaction->status === "Resolved" || $transaction->status === "Closed") ? $transaction->createdBy->username : ($transaction->assignedTo->username ?? "") }}',
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

        function openWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('hidden');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
        }
    </script>

    <div id="withdrawModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('grievances.withdraw', $grievance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="Withdrawn">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Withdraw Complaint
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to withdraw this complaint? This action cannot be undone.
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <label for="withdraw_description" class="block text-sm font-medium text-gray-700">Description (optional)</label>
                                    <textarea id="withdraw_description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Withdraw
                        </button>
                        <button type="button" onclick="closeWithdrawModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-consumer-layout>
