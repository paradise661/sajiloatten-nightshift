@extends('layouts.admin.master')

@section('content')
    @include('admin.includes.message')
    <div class="xl:col-span-12 col-span-12 mt-6">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Payroll Management
                </div>
            </div>
            <div class="flex items-center mb-2">
                <form action="" method="GET">
                    <div class="flex w-full max-w-xl gap-3 p-4">
                        <div class="">
                            <label class="mb-1" for="">Employee</label>
                            <select class="select2 form-control !px-3 !py-2 !text-sm !rounded-md" id="employee-select"
                                aria-label="Employee" name="employee">
                                @foreach ($employees as $employee)
                                    <option data-image="{{ $employee->image ?? asset('default-avatar.png') }}"
                                        value="{{ $employee->id }}"
                                        {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="">
                            <label class="mb-1" for="">Year</label>
                            <select class="select2 form-control !px-3 !py-2 !text-sm !rounded-md" aria-label="Year"
                                name="year">
                                @php
                                    $startYear = 2000;
                                    $endYear = now()->year;
                                    if (session('calendar') == 'BS') {
                                        $startYear = 2000 + 57;
                                        $endYear = now()->year + 57;
                                    }
                                @endphp
                                @for ($year = $endYear; $year >= $startYear; $year--)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="">
                            <label class="mb-1" for="">Month</label>
                            <select class="select2  form-control !px-3 !py-2 !text-sm !rounded-md" aria-label="Month"
                                name="month">
                                @if (session('calendar') != 'BS')
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option
                                            {{ request('month') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}
                                            value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                            {{ \Carbon\Carbon::createFromDate(null, $i, 1)->format('F') }}
                                        </option>
                                    @endfor
                                @else
                                    @foreach (\App\Models\MonthlyPayroll::nepaliMonths as $key => $month)
                                        <option {{ request('month') == $key ? 'selected' : '' }}
                                            value="{{ $key }}">
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button class="flex gap-1 items-center bg-primary text-white px-4 py-2 rounded-sm submitbtn"
                                type="submit">
                                Generate
                                <span class="ti-spinner text-white !w-[1rem] !h-[1rem]" style="display: none" role="status"
                                    aria-label="loading"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        @if (request('employee') && request('year') && request('month'))
            @if ($summary)
                <div class="flex justify-between gap-6 mb-4">
                    <div class="w-[60%] mx-auto bg-white border border-gray-300 rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex justify-between">
                                <h2 class="text-lg font-semibold text-gray-800 mb-1">Employee Salary Statement</h2>
                            </div>
                            <p class="text-sm text-gray-600">
                                <strong>Employee:</strong> {{ $selectedEmployee->first_name }}
                                {{ $selectedEmployee->last_name }}<br>
                                <strong>Month:</strong>
                                @if (session('calendar') != 'BS')
                                    {{ date('M Y', strtotime($selectedyear . '-01')) }}
                                @else
                                    {{ \App\Models\MonthlyPayroll::nepaliMonths[request('month')] }} {{ request('year') }}
                                @endif
                            </p>
                        </div>

                        <div class="px-6 py-4">
                            <h3 class="text-base font-semibold text-gray-700 mb-3">Attendance Summary</h3>
                            <table class="w-full text-sm border border-gray-400">
                                <tbody>
                                    <tr class="bg-gray-100">
                                        <td class="border px-3 py-2">Total Present</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['present_days'] }}</td>
                                        <td class="border px-3 py-2">Total Absent</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['absent_days'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border px-3 py-2">Total Paid Leave</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['paid_leaves'] }}</td>
                                        <td class="border px-3 py-2">Total Unpaid Leave</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['unpaid_leaves'] }}</td>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <td class="border px-3 py-2">Total Public Holidays</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['public_holidays'] }}</td>
                                        <td class="border px-3 py-2">Total Weekends</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['weekends'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border px-3 py-2">Total Working Days</td>
                                        <td class="border px-3 py-2 text-right">
                                            {{ $summary['total_expected_working_days'] }}
                                        </td>
                                        <td class="border px-3 py-2">Total Days</td>
                                        <td class="border px-3 py-2 text-right">{{ $summary['total_days_in_month'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @php
                            $timeDetails = json_decode($summary->workingtime_details);
                        @endphp

                        <div class="flex justify-end">
                            <button class="flex justify-center items-center px-4 py-2 text-gray-700 rounded"
                                onclick="openModal()">
                                View Salary Calculations
                                <svg class="w-4 h-4 ml-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a7 7 0 100 14A7 7 0 009 2zM8 7h2v5H8V7zm0 6h2v2H8v-2z" />
                                </svg>
                            </button>
                        </div>
                        <div class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
                            id="attendanceModal">
                            <div
                                class="bg-white rounded-xl shadow-2xl w-full max-w-[100vw] sm:max-w-lg md:max-w-2xl lg:max-w-2xl max-h-[100vh] flex flex-col relative mx-auto overflow-hidden">

                                <!-- Header -->
                                <div
                                    class="border-b border-gray-200 px-6 py-4 flex justify-between items-center bg-white z-20">
                                    <h2 class="text-xl font-semibold text-gray-900">Calculation Details</h2>
                                    <button class="text-gray-500 hover:text-gray-800 text-2xl font-bold focus:outline-none"
                                        onclick="closeModal()" title="Close modal">
                                        &times;
                                    </button>
                                </div>

                                <!-- Scrollable Main Body -->
                                <div class="overflow-y-auto px-6 py-4 flex-1 space-y-6"
                                    style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; max-height: calc(90vh - 120px);">

                                    <!-- Table Section (with internal scroll) -->
                                    <div>
                                        <div class="overflow-y-auto border border-gray-200 rounded-md"
                                            style="max-height: 300px;">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50 text-gray-700">
                                                    <tr>
                                                        <th class="text-left px-4 py-2 text-xs" style="width:30%">Date</th>
                                                        <th class="text-left px-4 py-2 text-xs" style="width:20%">Working
                                                            Hour</th>
                                                        <th class="text-left px-4 py-2 text-xs">Worked Hour</th>
                                                        <th class="text-left px-4 py-2 text-xs">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 text-gray-800">
                                                    @foreach ($timeDetails->extra_details->records ?? [] as $key => $timeDetail)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-4 py-2 text-xs">
                                                                @if (session('calendar') == 'BS')
                                                                    {{ App\Services\DateService::ADToBS($timeDetail->date ?? '') }}
                                                                @else
                                                                    {{ $timeDetail->date ?? '-' }}
                                                                @endif
                                                                <div class="text-xs text-gray-500">{{ $timeDetail->day }}
                                                                </div>
                                                            </td>
                                                            <td class="px-4 py-2 text-xs">
                                                                {{ $timeDetail->working_hour ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-xs">
                                                                {{ $timeDetail->worked_hour ?? '-' }}</td>
                                                            <td
                                                                class="px-4 py-2 text-xs {{ $timeDetail->status !== 'Present' ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                                                {{ $timeDetail->status ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Time Calculations -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Time Calculations</h3>
                                        <div class="space-y-1 text-sm text-gray-800">
                                            <div class="flex justify-between">
                                                <span>Total Working Hours ({{ $summary['total_expected_working_days'] }}
                                                    days):</span>
                                                <span>{{ $timeDetails->expected_hours }}h</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Total Worked Hours ({{ $summary['present_days'] }} days):</span>
                                                <span>{{ formatMinutesToHours($timeDetails->total_worked_minutes) }}</span>
                                            </div>
                                            @if ($summary['paid_leaves'] > 0)
                                                <div class="flex justify-between">
                                                    <span>Paid Leave Hours ({{ $summary['paid_leaves'] }} days):</span>
                                                    <span>{{ formatMinutesToHours($timeDetails->paid_leave_minutes) }}</span>
                                                </div>
                                            @endif
                                            @if ($timeDetails->overtime_after_expected > 0)
                                                <div class="flex justify-between text-green-600 font-medium">
                                                    <span>Extra Hours Worked:</span>
                                                    <span>{{ formatMinutesToHours($timeDetails->overtime_after_expected) }}</span>
                                                </div>
                                            @elseif ($timeDetails->short_after_expected > 0)
                                                <div class="flex justify-between text-red-600 font-medium">
                                                    <span>Hours Short of Target:</span>
                                                    <span>{{ formatMinutesToHours($timeDetails->short_after_expected) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Salary Calculations -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Salary Calculations</h3>
                                        <div class="space-y-1 text-sm text-gray-800">
                                            <div class="flex justify-between">
                                                <span>{{ $timeDetails->expected_hours }}h</span>
                                                <span>Rs. {{ number_format($summary['base_salary'], 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>1h</span>
                                                <span>Rs.
                                                    {{ number_format($summary['base_salary'] / $timeDetails->expected_hours, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>1min</span>
                                                <span>Rs.
                                                    {{ number_format($summary['base_salary'] / $timeDetails->expected_hours / 60, 2) }}</span>
                                            </div>
                                            @if ($summary['undertime_amount'] > 0)
                                                <div class="flex justify-between text-red-600 font-medium">
                                                    <span>Attendance Deduction
                                                        ({{ formatMinutesToHours($timeDetails->short_after_expected) }}):</span>
                                                    <span>Rs. {{ number_format($summary['undertime_amount'], 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($summary['overtime_amount'] > 0)
                                                <div class="flex justify-between text-green-600 font-medium">
                                                    <span>Attendance Deduction
                                                        ({{ formatMinutesToHours($timeDetails->overtime_after_expected) }}):</span>
                                                    <span>Rs. {{ number_format($summary['overtime_amount'], 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Salary Settings -->
                                    <div>
                                        @php
                                            $salarySettings = json_decode($summary->salary_settings);
                                        @endphp
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Salary Settings</h3>
                                        <div class="space-y-1 text-sm text-gray-800">
                                            <div class="flex justify-between">
                                                <span>Basic Salary:</span>
                                                <span>Rs. {{ number_format($salarySettings->base_salary, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Overtime Rate:</span>
                                                <span>Rs.
                                                    {{ number_format($salarySettings->overtime_rate, 2) }}/hour</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Tax:</span>
                                                <span>{{ $salarySettings->is_taxable ? 'Enabled' : 'None' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Attendance Deduction:</span>
                                                <span>{{ $salarySettings->is_deduction_enabled ? 'Enabled' : 'None' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Effective From:</span>
                                                <span>
                                                    @if (session('calendar') == 'BS')
                                                        {{ App\Services\DateService::ADToBS($salarySettings->effective_date ?? '') }}
                                                    @else
                                                        {{ $salarySettings->effective_date ?? '-' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sticky Footer (Optional if needed separately) -->
                                <!-- You can add buttons or actions here -->
                            </div>

                        </div>

                        <div class="px-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-base font-semibold text-gray-700 border-b pb-1 mb-2">Earnings</h3>
                                <div class="space-y-1">
                                    <div class="flex justify-between"><span>Salary</span><span>Rs.
                                            {{ number_format($summary['base_salary'], 2) }}</span></div>
                                    <div class="flex justify-between"><span>Allowances</span><span>Rs.
                                            {{ number_format($summary['allowances'], 2) }}</span></div>
                                    @if ($summary['overtime_amount'] > 0)
                                        <div class="flex justify-between"><span>Overtime</span><span>Rs.
                                                {{ number_format($summary['overtime_amount'], 2) }}</span></div>
                                    @endif

                                    @foreach ($compensations as $earning)
                                        @if ($earning->type === 'earning')
                                            <div class="flex justify-between">
                                                <span>{{ $earning->title }}
                                                    @if (json_decode($summary->salary_settings)->is_taxable)
                                                        {{ $earning->is_taxable ? '(Taxable)' : '' }}
                                                    @endif
                                                </span>
                                                <span>Rs. {{ number_format($earning->amount, 2) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="flex justify-between border-t font-semibold pt-2">
                                        <span>Total Earnings</span>
                                        <span>Rs. {{ number_format($summary['total_earnings'], 2) }}</span>
                                    </div>
                                    @if ($summary['taxable_salary'] > 0)
                                        <div class="flex justify-between text-gray-600">
                                            <span>Taxable Income</span>
                                            <span>Rs. {{ number_format($summary['taxable_salary'], 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-gray-700 border-b pb-1 mb-2">Deductions</h3>
                                <div class="space-y-1">
                                    @if ($summary['undertime_amount'] > 0)
                                        <div class="flex justify-between"><span>Undertime (Attendance)</span><span>Rs.
                                                {{ number_format($summary['undertime_amount'], 2) }}</span></div>
                                    @endif
                                    @foreach ($compensations as $deduction)
                                        @if ($deduction->type === 'deduction')
                                            <div class="flex justify-between">
                                                <span>{{ $deduction->title }}</span>
                                                <span>Rs. {{ number_format($deduction->amount, 2) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="flex justify-between border-t font-semibold pt-2">
                                        <span>Total Deductions</span>
                                        <span>Rs. {{ number_format($summary['total_deductions'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200">
                            <h3 class="text-base font-semibold text-gray-700 mb-2">Final Salary Details</h3>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Gross Salary</span>
                                    <span>Rs. {{ number_format($summary['gross_salary'], 2) }}</span>
                                </div>
                                @if ($summary['tax_amount'] > 0)
                                    <div class="flex justify-between">
                                        <span>Tax</span>
                                        <span class="text-red-600">Rs.
                                            {{ number_format($summary['tax_amount'], 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between font-semibold text-base">
                                    <span class="text-primary">Net Salary</span>
                                    <span class="text-primary">Rs. {{ number_format($summary['net_salary'], 2) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-base">
                                    <span class="text-primary"></span>
                                    <span
                                        class="{{ $summary->status == 'paid' ? 'bg-primary' : 'bg-red-400' }}  px-4 py-1 rounded-lg text-xs text-white self-end">{{ ucfirst($summary->status) }}</span>
                                </div>
                            </div>
                            {{-- <p class="text-xs text-gray-500 text-center mt-4">* This is a system-generated salary receipt.
                            </p> --}}
                        </div>

                        <div class="bg-gray-500">
                            <p class="text-xs text-white text-center">
                                Note: This is a system-generated salary receipt.
                            </p>
                        </div>

                    </div>

                    <div class="w-[40%]">
                        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-2">Salary Payment Details</h2>
                            @if ($summary['remaining_salary'] > 0)
                                <div class="space-y-4 text-sm text-gray-700">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Paid</span>
                                        <span class="text-green-600 font-semibold">Rs.
                                            {{ number_format($summary['paid_amount'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Remaining Salary</span>
                                        <span class="text-red-600 font-semibold">Rs.
                                            {{ number_format($summary['remaining_salary'], 2) }}</span>
                                    </div>
                                </div>

                                <form class="mt-6 space-y-5" action="{{ route('payroll.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $summary->user_id }}">
                                    <input type="hidden" name="monthly_payroll_id" value="{{ $summary->id }}">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" for="amount">Pay
                                            Salary</label>
                                        <input
                                            class="mt-2 w-full rounded-md border border-gray-300 px-4 py-2 text-sm
                                                   placeholder-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600
                                                   focus:outline-none transition"
                                            id="amount" type="number" min="1"
                                            max="{{ (int) $summary['remaining_salary'] }}" name="amount"
                                            value="{{ (int) $summary['remaining_salary'] }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700"
                                            for="payment_method">Payment Method</label>
                                        <select
                                            class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                                                   focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none transition"
                                            id="payment_method" name="payment_method" required>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700"
                                            for="remarks">Remarks</label>
                                        <input
                                            class="mt-2 w-full rounded-md border border-gray-300 px-4 py-2 text-sm
                                                   placeholder-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600
                                                   focus:outline-none transition"
                                            id="remarks" type="text" name="remarks" placeholder="Optional remarks">
                                    </div>

                                    <button
                                        class="w-1/4 mt-6 bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-md
                                               transition duration-200"
                                        type="submit">
                                        Pay
                                    </button>
                                </form>
                            @else
                                @if ((int) $summary['remaining_salary'] == 0)
                                    <div class="bg-gray-100 text-center py-4 rounded-md border border-gray-300 mt-4">
                                        <p class="text-green-700 font-semibold">Salary fully paid for this month</p>
                                    </div>
                                @endif
                            @endif

                            {{-- Payment History --}}
                            <div class="mt-8 bg-white border border-gray-300 rounded-lg shadow-sm" x-data="{ open: false }"
                                x-cloak>
                                <button
                                    class="w-full flex justify-between items-center px-6 py-3 font-semibold text-gray-800 focus:outline-none transition"
                                    @click="open = !open" aria-expanded="false" :aria-expanded="open.toString()">
                                    <span>Payment History</span>
                                    <svg class="w-4 h-4 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                        stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div class="px-2 py-2 max-h-64 overflow-y-auto space-y-4 text-sm text-gray-700"
                                    x-show="open" x-transition>
                                    @if ($statements->count())
                                        @foreach ($statements as $statement)
                                            <div class="border border-gray-200 rounded-md p-4 shadow-sm">
                                                <div class="flex justify-between mb-1">
                                                    <span class="font-medium text-gray-700">Amount:</span>
                                                    <span class="text-green-600 font-semibold">Rs.
                                                        {{ number_format($statement->amount, 2) }}</span>
                                                </div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-gray-600">Date:</span>
                                                    <span>{{ $statement->payment_date }}</span>
                                                </div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-gray-600">Remarks:</span>
                                                    <span>{{ $statement->remarks ?: '-' }}</span>
                                                </div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-gray-600">Payment Type:</span>
                                                    <span>{{ $statement->payment_method }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Paid By:</span>
                                                    <span>{{ $statement->staff->first_name ?? '' }}
                                                        {{ $statement->staff->last_name ?? '' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-center italic text-gray-500">No payment records found.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @if ($message)
                    <div class="w-full mx-auto bg-white p-4 border border-gray-300 shadow rounded-md">
                        <p>⚠️ {{ $message }}
                        </p>
                    </div>
                @else
                    @if (!$joined)
                        <div class="w-full mx-auto bg-white p-4 border border-gray-300 shadow rounded-md">
                            <p>⚠️ No payroll records found for <strong>{{ $selectedEmployee->first_name }}
                                    {{ $selectedEmployee->last_name }} </strong> in the selected month/year. The employee
                                joined on
                                <strong>{{ $selectedEmployee->join_date }}</strong>.
                            </p>
                        </div>
                    @else
                        <div class="w-full mx-auto bg-white p-4 border border-gray-300 shadow rounded-md">
                            <p>Salary not assigned for <strong>{{ $selectedEmployee->first_name }}
                                    {{ $selectedEmployee->last_name }}</strong>. Please assign a
                                salary to this
                                employee.</p>
                        </div>
                    @endif
                @endif
            @endif
        @endif
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

            function formatEmployee(employee) {
                if (!employee.id) {
                    return employee.text;
                }
                var imgUrl = $(employee.element).data('image');
                if (!imgUrl) imgUrl = '{{ asset('default-avatar.png') }}'; // fallback

                var $employee = $(
                    '<span><img src="' + imgUrl +
                    '" class="rounded-full mr-2" style="width: 20px; height: 20px; object-fit: cover;" />' +
                    employee.text + '</span>'
                );
                return $employee;
            };

            $('#employee-select').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        });

        function openModal() {
            $('#attendanceModal').removeClass('hidden');
        }

        function closeModal() {
            $('#attendanceModal').addClass('hidden');
        }
    </script>
@endsection
