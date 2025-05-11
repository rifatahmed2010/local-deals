@extends('layouts.app')
@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Time Based Performance Anylytic
        <div class="row">
            <div class="col-md-4">
                <input type="date" name="start_date" id="start_date" class="form-control sm">
            </div>
            <div class="col-md-4">
                <input type="date" name="end_date" id="end_date" class="form-control sm">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary" onclick="fetchChartData()">Search</button>
            </div>

        </div>

    </div>
    <div class="card-body">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Add a canvas for the chart -->
        <canvas id="myChart" style="height: 400px;"></canvas>
    </div>
</div>
@endsection
<!-- Add Chart.js from CDN -->

<!-- Add a canvas for the chart -->


<!-- JavaScript to initialize the chart -->
<script>


    let myChart = null; // Store the chart instance globally

    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();

        // Get the date one month ago
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);

        // Format the dates in YYYY-MM-DD format
        const formatDate = date => date.toISOString().split('T')[0];

        // Set the default values
        document.getElementById('start_date').value = formatDate(oneMonthAgo);
        document.getElementById('end_date').value = formatDate(today);

        fetchChartData();
    });

    function fetchChartData() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const requestData = {
            start_date: document.querySelector('input[name="start_date"]').value || "2024-01-02",
            end_date: document.querySelector('input[name="end_date"]').value || "2025-02-20",
        };

        fetch('/api/time-performance-anlysis-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(requestData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                renderChart(data);  // Render or update the chart with new data
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    function renderChart(data) {
        const ctx = document.getElementById('myChart').getContext('2d');
        if (myChart) {
            myChart.data.labels = data.clmlabels;  // Update X-axis labels
            myChart.data.datasets[0].data = data.claims;  // Update Deal Claims dataset

            // Re-render the chart
            myChart.update();
        }else{
            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.clmlabels,
                    datasets: [
                        {
                            label: 'Deal Claims',
                            data: data.claims,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        }

    }

</script>


