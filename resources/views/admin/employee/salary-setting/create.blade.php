<!-- Salary Settings Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" id="salarySettingsModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Configure Salary for {{ $employee->first_name ?? '' }}</h2>
            <button
                class="text-gray-600 hover:text-gray-900 text-2xl font-bold focus:outline-none cursor-pointer select-none leading-none"
                id="closeSalaryModal" type="button">&times;</button>
        </div>

        <form class="space-y-4" action="{{ route('employees.salary.store', ['employee' => $employee->id]) }}"
            method="POST">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="base_salary">Base Salary</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="base_salary" name="base_salary" type="number" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="allowance">Allowance</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="allowance" name="allowance" type="number" min="0" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="overtime_rate">Overtime Rate <small
                        class="text-gray-500">/hr</small></label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="overtime_rate" name="overtime_rate" type="number" min="0" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_date">Effective Date</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 flat-picker"
                    type="text" name="effective_date" readonly="readonly" value="{{ old('effective_date') }}">
            </div>

            <div class="space-y-2 pt-4 border-t">
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="is_epf_enrolled" name="is_epf_enrolled" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="is_epf_enrolled">Is EPF Enrolled?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="is_cit_enrolled" name="is_cit_enrolled" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="is_cit_enrolled">Is CIT Enrolled?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="is_taxable"
                        name="is_taxable" type="checkbox" value="1" checked>
                    <label class="text-sm text-gray-700" for="is_taxable">Is Taxable?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="is_deduction_enabled" name="is_deduction_enabled" type="checkbox" value="1" checked>
                    <label class="text-sm text-gray-700" for="is_deduction_enabled">Is Deduction Enabled?</label>
                </div>
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
