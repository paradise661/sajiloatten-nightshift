@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Bank Details <i>({{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }})</i>
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
                        <div class="flex justify-end">
                            <button
                                class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2 openBankDetailsModal"
                                type="button">
                                Add New Bank <i class="ri-add-line"></i>
                            </button>
                        </div>

                        <div class="table-responsive" style="overflow: visible">
                            <table class="table whitespace-nowrap min-w-full">
                                <thead>
                                    <tr class="border-b border-defaultborder">
                                        <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                                        <th class="text-start px-4 py-2" scope="col">Bank Name</th>
                                        <th class="text-start px-4 py-2" scope="col">Account Name</th>
                                        <th class="text-start px-4 py-2" scope="col">Account Number</th>
                                        <th class="text-start px-4 py-2" scope="col">Is Default</th>
                                        <th class="text-start px-4 py-2" scope="col">Updated At</th>
                                        <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($bankDetails->isNotEmpty())
                                        @foreach ($bankDetails as $key => $bank)
                                            <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                                <th class="px-4 py-2" scope="row">{{ $key + 1 }}
                                                </th>
                                                <td class="px-4 py-2 text-xs">{{ $bank->bank_name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-xs">{{ $bank->account_name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-xs">{{ $bank->account_number ?? '-' }}</td>
                                                <td class="px-4 py-2 text-xs">
                                                    {{ $bank->is_default == 1 ? 'Yes' : 'No' ?? '-' }}
                                                </td>
                                                <td class="px-4 py-2 text-xs">
                                                    {{ $bank->updated_at ? $bank->updated_at->format('d M, Y h:i A') : '-' }}
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

                                                            <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400 open-edit-bank-modal"
                                                                data-bank='@json($bank)'
                                                                data-employee-name="{{ $bank->employee->first_name ?? 'Unknown' }}"
                                                                href="javascript:void(0);">
                                                                Edit
                                                            </a>

                                                            <form
                                                                action="{{ route('employees.bank.destroy', [$employee->id, $bank->id]) }}"
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
                                            <td colspan="8"
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
    @include('admin.employee.bank-details.create')
    @include('admin.employee.bank-details.edit')
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.openBankDetailsModal').click(function() {
                $('#bankDetailsModal').removeClass('hidden');
            });

            $('#closeBankDetailsModal').click(function() {
                $('#bankDetailsModal').addClass('hidden');
            });

            $('.open-edit-bank-modal').click(function() {
                window.dispatchEvent(new Event('close-dropdown'));

                const bank = $(this).data('bank');
                const employeeId = {{ $employee->id }};

                $('#edit_bank_name').val(bank.bank_name);
                $('#edit_account_name').val(bank.account_name);
                $('#edit_account_number').val(bank.account_number);
                $('#edit_account_type').val(bank.account_type ?? '');
                $('#edit_is_default').prop('checked', bank.is_default == 1);

                const updateUrl = `/employees/${employeeId}/bank-details/${bank.id}`;
                $('#editBankForm').attr('action', updateUrl);

                $('#editBankDetailsModal').removeClass('hidden');

            });

            $('#closeEditBankModal').click(function() {
                $('#editBankDetailsModal').addClass('hidden');
            });
        });
    </script>
@endsection
