<?php
/** @var $this \App\Core\View */
/** @var $stats array */
$this->title = 'API Usage Statistics';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>API Usage Statistics</h3>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Requests</h5>
                                    <h2><?= number_format($stats['total_requests']) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Success Rate</h5>
                                    <h2><?= number_format($stats['success_rate'], 1) ?>%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Active Endpoints</h5>
                                    <h2><?= count($stats['endpoints']) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Last 24h Requests</h5>
                                    <h2><?= number_format($stats['last_24h_requests']) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Requests Over Time (Last 24 Hours)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="requestsChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Endpoint Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="endpointsPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endpoints Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Endpoint Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Endpoint</th>
                                            <th>Method</th>
                                            <th>Requests</th>
                                            <th>Success Rate</th>
                                            <th>Last Used</th>
                                            <th>First Used</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['endpoints'] as $endpoint): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($endpoint['endpoint']) ?></td>
                                                <td><span class="badge bg-<?= strtolower($endpoint['method']) === 'get' ? 'success' : (strtolower($endpoint['method']) === 'post' ? 'primary' : 'warning') ?>"><?= htmlspecialchars($endpoint['method']) ?></span></td>
                                                <td><?= number_format($endpoint['total_requests']) ?></td>
                                                <td>
                                                    <div class="progress">
                                                        <?php $successRate = $endpoint['avg_response_code'] < 400 ? 100 : 0; ?>
                                                        <div class="progress-bar bg-<?= $successRate > 90 ? 'success' : ($successRate > 70 ? 'warning' : 'danger') ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= $successRate ?>%"
                                                             aria-valuenow="<?= $successRate ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?= number_format($successRate, 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= date('Y-m-d H:i:s', strtotime($endpoint['last_request'] ?? 'now')) ?></td>
                                                <td><?= date('Y-m-d H:i:s', strtotime($endpoint['first_request'] ?? 'now')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Line chart for requests over time
    const hourlyData = <?= json_encode($stats['hourly_requests']) ?>;
    const ctx = document.getElementById('requestsChart').getContext('2d');
    
    // Fill in missing hours with zero values for the last 24 hours
    const last24Hours = [];
    const now = new Date();
    const userTimezoneOffset = now.getTimezoneOffset() * 60000; // Convert to milliseconds

    // Generate data points for each hour
    for (let i = 23; i >= 0; i--) {
        const hourDate = new Date(now - (i * 3600000)); // 3600000 ms = 1 hour
        const localHour = new Date(hourDate - userTimezoneOffset);
        const hourStr = localHour.toISOString().slice(0, 13) + ':00:00';
        
        // Find matching data point or use 0
        const found = hourlyData.find(d => d.hour.startsWith(hourStr.slice(0, 13)));
        
        last24Hours.push({
            hour: hourStr,
            requests: found ? parseInt(found.requests) : 0,
            label: hourDate.toLocaleTimeString([], { hour: '2-digit', hour12: true })
        });
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: last24Hours.map(d => d.label),
            datasets: [{
                label: 'API Requests',
                data: last24Hours.map(d => d.requests),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `Requests: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });

    // Pie chart for endpoint distribution
    const endpointData = <?= json_encode($stats['endpoints']) ?>;
    const pieCtx = document.getElementById('endpointsPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: endpointData.map(e => e.endpoint),
            datasets: [{
                data: endpointData.map(e => e.total_requests),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>

<!-- Add this CSS to make the chart container have a fixed height -->
<style>
.card-body canvas {
    min-height: 300px;
}
</style>
