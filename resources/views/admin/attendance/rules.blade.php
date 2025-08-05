@extends('layouts.admin.master')

@section('content')
    @include('admin.includes.message')

    <div class="xl:col-span-12 col-span-12 mt-6">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Attendance Rules (Office Hours) for All Employees
                </div>
                @can('reset employeerules')
                    <button class="ti-btn ti-btn-danger text-sm" type="button" onclick="resetAllTimes()">
                        <i class="ri-refresh-line"></i> Reset All
                    </button>
                @endcan
            </div>

            <div class="px-4 mt-2">
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-md p-3">
                    <strong>Note:</strong> These timings are for attendance tracking only. Any late arrival or early
                    out must be justified with reason via the mobile app. These rules are not linked to overtime
                    calculation.
                </div>
            </div>

            <div class="box-body p-4">
                <form class="grid grid-cols-12 gap-6 mt-0" action="{{ route('attendance.updaterules') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="col-span-12">
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full table-auto border border-gray-200 text-sm text-left bg-white rounded-lg shadow-sm">
                                <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
                                    <tr>
                                        <th class="text-start px-4 py-2 w-3">#</th>
                                        <th class="px-4 py-3 border text-left">Employee</th>
                                        <th class="px-4 py-3 border text-left">Check-In Time</th>
                                        <th class="px-4 py-3 border text-left">Check-Out Time</th>
                                        <th class="px-4 py-3 border text-left">Reset</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($employees as $key => $employee)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $key + 1 }}</td>
                                            <td class="px-4 py-4 border text-gray-800 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                        <a class="fancybox" data-fancybox="demo"
                                                            href="{{ $employee->image ?? '' }}">
                                                            <img class="rounded-full" src="{{ $employee->image ?? '' }}"
                                                                alt="profile">
                                                        </a>
                                                    </span>
                                                    <div>
                                                        {{ $employee->first_name }} {{ $employee->last_name }}<br>
                                                        <i class="text-xs text-gray-600">
                                                            {{ $employee->branch->name ?? 'Branch not assigned' }}
                                                        </i>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-2 align-top">
                                                <div class="flex flex-col">
                                                    <div class="input-group relative">
                                                        <div class="input-group-text text-[#8c9097] dark:text-white/50">
                                                            <i class="ri-time-line"></i>
                                                        </div>
                                                        <input
                                                            class="form-control checkin_timepicker rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 @error('rules.' . $employee->id . '.check_in_time') border-red-500 @enderror"
                                                            id="checkin-{{ $employee->id }}" type="text"
                                                            name="rules[{{ $employee->id }}][check_in_time]"
                                                            value="{{ old("rules.{$employee->id}.check_in_time", $employee->attendanceRule->check_in_time ?? '') }}"
                                                            placeholder="Select Check-In time" autocomplete="off" required>
                                                    </div>
                                                    @error('rules.' . $employee->id . '.check_in_time')
                                                        <i class="text-red-500 text-xs mt-1">*{{ $message }}</i>
                                                    @enderror
                                                </div>
                                            </td>

                                            <td class="py-4 px-2 align-top">
                                                <div class="flex flex-col">
                                                    <div class="input-group relative">
                                                        <div class="input-group-text text-[#8c9097] dark:text-white/50">
                                                            <i class="ri-time-line"></i>
                                                        </div>
                                                        <input
                                                            class="form-control checkout_timepicker rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 @error('rules.' . $employee->id . '.check_out_time') border-red-500 @enderror"
                                                            id="checkout-{{ $employee->id }}" type="text"
                                                            name="rules[{{ $employee->id }}][check_out_time]"
                                                            value="{{ old("rules.{$employee->id}.check_out_time", $employee->attendanceRule->check_out_time ?? '') }}"
                                                            placeholder="Select Check-Out time" autocomplete="off" required>
                                                    </div>
                                                    @error('rules.' . $employee->id . '.check_out_time')
                                                        <i class="text-red-500 text-xs mt-1">*{{ $message }}</i>
                                                    @enderror
                                                </div>
                                            </td>

                                            @can('reset employeerules')
                                                <td class="py-4 px-2 align-top">
                                                    <button class="ti-btn ti-btn-danger text-xs" type="button"
                                                        onclick="resetEmployeeTimes('{{ $employee->id }}')">
                                                        <i class="ri-refresh-line"></i> Reset
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @can('update employeerules')
                        <div class="col-span-12 mt-4">
                            <button class="ti-btn ti-btn-primary-full" type="submit">
                                <i class="ri-save-line text-base"></i> Update
                            </button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>

    <script>
        function resetEmployeeTimes(employeeId) {
            document.getElementById(`checkin-${employeeId}`).value = '';
            document.getElementById(`checkout-${employeeId}`).value = '';
        }

        function resetAllTimes() {
            const checkinInputs = document.querySelectorAll('input[id^="checkin-"]');
            const checkoutInputs = document.querySelectorAll('input[id^="checkout-"]');

            checkinInputs.forEach(input => input.value = '');
            checkoutInputs.forEach(input => input.value = '');
        }
    </script>
@endsection
