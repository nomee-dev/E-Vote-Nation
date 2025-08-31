<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<h3>Welcome to E-Vote Nation</h3>
<hr>

<div class="col-12">
    <div class="row gx-3 row-cols-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-user-tie fs-3 text-success"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Candidates</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $candidates = $conn->query("SELECT count(candidate_id) as `count` FROM `candidate_list` ")->fetchArray()['count'];
                                echo $candidates > 0 ? number_format($candidates) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-user-friends fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Validated Voters</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $voter = $conn->query("SELECT count(voter_id) as `count` FROM `voter_list` where status = 1 ")->fetchArray()['count'];
                                echo $voter > 0 ? number_format($voter) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-user-friends fs-3 text-secondary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Voters for Validation</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $voter = $conn->query("SELECT count(voter_id) as `count` FROM `voter_list` where status = 0 ")->fetchArray()['count'];
                                echo $voter > 0 ? number_format($voter) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-user-friends fs-3 text-primary"></span>

                            <!-- <span ></span> -->
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Users</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $admin = $conn->query("SELECT count(admin_id) as `count` FROM `admin_list`")->fetchArray()['count'];
                                echo $admin > 0 ? number_format($admin) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Query for male and female voters
$male_voters = $conn->query("SELECT count(voter_id) as count FROM voter_list WHERE gender = 'Male' AND status = 1")->fetchArray()['count'];
$female_voters = $conn->query("SELECT count(voter_id) as count FROM voter_list WHERE gender = 'Female' AND status = 1")->fetchArray()['count'];

// Query for validated and pending voters
$validated_voters = $conn->query("SELECT count(voter_id) as count FROM voter_list WHERE status = 1")->fetchArray()['count'];
$pending_voters = $conn->query("SELECT count(voter_id) as count FROM voter_list WHERE status = 0")->fetchArray()['count'];
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <canvas id="myPieChart" width="300" height="300"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="statusPieChart" width="300" height="300"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('myPieChart').getContext('2d');
    const myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Voters',
                data: [<?php echo $male_voters; ?>, <?php echo $female_voters; ?>],
                backgroundColor: ['#36A2EB', '#FF6384'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });

    const ctx2 = document.getElementById('statusPieChart').getContext('2d');
    const statusPieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Validated', 'Pending'],
            datasets: [{
                label: 'Voter Status',
                data: [<?php echo $validated_voters; ?>, <?php echo $pending_voters; ?>],
                backgroundColor: ['#4BC0C0', '#FFCE56'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });
</script>