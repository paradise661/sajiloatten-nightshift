@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-12 col-span-12 mt-4">
        <div class="box rounded-lg shadow-lg border border-gray-200">
            <div class="box-header flex justify-between items-center p-4">
                <div class="box-title text-xl font-semibold text-gray-800">
                    Create New Shift
                </div>
                <a class="ti-btn ti-btn-primary text-sm" href="{{ route('shifts.index') }}">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>

            <div class="box-body p-4">
                <form class="grid grid-cols-12 gap-6 mt-0" action="{{ route('shifts.store') }}" method="POST">
                    @csrf

                    <div class="col-span-12">
                        <label class="form-label">Shift Name <span class="text-red-500">*</span></label>
                        <input class="form-control @error('name') border-red-500 @enderror" type="text" name="name"
                            placeholder="Enter Name" value="{{ old('name') }}" autocomplete="off">
                        @error('name')
                            <p class="text-sm text-red-600 mt-2"><i>*{{ $message }}</i></p>
                        @enderror
                    </div>

                    <div class="col-span-12">
                        <label class="inline-flex items-center gap-2">
                            <input id="is_cross_day" type="checkbox" name="is_cross_day"
                                {{ old('is_cross_day') ? 'checked' : '' }}>
                            <span class="text-sm font-medium">Is Cross Day?</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">
                            Check this box if the shift period extends into the next day. (e.g., night shift from 10 PM to 6
                            AM next day).
                        </p>
                    </div>

                    <div class="col-span-6 hidden" id="day_end_time_wrapper">
                        <label class="form-label">Day End Time <span class="text-red-500">*</span></label>
                        <div class="input-group relative">
                            <div class="input-group-text text-[#8c9097] dark:text-white/50">
                                <i class="ri-time-line"></i>
                            </div>
                            <input class="form-control timepicker @error('day_end_time') border-red-500 @enderror"
                                id="day_end_time" type="text" name="day_end_time"
                                value="{{ old('day_end_time', '23:59:59') }}" placeholder="Enter Day End Time"
                                autocomplete="off">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Specify the end of the attendance day. For example, 23:59:59 means the day ends before midnight.
                        </p>
                        @error('day_end_time')
                            <i class="text-red-500 text-xs mt-1">*{{ $message }}</i>
                        @enderror
                    </div>

                    <div class="col-span-12">
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full table-auto border border-gray-200 text-sm text-left bg-white rounded-lg shadow-sm">
                                <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 border text-left">Day</th>
                                        <th class="px-4 py-3 border text-left">Start Time</th>
                                        <th class="px-4 py-3 border text-left">End Time</th>
                                        <th class="px-4 py-3 border text-left">Is Holiday?</th>
                                        <th class="px-4 py-3 border text-left">Reset</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php
                                        $days = [
                                            'Sunday',
                                            'Monday',
                                            'Tuesday',
                                            'Wednesday',
                                            'Thursday',
                                            'Friday',
                                            'Saturday',
                                        ];
                                    @endphp

                                    @foreach ($days as $day)
                                        @php
                                            $dayKey = $day;
                                            $defaultStart = old($dayKey . '_start_time', '10:00');
                                            $defaultEnd = old($dayKey . '_end_time', '18:00');
                                            $isHolidayChecked = old($dayKey . '_holiday')
                                                ? true
                                                : $dayKey === 'Saturday';
                                        @endphp

                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">{{ $day }}</td>

                                            <td class="py-3 px-2">
                                                <div class="input-group relative">
                                                    <div class="input-group-text text-[#8c9097] dark:text-white/50">
                                                        <i class="ri-time-line"></i>
                                                    </div>
                                                    <input
                                                        class="form-control timepicker @error($dayKey . '_start_time') border-red-500 @enderror"
                                                        id="{{ $dayKey }}_start_time" type="text"
                                                        name="{{ $dayKey }}_start_time"
                                                        value="{{ $isHolidayChecked ? '' : $defaultStart }}"
                                                        placeholder="Start Time" autocomplete="off"
                                                        {{ $isHolidayChecked ? 'disabled' : '' }}>
                                                </div>
                                                @error($dayKey . '_start_time')
                                                    <i class="text-red-500 text-xs mt-1">*{{ $message }}</i>
                                                @enderror
                                            </td>

                                            <td class="py-3 px-2">
                                                <div class="input-group relative">
                                                    <div class="input-group-text text-[#8c9097] dark:text-white/50">
                                                        <i class="ri-time-line"></i>
                                                    </div>
                                                    <input
                                                        class="form-control timepicker @error($dayKey . '_end_time') border-red-500 @enderror"
                                                        id="{{ $dayKey }}_end_time" type="text"
                                                        name="{{ $dayKey }}_end_time"
                                                        value="{{ $isHolidayChecked ? '' : $defaultEnd }}"
                                                        placeholder="End Time" autocomplete="off"
                                                        {{ $isHolidayChecked ? 'disabled' : '' }}>
                                                </div>
                                                @error($dayKey . '_end_time')
                                                    <i class="text-red-500 text-xs mt-1">*{{ $message }}</i>
                                                @enderror
                                            </td>

                                            <td class="py-3 px-2">
                                                <label class="inline-flex items-center gap-2">
                                                    <input class="holiday-toggle h-4 w-4" id="{{ $dayKey }}_holiday"
                                                        data-day="{{ $dayKey }}" type="checkbox"
                                                        name="{{ $dayKey }}_holiday"
                                                        {{ $isHolidayChecked ? 'checked' : '' }}>
                                                    <span class="text-sm">Holiday</span>
                                                </label>
                                            </td>

                                            <td class="py-3 px-2">
                                                <button class="ti-btn ti-btn-danger text-xs reset-day-btn"
                                                    data-day="{{ $dayKey }}" type="button">
                                                    <i class="ri-refresh-line"></i> Reset
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="py-3 px-2 text-left">
                                            <button class="ti-btn ti-btn-danger text-xs" id="reset-all-btn" type="button">
                                                <i class="ri-refresh-line"></i> Reset All
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-span-12 mt-2 flex justify-between">
                        <button class="ti-btn ti-btn-primary-full" type="submit">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const flatpickrInstances = {};
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            $('.timepicker').each(function() {
                const id = $(this).attr('id');
                flatpickrInstances[id] = flatpickr(this, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: $(this).val() || null,
                });
            });

            $('.holiday-toggle').on('change', function() {
                const day = $(this).data('day');
                const isHoliday = $(this).is(':checked');

                if (isHoliday) {
                    flatpickrInstances[day + '_start_time'].clear();
                    flatpickrInstances[day + '_start_time'].input.setAttribute('disabled', true);
                    flatpickrInstances[day + '_end_time'].clear();
                    flatpickrInstances[day + '_end_time'].input.setAttribute('disabled', true);
                } else {
                    flatpickrInstances[day + '_start_time'].input.removeAttribute('disabled');
                    flatpickrInstances[day + '_end_time'].input.removeAttribute('disabled');
                }
            }).each(function() {
                $(this).trigger('change');
            });

            $('.reset-day-btn').on('click', function() {
                const day = $(this).data('day');
                $('#' + day + '_holiday').prop('checked', false).trigger('change');
                flatpickrInstances[day + '_start_time'].setDate('10:00', true, "H:i");
                flatpickrInstances[day + '_end_time'].setDate('18:00', true, "H:i");
            });

            $('#reset-all-btn').on('click', function() {
                days.forEach(function(day) {
                    $('#' + day + '_holiday').prop('checked', false).trigger('change');
                    flatpickrInstances[day + '_start_time'].setDate('10:00', true, "H:i");
                    flatpickrInstances[day + '_end_time'].setDate('18:00', true, "H:i");
                });
            });

            // Cross Day toggle
            $('#is_cross_day').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#day_end_time_wrapper').show();
                } else {
                    $('#day_end_time_wrapper').hide();
                    $('#day_end_time').val('23:59:59');
                }
            }).trigger('change');

            flatpickrInstances['day_end_time'] = flatpickr("#day_end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i:S",
                time_24hr: true,
                defaultDate: $('#day_end_time').val() || "23:59:59",
            });

        });
    </script>
@endsection
