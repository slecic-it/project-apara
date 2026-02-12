<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.header')

    <link rel="stylesheet" href="css/style.css"/>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">
    <div class="logo">APARA Admin</div>

    <a class="active">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a onclick="toggleMenu('appMenu')">
        <i class="bi bi-folder me-2"></i> Applications
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="appMenu">
        <a>Pending</a>
        <a>Accepted</a>
        <a>Approved</a>
        <a>Rejected</a>
    </div>

    <a onclick="toggleMenu('manageMenu')">
        <i class="bi bi-gear me-2"></i> Management
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="manageMenu">
        <a>Banks</a>
        <a>Employees</a>
        <a>Reports</a>
    </div>

    <a onclick="toggleMenu('financeMenu')">
        <i class="bi bi-cash me-2"></i> Finance
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="financeMenu">
        <a>Invoice</a>
        <a>Fee Due</a>
        <a>Receipt</a>
        <a>History</a>
    </div>
</div>

<!-- ================= HEADER ================= -->
<div class="header">
    <div id="datetime"></div>
    <div>
        <i class="bi bi-bell me-3 text-primary"></i>
        Admin User
    </div>
</div>

<!-- ================= MAIN ================= -->
<div class="main">

    <h4>Dashboard</h4>

    <div class="d-flex gap-2 mb-3">
        <a href="#" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Application
        </a>
    </div>

    <!-- ================= WORKFLOW LINE CHART ================= -->
    <div class="chart-card mb-4">
        <h5>Application Workflow (System Flow View)</h5>
        <canvas id="workflowChart"></canvas>
    </div>

</div>

<!-- ================= FOOTER ================= -->
<footer>
© 2025 Sri Lanka Export Credit Insurance Corporation | APARA System
</footer>

<!-- ================= SCRIPT ================= -->
<script> 
function toggleMenu(id){
    let menu = document.getElementById(id);
    menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

/* ===== Workflow Line Chart ===== */
const ctx = document.getElementById('workflowChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            "Application Fill(Branch)",
            "Blacklist Check",
            "Head office Approve",
            "Marketing Review",
            "Approve/Reject",
            "Finance check",
            "Payment Verify",
            "Invoice Generate",
            "Receipt Issue",
            "Operation Approval",
            "Loan Settle"
        ],
        datasets: [{
            label: "Application Workflow",
            data: [1,2,3,4,5,6,7,8,9,10,11],
            borderWidth: 3,
            tension: 0.3,
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true, 
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

</body>
</html>
