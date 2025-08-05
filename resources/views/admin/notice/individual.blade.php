@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Send Push Notification

                </div>
                <div class="prism-toggle">
                    {{-- <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('notices.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a> --}}
                </div>
            </div>
            <div class="px-4 mt-2">
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-md p-3">
                    <strong>Note:</strong> This data will not be saved.
                </div>
            </div>
            <div class="box-body">
                <form class="" action="{{ route('send.push.notification.devices') }}" method="POST">
                    @csrf
                    @include('admin.includes.message')
                    <div class="grid grid-cols-12 gap-8">
                        <!-- Left Side -->
                        <div class="col-span-12 md:col-span-6 space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                        class="text-red-500">*</span></label>
                                <input
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    type="text" required name="title" maxlength="50" />
                                <p class="mt-1 text-sm text-gray-500"><i>(Note: Max 50 Characters)</i></p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    type="text" required name="description" maxlength="500" />
                                <p class="mt-1 text-sm text-gray-500"><i>(Note: Max 500 Characters)</i></p>
                            </div>
                        </div>

                        <!-- Right Side (You can add more here) -->
                        <div class="col-span-12 md:col-span-6">
                            <div class="overflow-y-auto border rounded-md p-4" style="height:400px">
                                <div class="" style="height:500px">
                                    @foreach ($groupedEmployees as $branch => $departments)
                                        <div class="">
                                            <input class="branch-checkbox" id="" type="checkbox" name="branches">
                                            <span class="font-bold">{{ $branch }}</span>
                                        </div>
                                        <div class="px-4 py-1 departments-wrapper">
                                            @foreach ($departments as $department => $employees)
                                                <div class="">
                                                    <input class="department-checkbox" id="" type="checkbox"
                                                        name="departments">
                                                    <span>{{ $department }}</span>
                                                </div>
                                                <div class="px-4 py-1 employees-wrapper">
                                                    @foreach ($employees as $employee)
                                                        <div class="">
                                                            <input class="employee-checkbox" id=""
                                                                value="{{ $employee->expo_token }}" type="checkbox"
                                                                name="expo_tokens[]">
                                                            <span>{{ $employee->first_name }}
                                                                {{ $employee->last_name }}</span>
                                                            @if ($employee->expo_token)
                                                                <svg class="h-3 w-3 text-green-500 inline"
                                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                    fill="green">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12">
                        <button class="ti-btn ti-btn-primary-full submitbtn" type="submit">Send <span
                                class="ti-spinner text-white !w-[1rem] !h-[1rem]" style="display: none" role="status"
                                aria-label="loading"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function() {
                let button = $(this).find('.submitbtn');
                let spinner = button.find('.ti-spinner');

                button.prop('disabled', true);
                spinner.show();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Branch → Department + Employee
            $(document).on('change', '.branch-checkbox', function() {
                let isChecked = $(this).is(':checked');
                let departmentsWrapper = $(this).closest('div').next('.departments-wrapper');
                departmentsWrapper.find('.department-checkbox, .employee-checkbox').prop('checked',
                    isChecked);
            });

            // Department → Employee
            $(document).on('change', '.department-checkbox', function() {
                let isChecked = $(this).is(':checked');
                let employeesWrapper = $(this).closest('div').next('.employees-wrapper');
                employeesWrapper.find('.employee-checkbox').prop('checked', isChecked);
            });

            // Auto check parent department if all employees are checked
            $(document).on('change', '.employee-checkbox', function() {
                let employeesWrapper = $(this).closest('.employees-wrapper');
                let departmentCheckbox = employeesWrapper.prev('.department-checkbox');

                let allChecked = employeesWrapper.find('.employee-checkbox').length === employeesWrapper
                    .find('.employee-checkbox:checked').length;
                departmentCheckbox.prop('checked', allChecked);

                // Also check branch
                let departmentsWrapper = employeesWrapper.closest('.departments-wrapper');
                let branchCheckbox = departmentsWrapper.prev('.branch-checkbox');

                let allDepartmentsChecked = departmentsWrapper.find('.department-checkbox').length ===
                    departmentsWrapper.find('.department-checkbox:checked').length;
                branchCheckbox.prop('checked', allDepartmentsChecked);
            });
        });
    </script>
@endsection
