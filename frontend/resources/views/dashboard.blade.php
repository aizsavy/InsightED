<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>InsightEd - Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 min-h-screen bg-white dark:bg-gray-800 shadow-lg p-6 flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mb-6">InsightEd</h1>
            <div class="space-y-2">
                <input type="file" id="csvFile" accept=".csv" class="hidden" />
                <button onclick="document.getElementById('csvFile').click()" class="w-full bg-gray-200 dark:bg-gray-600 py-2 rounded-md text-sm">Choose File</button>
                <button onclick="uploadCSV()" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Upload & Process</button>
                <button onclick="analyzeAll()" class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700">Analyze All</button>
                <button onclick="resetData()" class="w-full bg-gray-300 dark:bg-gray-600 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">Reset Data</button>
            </div>
        </div>
        <div class="mt-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" onchange="toggleDarkMode()" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Dark Mode</span>
            </label>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                <h4 class="text-gray-500 dark:text-gray-400 text-sm">Total Surveys</h4>
                <p id="totalSurveys" class="text-2xl font-bold">0</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                <h4 class="text-gray-500 dark:text-gray-400 text-sm">Traits Analyzed</h4>
                <p class="text-2xl font-bold">5</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                <h4 class="text-gray-500 dark:text-gray-400 text-sm">Most Common Trait</h4>
                <p id="dominantTrait" class="text-xl font-semibold text-purple-600 dark:text-purple-400">Loading...</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                <h4 class="text-gray-500 dark:text-gray-400 text-sm">Last Upload</h4>
                <p class="text-xl font-semibold">--</p>
            </div>
        </div>

        <!-- Student Table -->
<div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
            Student List
            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                Click on any student to view their personality result.
            </p>
        </caption>
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">ID Number</th>
                <th class="px-6 py-3">Student Name</th>
                <th class="px-6 py-3">Age</th>
                <th class="px-6 py-3 text-right">Analyze</th>
            </tr>
        </thead>
        <tbody id="studentTableBody"></tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="flex justify-end p-4 space-x-2" id="paginationControls"></div>
</div>



            <!-- Chart Area -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-2">Trait Distribution</h3>
                <canvas id="barChart" height="200"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-2">Dominant Trait Breakdown</h3>
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <h3 class="text-lg font-semibold text-blue-600 border-l-4 border-blue-500 pl-2">AI–Generated Teaching Strategies</h3>
            <div id="recommendations" class="mt-2 text-sm text-gray-700 dark:text-gray-200 space-y-2"></div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="resultModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 w-full max-w-3xl mx-auto rounded-lg shadow-xl p-8 relative">
        <button onclick="closeModal()" class="absolute top-2 right-3 text-xl text-red-500 font-bold">&times;</button>
        <h3 class="text-xl font-semibold mb-4">Student Analysis Result</h3>
        <div id="modalContent" class="text-sm text-gray-800 dark:text-gray-100"></div>
    </div>
</div>

<script>
const apiUrl = "http://127.0.0.1:5000";
let barChart, pieChart;
let currentPage = 1;
const studentsPerPage = 5;
let allStudents = [];

function uploadCSV() {
    const fileInput = document.getElementById('csvFile');
    const file = fileInput.files[0];
    if (!file) return alert("❌ Please select a file.");

    const formData = new FormData();
    formData.append("file", file);

    fetch(`${apiUrl}/upload`, {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(() => {
        alert("✅ File uploaded!");
        fetchDashboardStats();        // 👈 Refresh the summary cards
        fetchStudentList();           // 👈 Re-render student table
        setTimeout(generateCharts, 500);  // 👈 Update visual charts
    })
    .catch(err => console.error("❌ Upload Error:", err));
}


function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
}

function analyzeAll() {
    fetch(`${apiUrl}/assess/all`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("recommendations").innerHTML = `
            <strong>Most Dominant Trait:</strong> ${data.dominant_trait}<br>
            <strong>Recommendation:</strong> ${data.recommendation}
        `;
        fetchDashboardStats();
    })
    .catch(err => console.error("Analyze error:", err));
}

function resetData() {
    location.reload();
}

function fetchDashboardStats() {
    fetch(`${apiUrl}/dashboard/stats`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalSurveys").textContent = data.totalSurveys || "0";
        document.getElementById("dominantTrait").textContent = data.mostCommonTrait || "N/A";
    })
    .catch(err => console.error("Dashboard stats error:", err));
}


function generateCharts() {
    fetch(`${apiUrl}/assess/ocean-averages`)
    .then(res => res.json())
    .then(data => {
        const labels = Object.keys(data);
        const values = Object.values(data);
        const colors = ['#3B82F6', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'];

        const barCtx = document.getElementById("barChart").getContext("2d");
        if (barChart) barChart.destroy();
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Average Score', data: values, backgroundColor: colors }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, max: 5 } }
            }
        });

        const pieCtx = document.getElementById("pieChart").getContext("2d");
        if (pieChart) pieChart.destroy();
        pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: { labels, datasets: [{ data: values, backgroundColor: colors }] },
            options: { responsive: true }
        });
    })
    .catch(err => console.error("Chart error:", err));
}

function fetchStudentList() {
    fetch(`${apiUrl}/students`)
    .then(res => res.json())
    .then(data => {
        allStudents = data;
        renderStudentPage(currentPage);
        renderPaginationControls();
    })
    .catch(err => console.error("Fetch students error:", err));
}

function renderStudentPage(page) {
    const start = (page - 1) * studentsPerPage;
    const end = start + studentsPerPage;
    const paginatedStudents = allStudents.slice(start, end);

    const tbody = document.getElementById("studentTableBody");
    tbody.innerHTML = "";

    paginatedStudents.forEach(s => {
        const tr = document.createElement("tr");
        tr.className = "bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700";
        tr.innerHTML = `
            <td class="px-6 py-4">${s["STUDENT ID"]}</td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white cursor-pointer" onclick="analyzeStudent(${s["STUDENT ID"]})">
                ${s["STUDENT NAME"]}
            </td>
            <td class="px-6 py-4">${s["AGE"] || "--"}</td>
            <td class="px-6 py-4 text-right">
                <button onclick="analyzeStudent(${s['STUDENT ID']})"
                    class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Analyze</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPaginationControls() {
    const totalPages = Math.ceil(allStudents.length / studentsPerPage);
    const pagination = document.getElementById("paginationControls");
    pagination.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.innerText = i;
        btn.className = `px-3 py-1 rounded-md text-sm ${
            i === currentPage
                ? "bg-blue-600 text-white"
                : "bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
        } hover:bg-blue-500 hover:text-white`;
        btn.onclick = () => {
            currentPage = i;
            renderStudentPage(currentPage);
            renderPaginationControls();
        };
        pagination.appendChild(btn);
    }
}


function analyzeStudent(id) {
    fetch(`${apiUrl}/assess/individual?student_id=${id}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("modalContent").innerHTML = data.result;
        document.getElementById("resultModal").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    })
    .catch(err => console.error("Student analysis error:", err));
}

function closeModal() {
    document.getElementById("resultModal").classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
}

document.addEventListener("DOMContentLoaded", () => {
    fetchDashboardStats();
    fetchStudentList();
    setTimeout(generateCharts, 500);
});
</script>
</body>
</html>
