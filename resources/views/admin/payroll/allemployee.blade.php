@extends('layouts.admin.master')

@section('content')
    @include('admin.includes.message')
    <div class="xl:col-span-12 col-span-12 mt-6">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Monthly Payroll Summary
                </div>
            </div>
            <div class="flex items-center mb-2">
                <form action="" method="GET">
                    <div class="flex w-full max-w-xl gap-3 p-4">
                        <div class="">
                            <label class="mb-1" for="">Branch</label>
                            <select class="select2 form-control !px-3 !py-2 !text-sm !rounded-md" id=""
                                aria-label="Branch" name="branch">
                                <option value="all">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
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
                            <button class="bg-primary flex gap-1 items-center text-white px-4 py-2 rounded-sm submitbtn"
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
        @if (request('year') && request('month'))
            @if (count($employeesSalary))
                <div class="w-full mb-4" id="exportContent">
                    <div class=" bg-white rounded-md overflow-hidden">
                        <div class="flex justify-between items-center px-4 py-2">
                            <div class="">
                                <div class="flex justify-between">
                                    <h2 class="text-sm font-semibold text-gray-800 mb-1">Employee Monthly Salary Statement
                                    </h2>
                                </div>
                                <p class="text-sm text-gray-800">
                                    <strong>Month:</strong>
                                    @if (session('calendar') != 'BS')
                                        {{ date('M Y', strtotime(request('year') . '-' . request('month') . '-01')) }}
                                    @else
                                        {{ \App\Models\MonthlyPayroll::nepaliMonths[request('month')] }}
                                        {{ request('year') }}
                                    @endif
                                </p>
                            </div>
                            @can('export excel')
                                <div class="items-center justify-center">
                                    <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded-sm mb-2"
                                        id="exportExcel">
                                        Export to Excel
                                    </button>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="max-h-[400px] overflow-y-scroll overflow-x-auto">
                        <table class="min-w-full table-auto border border-gray-300 text-sm bg-white" id="salaryTable">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">#</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Employee</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Basic Salary</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Allowances</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Add. Earnings</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Total Deductions</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Gross Salary</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Taxable Salary</th>
                                    {{-- <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Attendance Deductions
                                    </th> --}}
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Tax</th>
                                    <th class="sticky top-0 z-10 bg-gray-100 px-4 py-2 text-xs">Net Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalNetSalary = 0;
                                @endphp
                                @foreach ($employeesSalary as $key => $employee)
                                    @php
                                        if (is_numeric($employee['net_salary'])) {
                                            $totalNetSalary += $employee['net_salary'];
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="text-center text-xs px-4 py-2">{{ $key + 1 }}</td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['employee_name'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['basic_salary'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['allowance'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['earnings'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['deductions'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['gross_salary'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['taxable_salary'] }}
                                        </td>
                                        {{-- <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['attenance_deductions'] }}
                                        </td> --}}
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['tax'] }}
                                        </td>
                                        <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            {{ $employee['net_salary'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="hover:bg-gray-50" style="font-weight: bold;">
                                    <td class="text-center text-xs px-4 py-2"></td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                        <p class="font-bold">Total</p>
                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    {{-- <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td> --}}
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">

                                    </td>
                                    <td class="text-center text-xs px-4 py-4 border text-gray-800 whitespace-nowrap">
                                        <p class="font-bold">{{ $totalNetSalary }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @if ($message)
                    <div class="w-full mx-auto bg-white p-4 border border-gray-300 shadow rounded-md">
                        <p>⚠️ {{ $message }}
                        </p>
                    </div>
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

            $('#exportExcel').click(function() {
                var content = $('#exportContent').clone();

                // Ensure consistent text alignment in Excel
                content.find('td').each(function() {
                    $(this).css({
                        'text-align': 'left',
                        'mso-number-format': '\\@' // Force Excel to treat all cells as text
                    });
                });

                content.find('th').each(function() {
                    $(this).css({
                        'text-align': 'left',
                        'mso-number-format': '\\@'
                    });
                });

                var html = content.prop('outerHTML');
                var fileName = 'employee_salary_statement.xls';
                var excelData = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(html);

                var link = $('<a></a>')
                    .attr('href', excelData)
                    .attr('download', fileName)
                    .appendTo('body');

                link[0].click();
                link.remove();
            });

        });
    </script>
@endsection
