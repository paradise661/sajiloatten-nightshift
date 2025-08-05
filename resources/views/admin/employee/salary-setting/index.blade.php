@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Manage Salary <i>({{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }})</i>
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div>
                    <div class="box custom-box">
                        <div class="box-header justify-between">
                            <div class="box-title">
                                Salary Records
                                @if ($salaries->isNotEmpty())
                                    <span
                                        class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $salaries->count() }}</span>
                                @endif
                            </div>
                            <div class="prism-toggle">
                                <button
                                    class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2 openSalaryModal"
                                    type="button">
                                    New Salary <i class="ri-add-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive" style="overflow: visible">
                                <table class="table whitespace-nowrap min-w-full">
                                    <thead>
                                        <tr class="border-b border-defaultborder">
                                            <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                                            <th class="text-start px-4 py-2" scope="col">Base Salary</th>
                                            <th class="text-start px-4 py-2" scope="col">Allowance</th>
                                            <th class="text-start px-4 py-2" scope="col">Overtime Rate</th>
                                            <th class="text-start px-4 py-2" scope="col">Is PF Enrolled?</th>
                                            <th class="text-start px-4 py-2" scope="col">Is CIT Enrolled?</th>
                                            <th class="text-start px-4 py-2" scope="col">Is Taxable?</th>
                                            <th class="text-start px-4 py-2" scope="col">Is Deduction Enabled?</th>
                                            <th class="text-start px-4 py-2" scope="col">Effective Date</th>
                                            <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($salaries->isNotEmpty())
                                            @foreach ($salaries as $key => $salary)
                                                <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                                    <th class="px-4 py-2" scope="row">{{ $key + 1 }}
                                                    </th>
                                                    <td class="px-4 py-2 text-xs">{{ $salary->base_salary ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">{{ $salary->allowance ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">{{ $salary->overtime_rate ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $salary->is_epf_enrolled == 1 ? 'Yes' : 'No' ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $salary->is_cit_enrolled == 1 ? 'Yes' : 'No' ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $salary->is_taxable == 1 ? 'Yes' : 'No' ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $salary->is_deduction_enabled == 1 ? 'Yes' : 'No' ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $salary->effective_date ?? '-' }}
                                                    </td>
                                                    <td class="text-end px-4 py-2">
                                                        <div class="relative inline-flex" x-cloak x-data="{ open: false }">
                                                            <button
                                                                class="flex justify-center items-center size-8 text-sm font-semibold rounded-md border shadow-md border-gray-400 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-none"
                                                                @click="open = !open">
                                                                <svg class="flex-none size-4 text-gray-600"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="12" cy="12" r="1" />
                                                                    <circle cx="12" cy="5" r="1" />
                                                                    <circle cx="12" cy="19" r="1" />
                                                                </svg>
                                                            </button>

                                                            <div class="absolute z-50 bg-white shadow-md rounded-md mt-2 w-40 transition duration-150 ease-in-out max-w-fit"
                                                                x-show="open" @click.away="open = false" x-transition
                                                                @close-dropdown.window="open = false"
                                                                style="z-index: 9999;top: 25px; right: -14px;">

                                                                <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400 open-edit-salary-modal"
                                                                    data-salary='@json($salary)'
                                                                    data-employee-name="{{ $salary->employee->first_name ?? 'Unknown' }}"
                                                                    href="javascript:void(0);">
                                                                    Edit
                                                                </a>

                                                                <form
                                                                    action="{{ route('employees.salary.destroy', [$employee->id, $salary->id]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        class="text-sm action-btn hover:bg-red-100 ti-btn-wave text-red-600 w-full flex !justify-start px-4 py-2 delete_button"
                                                                        type="submit">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10"
                                                    style="text-align: center; height: 100px; vertical-align: middle; color: #6b7280; display: table-cell;">
                                                    <div
                                                        style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                                                        <p class="text-lg font-semibold">No data available</p>
                                                        <p class="mt-2 text-sm">There are no records to display at the
                                                            moment.
                                                            Please check again later.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.employee.salary-setting.create')
    @include('admin.employee.salary-setting.edit')
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.openSalaryModal').click(function() {
                $('#salarySettingsModal').removeClass('hidden');
            });

            $('#closeSalaryModal').click(function() {
                $('#salarySettingsModal').addClass('hidden');
            });
        });

        $(document).on('click', '.open-edit-salary-modal', function() {
            window.dispatchEvent(new Event('close-dropdown'));

            const salary = $(this).data('salary');
            const employeeName = $(this).data('employee-name');

            $('#edit_base_salary').val(salary.base_salary);
            $('#edit_allowance').val(salary.allowance);
            $('#edit_overtime_rate').val(salary.overtime_rate);
            $('#edit_effective_date').val(salary.effective_date);
            $('#edit_is_epf_enrolled').prop('checked', salary.is_epf_enrolled);
            $('#edit_is_cit_enrolled').prop('checked', salary.is_cit_enrolled);
            $('#edit_is_taxable').prop('checked', salary.is_taxable);
            $('#edit_is_deduction_enabled').prop('checked', salary.is_deduction_enabled);

            $('#editSalaryForm').attr('action', `/employees/${salary.user_id}/salary/${salary.id}`);
            $('#editEmployeeName').text(employeeName);
            $('#editSalaryModal').removeClass('hidden');
        });

        $('#closeEditSalaryModal').click(function() {
            $('#editSalaryModal').addClass('hidden');
        });
    </script>
@endsection
