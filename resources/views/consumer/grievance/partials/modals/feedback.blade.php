<div id="feedbackModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ session()->has('mobile_number') ? route('grievances.withdrawotp', $grievance) : route('grievances.withdraw', $grievance) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Feedback
                            </h3>
                            <div class="mt-2 mb-4">
                                <p class="text-sm text-gray-700">
                                    Please provide your feedback on the grievance resolution.
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Satisfaction</label>
                                <div class="flex items-center">
                                    <input type="radio" id="satisfied" name="satisfaction" value="Satisfied" class="mr-2">
                                    <label for="satisfied" class="mr-4">Satisfied</label>
                                    <input type="radio" id="not_satisfied" name="satisfaction" value="Not Satisfied" class="mr-2">
                                    <label for="not_satisfied">Not Satisfied</label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="satisfaction_remark" class="block text-sm font-medium text-gray-700">Remark (optional)</label>
                                <textarea id="satisfaction_remark" name="satisfaction_remark" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <input type="hidden" name="status" value="Feedback">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Submit Feedback
                    </button>
                    <button type="button" onclick="closeFeedbackModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>