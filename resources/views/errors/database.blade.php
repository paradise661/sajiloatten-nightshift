<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajilo Attendance | Error</title>
    <meta name="description"
        content="Sajilo Attendance system is a cutting-edge solution for managing employee attendance efficiently. Designed for accuracy, scalability, and ease of use.">
    <meta name="keywords"
        content="Sajilo Attendance system, attendance management, employee attendance software, time tracking, admin dashboard, responsive system, attendance solutions, Paradise attendance system, AttendanceX">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/brand-logos/fav.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="">
    <div class="flex items-center justify-center h-screen bg-gray-100 flex-col">
        <div class="max-w-lg bg-white shadow-xl rounded-2xl p-8 text-center">
            <h1 class="text-3xl font-bold text-red-600 mt-5">Oops! Something Went Wrong</h1>
            <p class="text-gray-600 mt-3">We’re experiencing some issues. Our team is working on it!</p>

            <div class="mt-5 text-left">
                <h2 class="text-xl font-semibold text-gray-700">Need Help?</h2>
                <p class="text-gray-600 mt-2">If the problem persists, please contact our support team:</p>
                <ul class="mt-3 text-gray-700">
                    <li>📞 Phone: <span class="font-semibold">+977-9851199432</span></li>
                    <li>📧 Email: <a class="text-[#0a6a8f] hover:underline"
                            href="mailto:support@sajiloattendance.com">support@sajiloattendance.com</a></li>
                    <li>💬 Website: <a class="text-[#0a6a8f] hover:underline"
                            href="https://sajiloattendance.com">www.sajiloattendance.com</a></li>
                    <li>📍 Office: Kathmandu, Nepal</li>
                </ul>
            </div>

            <a class="mt-6 inline-block bg-[#0a6a8f] text-white px-6 py-3 rounded-lg shadow hover:bg-[#0a6a8f] transition"
                href="https://sajiloattendance.com">
                Click here for more queries
            </a>
        </div>

        <div class="bg-white p-6 rounded-md mt-6 w-2xl">
            <p class="text-red-600">Error: {{ $message }}</p>
        </div>
    </div>

</body>

</html>
