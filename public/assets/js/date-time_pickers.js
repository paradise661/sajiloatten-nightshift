(function () {
    "use strict";

    /* To choose date */
    flatpickr("#date", {
         maxDate: "today"
    });

    flatpickr("#fulldate", {
    });

    flatpickr(".flat-picker", {
    });

    /* To choose date and time */
    flatpickr("#datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    /* For Human Friendly dates */
    flatpickr("#humanfrienndlydate", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    /* For Date Range Picker */
    flatpickr("#daterange", {
        mode: "range",
        dateFormat: "Y-m-d",
         maxDate: "today"
    });


    flatpickr("#daterangeCalendar", {
        mode: "range",
        dateFormat: "Y-m-d",
    });

    /* For Time Picker */
    flatpickr("#timepikcr", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultHour: 9,
    });

    /* For Time Picker With 24hr Format */
    flatpickr("#timepickr1", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
         defaultHour: 10,
    });

    /* For Time Picker With Limits */
    flatpickr("#limittime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        minTime: "16:00",
        maxTime: "22:30",
    });

    /* For DateTimePicker with Limited Time Range */
    flatpickr("#limitdatetime", {
        enableTime: true,
        minTime: "16:00",
        maxTime: "22:00"
    });

    flatpickr(".checkin_timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // 24-hour format; use "h:i K" for 12-hour
        time_24hr: false,
        defaultHour: 9,
    });

    flatpickr(".checkout_timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // 24-hour format; use "h:i K" for 12-hour
        time_24hr: false,
        defaultHour: 17,
    });

    /* For Inline Calendar */
    flatpickr("#inlinecalendar", {
        inline: true
    });

    /* For Date Pickr With Week Numbers */
    flatpickr("#weeknum", {
        weekNumbers: true,
    });

    /* For Inline Time */
    flatpickr("#inlinetime", {
        inline: true,
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
    });

    /* For Preloading Time */
    flatpickr("#pretime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "13:45"
    });

})();
