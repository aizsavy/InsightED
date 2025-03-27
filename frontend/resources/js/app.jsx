import React from 'react';
import ReactDOM from 'react-dom/client';
import Dashboard from './components/Dashboard'; // Import React Dashboard Component

ReactDOM.createRoot(document.getElementById('app')).render(
    <React.StrictMode>
        <Dashboard />
    </React.StrictMode>
);


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

window.analyzeStudent = analyzeStudent;
window.closeModal = closeModal;
