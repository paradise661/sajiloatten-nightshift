@extends('layouts.admin.master')

@section('content')
    @include('admin.includes.message')

    <div class="xl:col-span-12 col-span-12 mt-6">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Current Salary Details
                </div>
            </div>

            <div class="box-body p-4">

                <div class="col-span-12">
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full table-auto border border-gray-200 text-sm text-left bg-white rounded-lg shadow-sm">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
                                <tr>
                                    <th class="text-start px-4 py-2 w-3">#</th>
                                    <th class="px-4 py-3 border text-left">Employee</th>
                                    <th class="px-4 py-3 border text-left">Basic Salary</th>
                                    <th class="px-4 py-3 border text-left">Allowance</th>
                                    <th class="px-4 py-3 border text-left">Overtime Rate</th>
                                    <th class="px-4 py-3 border text-left">Taxable</th>
                                    <th class="px-4 py-3 border text-left">Deduction</th>
                                    <th class="px-4 py-3 border text-left">Effective Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($salaries as $key => $employee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $key + 1 }}</td>
                                        <td class="px-4 py-4 border text-gray-800 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                    <a class="fancybox" data-fancybox="demo"
                                                        href="{{ $employee['user']->image ?? '' }}">
                                                        <img class="rounded-full" src="{{ $employee['user']->image ?? '' }}"
                                                            alt="profile">
                                                    </a>
                                                </span>
                                                <div>
                                                    {{ $employee['user']->first_name }}
                                                    {{ $employee['user']->last_name }}<br>
                                                    <i class="text-xs text-gray-600">
                                                        {{ $employee['user']->designation ?? '' }}
                                                    </i>, 
                                                    <i class="text-xs text-gray-600">
                                                        {{ $employee['user']->branch->name ?? 'Branch not assigned' }}
                                                    </i>
                                                    
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">{{ $employee['salary']?->base_salary ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $employee['salary']?->allowance ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $employee['salary']?->overtime_rate ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            {{ $employee['salary']?->is_taxable ? ($employee['salary']?->is_taxable ? 'YES' : 'NO') : '-' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $employee['salary']?->is_deduction_enabled ? ($employee['salary']?->is_deduction_enabled ? 'YES' : 'NO') : '-' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $employee['salary']?->effective_date ? (session('calendar') == 'BS' ? App\Services\DateService::ADToBS($employee['salary']?->effective_date) : $employee['salary']?->effective_date) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
