<!-- Bank Details Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" id="bankDetailsModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Add Bank Details for {{ $employee->first_name ?? '' }}</h2>
            <button class="text-gray-600 hover:text-gray-900 text-2xl font-bold focus:outline-none cursor-pointer"
                id="closeBankDetailsModal" type="button">&times;</button>
        </div>

        <form class="space-y-4" action="{{ route('employees.bank.store', ['employee' => $employee->id]) }}"
            method="POST">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="bank_name">Bank Name</label>
                <select
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="bank_name" name="bank_name" required>
                    <option value="">-- Select Bank --</option>
                    @foreach (collect(\App\Models\Bank::getNepalBankList())->where('status', 1) as $bank)
                        <option value="{{ $bank['name'] }}">{{ $bank['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="account_name">Account Name</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="account_name" name="account_name" type="text"
                    value="{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="account_number">Account Number</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="account_number" name="account_number" type="text" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="account_type">Account Type</label>
                <select
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="account_type" name="account_type">
                    <option value="">Select</option>
                    <option value="Saving">Saving</option>
                    <option value="Current">Current</option>
                    <option value="Salary">Salary</option>
                </select>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="is_default"
                    type="checkbox" name="is_default" value="1">
                <label class="text-sm text-gray-700" for="is_default">Set as default bank</label>
            </div>

            <div class="pt-4 flex justify-start">
                <button class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                    type="submit">
                    <i class="ri-check-line"></i> Submit
                </button>
            </div>
        </form>
    </div>
</div>
