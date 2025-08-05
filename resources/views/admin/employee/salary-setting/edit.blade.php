<!-- Edit Salary Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" id="editSalaryModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Edit Salary for <span id="editEmployeeName"></span></h2>
            <button
                class="text-gray-600 hover:text-gray-900 text-2xl font-bold focus:outline-none cursor-pointer select-none leading-none"
                id="closeEditSalaryModal" type="button">&times;</button>
        </div>

        <form class="space-y-4" id="editSalaryForm" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700" for="edit_base_salary">Base Salary</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-gray-100 cursor-not-allowed focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="edit_base_salary" name="base_salary" type="number" required readonly />
                <p class="mt-1 text-xs text-gray-500 italic">* Base Salary cannot be edited here. To change, please
                    create a new salary record.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="edit_allowance">Allowance</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="edit_allowance" name="allowance" type="number" min="0" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="edit_overtime_rate">Overtime Rate <small
                        class="text-gray-500">/hr</small></label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    id="edit_overtime_rate" name="overtime_rate" type="number" min="0" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_date">Effective Date</label>
                <input
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 flat-picker"
                    id="edit_effective_date" type="text" name="effective_date" readonly="readonly"
                    value="{{ old('effective_date') }}">
            </div>

            <div class="space-y-2 pt-4 border-t">
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="edit_is_epf_enrolled" name="is_epf_enrolled" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="edit_is_epf_enrolled">Is EPF Enrolled?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="edit_is_cit_enrolled" name="is_cit_enrolled" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="edit_is_cit_enrolled">Is CIT Enrolled?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="edit_is_taxable" name="is_taxable" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="edit_is_taxable">Is Taxable?</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        id="edit_is_deduction_enabled" name="is_deduction_enabled" type="checkbox" value="1">
                    <label class="text-sm text-gray-700" for="edit_is_deduction_enabled">Is Deduction Enabled?</label>
                </div>
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
