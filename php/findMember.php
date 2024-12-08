<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "themiya";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$memberDetails = null;
$familyDetails = null;
$table1Details = null;
$table2Details = null;

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['memberId'])) {
    $memberId = intval($_POST['memberId']);

    // Fetch member details
    $memberQuery = "SELECT * FROM members WHERE member_id = $memberId";
    $memberResult = $conn->query($memberQuery);

    if ($memberResult && $memberResult->num_rows > 0) {
        $memberDetails = $memberResult->fetch_assoc();
    }

    // Fetch family details
    $familyQuery = "SELECT * FROM family_members WHERE member_id = $memberId";
    $familyDetails = $conn->query($familyQuery);

    // Fetch details from table1
    $table1Query = "SELECT * FROM loan_details WHERE member_id = $memberId";
    $table1Details = $conn->query($table1Query);

    // Fetch details from table2
    $table2Query = "SELECT * FROM bail_details WHERE member_id = $memberId";
    $table2Details = $conn->query($table2Query);
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            background-color: #45a049;
            border-radius: 5px;
        }
        .container {
            max-width: 850px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2, h3 {
            text-align: center;
            color: #4CAF50;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 90%;
        }
        button {
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: #4CAF50;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button:hover {
            background-color: #45a049;
            transform: translateY(-3px);
        }
        .searchbtn {
            width: 93%;
        }
        .editbtn {
            width: 15%;
            display: inline-block;
            margin: 5px;
            float: right;
            
        }
        .deletebtn {
            width: 15%;
            display: inline-block;
            margin: 5px;
            float: right;
            background: #aa0004
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="#">සමගි සමිතිය</a>
        </div>
        <div class="links">
            <a href="form.php">Home</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
            <a href="form.php">Add members</a>
        </div>
    </div>
    <div class="container">
        <h1>සාමාජික තොරතුරු</h1>
        <form method="POST" action="findMember.php">
            <label for="memberId">සාමාජික අංකය ඇතුලත් කරන්න</label>
            <input type="number" id="memberId" name="memberId" placeholder="Enter Member ID" required>
            <button type="submit" class="searchbtn">Search</button>
        </form>

        <?php if ($memberDetails): ?>
            <h2>සාමාජිකයාගේ විස්තර</h2>
            <p><strong>නම :</strong> <?= htmlspecialchars($memberDetails['head_name']) ?></p>
            <p><strong>කණ්ඩායම් අංකය :</strong> <?= htmlspecialchars($memberDetails['team_id']) ?></p>
            <p><strong>ජාතික හැදුනුම්පත් අංකය :</strong> <?= htmlspecialchars($memberDetails['nic']) ?></p>
            <p><strong>ලිපිනය:</strong> <?= htmlspecialchars($memberDetails['address']) ?></p>
            <p><strong>පවුලේ සාමාජිකයින් සංඛ්‍යාව:</strong> <?= $memberDetails['family_count'] ?></p>

            <h3>පවුලේ විස්තර</h3>
            <?php if ($familyDetails && $familyDetails->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>අනු අංකය</th>
                            <th>නම</th>
                            <th>ලිංගිකත්වය</th>
                            <th>උපන්දිනය</th>
                            <th>සම්බන්දතාවය</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($family = $familyDetails->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($family['sub_number']) ?></td>
                                <td><?= htmlspecialchars($family['name']) ?></td>
                                <td><?= htmlspecialchars($family['gender']) ?></td>
                                <td><?= htmlspecialchars($family['birthday']) ?></td>
                                <td><?= htmlspecialchars($family['relation']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No family details found for this member.</p>
            <?php endif; ?>

            <h3>ණය ලබාගැනීම්</h3>
            <?php if ($table1Details && $table1Details->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ණය ලබාගත් දිනය</th>
                            <th>ලබාගත් මුදල</th>
                            <th>ගෙවා අවසන් කලයුතු දිනය</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $table1Details->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['get_date']) ?></td>
                                <td><?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['final_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No details found in Table 1 for this member.</p>
            <?php endif; ?>

            <h3>ඇප තොරතුරු</h3>
            <?php if ($table2Details && $table2Details->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ණය ලබාගත් අයගේ නම</th>
                            <th>ණය ලබාගත් දිනය</th>
                            <th>ලබාගත් මුදල</th>
                            <th>ගෙවා අවසන් කලයුතු දිනය</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $table2Details->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['bail_name']) ?></td>
                                <td><?= htmlspecialchars($row['bail_getDate']) ?></td>
                                <td><?= htmlspecialchars($row['bail_price']) ?></td>
                                <td><?= htmlspecialchars($row['bail_finalDate']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No details found in Table 2 for this member.</p>
            <?php endif; ?>

            <div style="text-align: center; margin-bottom: 60px;">
            <a href="deleteMember.php?memberId=<?= htmlspecialchars($memberDetails['member_id']) ?>">

<button type="button" class="deletebtn">Delete</button>

</a>
    <a href="editMember.php?memberId=<?= htmlspecialchars($memberDetails['member_id']) ?>">
        
        
        <button type="button" class="editbtn">Edit</button>
    </a>
    
</div>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>No details found for Member ID <?= htmlspecialchars($memberId) ?>.</p>
        <?php endif; ?>
    </div>
</body>
</html>