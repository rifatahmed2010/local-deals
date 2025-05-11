@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Deal Anylytic
        </div>
        <div class="card-body">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <!-- Add a canvas for the chart -->
            <canvas id="multiLineChart" style="height: 400px;"></canvas>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetchChartData();
    });

    function fetchChartData() {
        fetch('/chart-data') // Endpoint to get data from the server
            .then(response => response.json())
            .then(data => {
                renderChart(data);
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    function renderChart(data) {
        const ctx = document.getElementById('multiLineChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels, // X-axis labels from API
                datasets: [
                    {
                        label: 'Clicks',
                        data: data.clicks, // Y-axis values for Clicks
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Redemption',
                        data: data.redemptions, // Y-axis values for Redemptions
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
</script>

