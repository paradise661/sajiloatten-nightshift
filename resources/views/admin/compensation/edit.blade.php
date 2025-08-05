<!-- Edit Salary Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" id="editCompensationModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Edit Compensation</h2>
            <button
                class="text-gray-600 hover:text-gray-900 text-2xl font-bold focus:outline-none cursor-pointer select-none leading-none"
                id="closeEditCompensationModal" type="button">&times;</button>
        </div>

        <form class="space-y-4" id="editCompensationForm" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-600" for="title">Compensation Title</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="title" name="title" type="text" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600" for="employee">Employee</label>
                <select
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="user_id" required name="user_id">
                    <option value="" selected disabled>Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600" for="amount">Amount</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="amount" name="amount" type="number" min="1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600" for="month">Date</label>
                @if (session('calendar') == 'BS')
                    <input id="month"
                        class="nepali-datepicker mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        value="{{ App\Services\DateService::ADToBS(date('Y-m-d')) }}" name="month" type="text"
                        required />
                @else
                    <input id="month"
                        class="flat-picker mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        value="{{ date('Y-m-d') }}" name="month" type="text" required />
                @endif
                {{-- <input
                    class="flat-picker mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="month" value="{{ date('Y-m-d') }}" name="month" type="text" required /> --}}
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600" for="type">Type</label>
                <select
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="type" required name="type">
                    <option value="earning">Earning</option>
                    <option value="deduction">Deduction</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600" for="remarks">Remarks</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="remarks" name="remarks" type="text" required />
            </div>

            <div class="flex items-center space-x-2">
                <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="is_taxable"
                    name="is_taxable" type="checkbox" value="1">
                <label class="ml-2 text-sm text-gray-700" for="is_taxable">Is Taxable?</label>
            </div>

            <div class="pt-4 flex justify-start">
                <button class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                    type="submit">
                    <i class="ri-check-line"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
