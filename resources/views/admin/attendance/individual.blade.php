@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')

    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="xl:col-span-12 col-span-12">
            @livewire('attendance.filter-individual-employee')
            @include('admin.attendance.modal')
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#attendanceModal');
            const $closeModalBtn = $('#closeModal');
            const $modalContent = $('#modalContent');

            $(document).on('click', '.individual-row', function() {
                const $row = $(this);
                const attendance = JSON.parse($(this).attr('attendance'));
                const attendance_rule = JSON.parse($(this).attr('attendancerule'));

                const date = $row.attr('date') ?? '-';
                const day = $row.attr('day') ?? '-';
                const checkin = $row.attr('checkin') ?? '-';
                const checkout = $row.attr('checkout') ?? '-';
                const breakStart = $row.attr('breakstart') ?? '-';
                const breakEnd = $row.attr('breakend') ?? '-';
                const workedHours = $row.attr('workedhours') ?? '-';
                const totalBreak = $row.attr('totalbreak') ?? '-';
                const status = $row.attr('status') ?? '-';
                const lateCheckInReason = $row.attr('late-checkin-reason') ?? '-';
                const earlyCheckOutReason = $row.attr('early-checkout-reason') ?? '-';
                const checkout_requested = $(this).attr('checkout_requested');
                const checkin_requested = $(this).attr('checkin_requested');
                const viewMapBaseUrl = "{{ route('view.map') }}";

                const html = `
                    <table class="w-full table-auto">
                        <tbody>
                            <tr>
                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Date:</td>
                                <td class="py-1">${date}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Day:</td>
                                <td class="py-1">${day}</td>
                            </tr>
                            ${status === 'Present' ?
                            `
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Check In:</td>
                                                                <td class="py-1">${checkin} <span class="text-red-500">${checkin_requested ? '(Requested)' : ''}</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Check Out:</td>
                                                                <td class="py-1">${checkout} <span class="text-red-500">${checkout_requested ? '(Requested)' : ''}</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Break Start:</td>
                                                                <td class="py-1">${breakStart}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Break End:</td>
                                                                <td class="py-1">${breakEnd}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Total Worked:</td>
                                                                <td class="py-1">${workedHours}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Total Break:</td>
                                                                <td class="py-1">${totalBreak}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Late Check-In Reason:</td>
                                                                <td class="py-1 break-words max-w-xs">${lateCheckInReason}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Early Check-Out Reason:</td>
                                                                <td class="py-1 break-words max-w-xs">${earlyCheckOutReason}</td>
                                                            </tr>
                                                            ` : ``}

                             ${attendance?.leave ?
                                     `<tr>
                                                                                    <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Leave Reason:</td>
                                                                                    <td class="py-1">${attendance?.leave?.reason || '-'}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Leave Date:</td>
                                                                                        <td class="py-1">${attendance?.leave?.from_date === attendance?.leave?.to_date ? attendance?.leave?.from_date : `${attendance?.leave?.from_date} TO ${attendance?.leave?.to_date}`  }</td>
                                                                                    </tr>`
                                :``}

                                <tr>
                        <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Office Hours:</td>
                        ${attendance_rule ?
                        `<td class="py-1 break-words max-w-xs">${attendance_rule?.check_in_time && attendance_rule?.check_out_time ? `${convertToAmPm(attendance_rule?.check_in_time)} - ${convertToAmPm(attendance_rule?.check_out_time)}` : `${attendance_rule?.check_in_time ? convertToAmPm(attendance_rule?.check_in_time) + ' (Check In)' : convertToAmPm(attendance_rule?.check_out_time) ? convertToAmPm(attendance_rule?.check_out_time) + ' (Check Out)' : '-'}`}</td>`
                         : `<td class="py-1">-</td>`}

                        </tr>
                            <tr>
                                <td class="py-1 pr-4 font-semibold text-gray-800 w-40 whitespace-nowrap">Status:</td>
                                <td class="py-1">${status}</td>
                            </tr>
                                ${attendance?.location_log ? `
                                <tr>
                                    <td colspan="2" class="py-1 text-center">
                                        <a href="${viewMapBaseUrl}?location=${encodeURIComponent(attendance.location_log)}" target="_blank" class="text-blue-500 underline">View on Map</a>
                                    </td>
                                </tr>
                            ` : ''}
                        </tbody>
                    </table>
                `;

                $modalContent.html(html);
                $modal.removeClass('hidden');
            });

            $closeModalBtn.on('click', function() {
                $modal.addClass('hidden');
            });

            $modal.on('click', function(e) {
                if (e.target === this) {
                    $modal.addClass('hidden');
                }
            });
        });

        function convertToAmPm(time) {
            if (!time) return "";
            const [hour, minute] = time.split(":");
            const date = new Date();
            date.setHours(parseInt(hour), parseInt(minute));
            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }
    </script>
@endsection
