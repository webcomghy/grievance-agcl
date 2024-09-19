<div class="w-full lg:w-1/3 bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-8">
        <h3 class="text-2xl font-semibold text-gray-800 mb-8">Timeline</h3>
        <div class="flow-root">
            <ul role="list" class="relative">
                @foreach($grievance->transactions->sortByDesc('created_at') as $transaction)
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white
                            @if($transaction->status === 'Forwarded')
                                bg-blue-500
                            @elseif($transaction->status === 'Resolved')
                                bg-green-500
                            @elseif($transaction->status === 'Closed')
                                bg-red-500
                            @elseif($transaction->status === 'Assigned')
                                bg-orange-500
                            @else
                                bg-gray-500
                            @endif
                        ">
                            @if($transaction->status === 'Forwarded')
                                <i class="fas fa-forward text-white text-xs"></i>
                            @elseif($transaction->status === 'Resolved')
                                <i class="fas fa-check text-white text-xs"></i>
                            @elseif($transaction->status === 'Closed')
                                <i class="fas fa-times text-white text-xs"></i>
                            @elseif($transaction->status === 'Assigned')
                                <i class="fas fa-user-plus text-white text-xs"></i>
                            @else
                                <i class="fas fa-circle text-white text-xs"></i>
                            @endif
                        </span>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $transaction->status }}</h3>
                            <p class="mb-2 text-base font-normal text-gray-500">{{ $transaction->description }}</p>
                            <time class="block mb-2 text-sm font-normal leading-none text-gray-400">{{ $transaction->created_at->format('M d, Y') }}</time>
                        </div>
                    </li>
                @endforeach
                <li class="ml-6">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-green-500 rounded-full -left-4 ring-4 ring-white">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </span>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Grievance Received</h3>
                        <p class="mb-2 text-base font-normal text-gray-500">Grievance received by the grid admin</p>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400">{{ $grievance->created_at->format('M d, Y') }}</time>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>