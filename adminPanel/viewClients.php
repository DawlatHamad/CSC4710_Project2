<?php
include("../includes/connect.php");

// Initialize variables
$topUsers = [];
$difficultClients = [];
$prospectiveClients = [];
$badClients = [];
$goodClients = [];
$showTopUsers = isset($_POST['current_state_top_users']) && $_POST['current_state_top_users'] === '1';
$showDifficultClients = isset($_POST['current_state_difficult_clients']) && $_POST['current_state_difficult_clients'] === '1';
$showProspectiveClients = isset($_POST['current_state_prospective_clients']) && $_POST['current_state_prospective_clients'] === '1';
$showBadClients = isset($_POST['current_state_bad_clients']) && $_POST['current_state_bad_clients'] === '1';
$showGoodClients = isset($_POST['current_state_good_clients']) && $_POST['current_state_good_clients'] === '1';

// Handle toggle for top users
if (isset($_POST['toggle_top_users'])) {
    $showTopUsers = !$showTopUsers;
}

// Handle toggle for difficult clients
if (isset($_POST['toggle_difficult_clients'])) {
    $showDifficultClients = !$showDifficultClients;
}

// Handle toggle for prospective clients
if (isset($_POST['toggle_prospective_clients'])) {
    $showProspectiveClients = !$showProspectiveClients;
}

// Handle toggle for bad clients
if (isset($_POST['toggle_bad_clients'])) {
    $showBadClients = !$showBadClients;
}

// Handle toggle for good clients
if (isset($_POST['toggle_good_clients'])) {
    $showGoodClients = !$showGoodClients;
}

// Fetch top users if toggle is active
if ($showTopUsers) {
    $sqlTopUsers = "
        SELECT 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role,
            COUNT(t.transactionid) AS transaction_count
        FROM 
            Users u
        JOIN 
            Transactions t
        ON 
            u.userid = t.userid
        GROUP BY 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role
        HAVING 
            transaction_count = (
                SELECT MAX(transaction_count) 
                FROM (
                    SELECT COUNT(t.transactionid) AS transaction_count
                    FROM Users u
                    JOIN Transactions t
                    ON u.userid = t.userid
                    GROUP BY u.userid
                ) AS Counts
            );
    ";

    $resultTopUsers = $conn->query($sqlTopUsers);
    if ($resultTopUsers && $resultTopUsers->num_rows > 0) {
        while ($row = $resultTopUsers->fetch_assoc()) {
            $topUsers[] = $row;
        }
    }
}

// Fetch difficult clients if toggle is active
if ($showDifficultClients) {
    $sqlDifficultClients = "
        SELECT 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role,
            COUNT(w.workid) AS pending_work_count
        FROM 
            Users u
        JOIN 
            Work w
        ON 
            u.userid = w.userid
        WHERE 
            w.client_status = 'pending'
        GROUP BY 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role
        HAVING 
            COUNT(w.workid) >= 3;
    ";

    $resultDifficultClients = $conn->query($sqlDifficultClients);
    if ($resultDifficultClients && $resultDifficultClients->num_rows > 0) {
        while ($row = $resultDifficultClients->fetch_assoc()) {
            $difficultClients[] = $row;
        }
    }
}

// Fetch prospective clients if toggle is active
if ($showProspectiveClients) {
    $sqlProspectiveClients = "
        SELECT 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role
        FROM 
            Users u
        LEFT JOIN 
            Quote q
        ON 
            u.userid = q.userid
        WHERE 
            q.userid IS NULL AND u.userid != 1; -- Exclude user with userid = 1
    ";

    $resultProspectiveClients = $conn->query($sqlProspectiveClients);
    if ($resultProspectiveClients && $resultProspectiveClients->num_rows > 0) {
        while ($row = $resultProspectiveClients->fetch_assoc()) {
            $prospectiveClients[] = $row;
        }
    }
}

// Fetch bad clients if toggle is active
if ($showBadClients) {
    $sqlBadClients = "
        SELECT 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role,
            COUNT(t.transactionid) AS pending_transaction_count
        FROM 
            Users u
        JOIN 
            Transactions t
        ON 
            u.userid = t.userid
        WHERE 
            t.charge_status = 'pending'
            AND t.created_at <= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) -- Pending for over a week
        GROUP BY 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role;
    ";

    $resultBadClients = $conn->query($sqlBadClients);
    if ($resultBadClients && $resultBadClients->num_rows > 0) {
        while ($row = $resultBadClients->fetch_assoc()) {
            $badClients[] = $row;
        }
    }
}

// Fetch good clients if toggle is active
if ($showGoodClients) {
    $sqlGoodClients = "
        SELECT 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role
        FROM 
            Users u
        JOIN 
            TransactionPaid t
        ON 
            u.userid = t.userid
        WHERE 
            t.paid_date <= DATE_ADD(t.created_at, INTERVAL 7 DAY)
        GROUP BY 
            u.userid, 
            u.email, 
            u.firstname, 
            u.lastname, 
            u.address, 
            u.phone, 
            u.role;
    ";

    $resultGoodClients = $conn->query($sqlGoodClients);
    if ($resultGoodClients && $resultGoodClients->num_rows > 0) {
        while ($row = $resultGoodClients->fetch_assoc()) {
            $goodClients[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        #biggestClientsTable, #difficultClientsTable, #prospectiveClientsTable, #badClientsTable, #goodClientsTable {
            display: none;
            margin-top: 20px;
        }
        .toggle-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toggle for Top Users -->
        <form method="post">
            <input type="hidden" name="current_state_top_users" value="<?= $showTopUsers ? '1' : '0' ?>">
            <button type="submit" name="toggle_top_users" class="btn btn-primary toggle-button">
                <?= $showTopUsers ? 'Hide Biggest Client(s)' : 'Show Biggest Client(s)' ?>
            </button>
        </form>

        <!-- Biggest Clients Table -->
        <div id="biggestClientsTable" style="display: <?= $showTopUsers ? 'block' : 'none' ?>;">
            <h2>Biggest Client(s)</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Transaction Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($topUsers)) {
                        foreach ($topUsers as $user) {
                            echo "
                            <tr>
                                <td>{$user['userid']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['firstname']}</td>
                                <td>{$user['lastname']}</td>
                                <td>{$user['address']}</td>
                                <td>{$user['phone']}</td>
                                <td>{$user['transaction_count']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Toggle for Difficult Clients -->
        <form method="post">
            <input type="hidden" name="current_state_difficult_clients" value="<?= $showDifficultClients ? '1' : '0' ?>">
            <button type="submit" name="toggle_difficult_clients" class="btn btn-secondary toggle-button">
                <?= $showDifficultClients ? 'Hide Difficult Clients' : 'Show Difficult Clients' ?>
            </button>
        </form>

        <!-- Difficult Clients Table -->
        <div id="difficultClientsTable" style="display: <?= $showDifficultClients ? 'block' : 'none' ?>;">
            <h2>Difficult Clients</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Pending Work Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($difficultClients)) {
                        foreach ($difficultClients as $client) {
                            echo "
                            <tr>
                                <td>{$client['userid']}</td>
                                <td>{$client['email']}</td>
                                <td>{$client['firstname']}</td>
                                <td>{$client['lastname']}</td>
                                <td>{$client['address']}</td>
                                <td>{$client['phone']}</td>
                                <td>{$client['pending_work_count']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Toggle for Prospective Clients -->
        <form method="post">
            <input type="hidden" name="current_state_prospective_clients" value="<?= $showProspectiveClients ? '1' : '0' ?>">
            <button type="submit" name="toggle_prospective_clients" class="btn btn-primary toggle-button">
                <?= $showDifficultClients ? 'Hide Prospective Clients' : 'Show Prospective Clients' ?>
            </button>
        </form>

        <!-- Prospective Clients Table -->
        <div id="prospectiveClientsTable" style="display: <?= $showProspectiveClients ? 'block' : 'none' ?>;">
            <h2>Prospective Clients</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($prospectiveClients)) {
                        foreach ($prospectiveClients as $client) {
                            echo "
                            <tr>
                                <td>{$client['userid']}</td>
                                <td>{$client['email']}</td>
                                <td>{$client['firstname']}</td>
                                <td>{$client['lastname']}</td>
                                <td>{$client['address']}</td>
                                <td>{$client['phone']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Toggle for Bad Clients -->
        <form method="post">
            <input type="hidden" name="current_state_bad_clients" value="<?= $showBadClients ? '1' : '0' ?>">
            <button type="submit" name="toggle_bad_clients" class="btn btn-secondary toggle-button">
                <?= $showDifficultClients ? 'Hide Bad Clients' : 'Show Bad Clients' ?>
            </button>
        </form>

        <!-- Bad Clients Table -->
        <div id="badClientsTable" style="display: <?= $showBadClients ? 'block' : 'none' ?>;">
            <h2>Bad Clients</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Pending Transaction Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($badClients)) {
                        foreach ($badClients as $client) {
                            echo "
                            <tr>
                                <td>{$client['userid']}</td>
                                <td>{$client['email']}</td>
                                <td>{$client['firstname']}</td>
                                <td>{$client['lastname']}</td>
                                <td>{$client['address']}</td>
                                <td>{$client['phone']}</td>
                                <td>{$client['pending_transaction_count']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Toggle for Good Clients -->
        <form method="post">
            <input type="hidden" name="current_state_good_clients" value="<?= $showGoodClients ? '1' : '0' ?>">
            <button type="submit" name="toggle_good_clients" class="btn btn-primary toggle-button">
                <?= $showDifficultClients ? 'Hide Good Clients' : 'Show Good Clients' ?>
            </button>
        </form>

        <!-- Good Clients Table -->
        <div id="goodClientsTable" style="display: <?= $showGoodClients ? 'block' : 'none' ?>;">
            <h2>Good Clients</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($goodClients)) {
                        foreach ($goodClients as $client) {
                            echo "
                            <tr>
                                <td>{$client['userid']}</td>
                                <td>{$client['email']}</td>
                                <td>{$client['firstname']}</td>
                                <td>{$client['lastname']}</td>
                                <td>{$client['address']}</td>
                                <td>{$client['phone']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Clients Table -->
        <div class="container">
            <div class="row">
                <h2>Client(s)</h2>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $selectClient = "SELECT * FROM Users WHERE role != 'contractor'";
                        $resultSelect = $conn->query($selectClient);

                        if ($resultSelect && $resultSelect->num_rows > 0) {
                            while ($row_client = $resultSelect->fetch_assoc()) {
                                echo "
                                <tr>
                                    <td>{$row_client['userid']}</td>
                                    <td>{$row_client['email']}</td>
                                    <td>{$row_client['firstname']}</td>
                                    <td>{$row_client['lastname']}</td>
                                    <td>{$row_client['address']}</td>
                                    <td>{$row_client['phone']}</td>
                                </tr>
                                ";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No data available.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
