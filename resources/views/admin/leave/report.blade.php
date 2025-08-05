@extends('layouts.admin.master')

@section('content')
    @include('admin.includes.message')

    <div class="xl:col-span-12 col-span-12 mt-6">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Leave Report for All Employees (FY: {{ $fiscalYear }})
                </div>
                <form class="flex items-center gap-2" action="{{ route('leave.report.employee') }}" method="GET">
                    <select class="form-select ml-2 block w-full sm:text-sm border-gray-300 rounded-md shadow-sm"
                        name="fiscal_year">
                        <option value="2080/2081" {{ $fiscalYear == '2080/2081' ? 'selected' : '' }}>2080/2081</option>
                        <option value="2081/2082" {{ $fiscalYear == '2081/2082' ? 'selected' : '' }}>2081/2082</option>
                        <option value="2082/2083" {{ $fiscalYear == '2082/2083' ? 'selected' : '' }}>2082/2083</option>
                    </select>
                    <button class="ti-btn ti-btn-primary text-sm" type="submit">
                        <i class="ri-search-line"></i> Filter
                    </button>
                </form>
            </div>

            <div class="box-body p-4">
                @foreach ($employees as $key => $employee)
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="avatar avatar-xs me-2 online avatar-rounded">
                                <a class="fancybox" data-fancybox="profile" href="{{ $employee['user_image'] ?? '' }}">
                                    <img class="rounded-full" src="{{ $employee['user_image'] ?? '' }}"
                                        alt="{{ $employee['user_name'] }}">
                                </a>
                            </span>
                            <div>
                                <strong>{{ $employee['user_name'] }}</strong><br>
                                <span class="text-xs text-gray-600">{{ $employee['branch_name'] ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full table-auto border border-gray-200 text-sm text-left bg-white rounded-lg shadow-sm">
                                <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 border text-left">Leave Type</th>
                                        <th class="px-4 py-3 border text-left">Entitled</th>
                                        <th class="px-4 py-3 border text-left">Taken</th>
                                        <th class="px-4 py-3 border text-left">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($employee['leave_types'] as $leave)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 border">{{ $leave['leave_type_name'] }}</td>
                                            <td class="px-4 py-2 border">{{ $leave['entitled_days'] }}</td>
                                            <td class="px-4 py-2 border text-red-600">{{ $leave['taken_days'] }}</td>
                                            <td class="px-4 py-2 border text-green-600">{{ $leave['remaining_days'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
