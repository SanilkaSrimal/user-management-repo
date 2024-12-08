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

if (isset($_GET['memberId'])) {
    $memberId = intval($_GET['memberId']);

    // Fetch member details
    $memberQuery = "SELECT * FROM members WHERE member_id = $memberId";
    $memberResult = $conn->query($memberQuery);
    if ($memberResult && $memberResult->num_rows > 0) {
        $memberDetails = $memberResult->fetch_assoc();
    }

    // Fetch family details
    $familyQuery = "SELECT * FROM family_members WHERE member_id = $memberId";
    $familyDetails = $conn->query($familyQuery);

    // Fetch loan details
    $loanQuery = "SELECT * FROM loan_details WHERE member_id = $memberId";
    $table1Details = $conn->query($loanQuery);

    // Fetch bail details
    $bailQuery = "SELECT * FROM bail_details WHERE member_id = $memberId";
    $table2Details = $conn->query($bailQuery);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member'])) {
    $memberId = intval($_POST['member']['member_id']);
    $headName = $conn->real_escape_string($_POST['member']['head_name']);
    $nic = $conn->real_escape_string($_POST['member']['nic']);
    $address = $conn->real_escape_string($_POST['member']['address']);
    $familyCount = intval($_POST['member']['family_count']);

    // Update member details
    $updateMemberQuery = "UPDATE members SET head_name='$headName', nic='$nic', address='$address', family_count=$familyCount WHERE member_id=$memberId";
    $conn->query($updateMemberQuery);

    // Update family details
    if (isset($_POST['family'])) {
        foreach ($_POST['family'] as $subNumber => $familyData) {
            $name = $conn->real_escape_string($familyData['name']);
            $gender = $conn->real_escape_string($familyData['gender']);
            $birthday = $conn->real_escape_string($familyData['birthday']);
            $relation = $conn->real_escape_string($familyData['relation']);

            $updateFamilyQuery = "UPDATE family_members SET name='$name', gender='$gender', birthday='$birthday', relation='$relation' WHERE member_id=$memberId AND sub_number=$subNumber";
            $conn->query($updateFamilyQuery);
        }
    }

    // Update loan details
    if (isset($_POST['loans'])) {
        foreach ($_POST['loans'] as $loanId => $loanData) {
            $getDate = $conn->real_escape_string($loanData['get_date']);
            $price = floatval($loanData['price']);
            $finalDate = $conn->real_escape_string($loanData['final_date']);

            $updateLoanQuery = "UPDATE loan_details SET get_date='$getDate', price=$price, final_date='$finalDate' WHERE  member_id=$memberId";
            $conn->query($updateLoanQuery);
        }
    }

    // Update bail details
    if (isset($_POST['bails'])) {
        foreach ($_POST['bails'] as $bailId => $bailData) {
            $bailName = $conn->real_escape_string($bailData['bail_name']);
            $bailGetDate = $conn->real_escape_string($bailData['bail_getDate']);
            $bailPrice = floatval($bailData['bail_price']);
            $bailFinalDate = $conn->real_escape_string($bailData['bail_finalDate']);

            $updateBailQuery = "UPDATE bail_details SET bail_name='$bailName', bail_getDate='$bailGetDate', bail_price=$bailPrice, bail_finalDate='$bailFinalDate' WHERE bail_id=$bailId AND member_id=$memberId";
            $conn->query($updateBailQuery);
        }
    }

    header("Location: findMember.php?memberId=$memberId&success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f3f4f6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        /* Navbar Styles */
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
        /* Container */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2, h3 {
            text-align: center;
            color: #309425;
        }
        h1{
            text-align: center;
        }
        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: bold;
            color: #374151;
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            background: #f9fafb;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #3b82f6;
            outline: none;
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
        button:active {
            transform: translateY(1px);
        }
        .deletebtn {
            background-color: #f43f5e;
        }
        .deletebtn:hover {
            background-color: #e11d48;
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.95rem;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
            cursor: pointer;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #f3f4f6;
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
        <a href="form.php">Add Members</a>
    </div>
</div>

<div class="container">
    <h1>සාමාජික තොරතුරු</h1>
    <?php if ($memberDetails): ?>
        <form method="POST">
            <h3>සාමාජිකයාගේ විස්තර</h3>
            <input type="hidden" name="member[member_id]" value="<?= $memberDetails['member_id'] ?>">
            <label>නම:</label>
            <input type="text" name="member[head_name]" value="<?= htmlspecialchars($memberDetails['head_name']) ?>" required>
            <label>ජාතික හැදුනුම්පත් අංකය:</label>
            <input type="text" name="member[nic]" value="<?= htmlspecialchars($memberDetails['nic']) ?>" required>
            <label>ලිපිනය:</label>
            <input type="text" name="member[address]" value="<?= htmlspecialchars($memberDetails['address']) ?>" required>
            <label>කණ්ඩායම් අංකය:</label>
            <input type="number" name="member[team_id]" value="<?= $memberDetails['team_id'] ?>" required>
            <label>පවුලේ සාමාජිකයින් සංඛ්‍යාව:</label>
            <input type="number" name="member[family_count]" value="<?= $memberDetails['family_count'] ?>" required>

            <h3>පවුලේ විස්තර</h3>
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
                        <td><?= $family['sub_number'] ?></td>
                        <td><input type="text" name="family[<?= $family['sub_number'] ?>][name]" value="<?= htmlspecialchars($family['name']) ?>"></td>
                        <td>
                            <select name="family[<?= $family['sub_number'] ?>][gender]">
                                <option value="Male" <?= (strcasecmp($family['gender'], 'Male') === 0) ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= (strcasecmp($family['gender'], 'Female') === 0) ? 'selected' : '' ?>>Female</option>
                            </select>
                        </td>
                        <td><input type="date" name="family[<?= $family['sub_number'] ?>][birthday]" value="<?= $family['birthday'] ?>"></td>
                        <td><input type="text" name="family[<?= $family['sub_number'] ?>][relation]" value="<?= htmlspecialchars($family['relation']) ?>"></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <h3>ණය ලබාගැනීම්</h3>
            <table>
                <thead>
                <tr>
                    <th>ණය ලබාගත් දිනය</th>
                    <th>ලබාගත් මුදල</th>
                    <th>ගෙවා අවසන් කලයුතු දිනය</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($loan = $table1Details->fetch_assoc()): ?>
                    <tr>
                        <td><input type="date" name="loans[<?= $loan['get_date'] ?>][get_date]" value="<?= $loan['get_date'] ?>"></td>
                        <td><input type="number" name="loans[<?= $loan['get_date'] ?>][price]" value="<?= $loan['price'] ?>"></td>
                        <td><input type="date" name="loans[<?= $loan['get_date'] ?>][final_date]" value="<?= $loan['final_date'] ?>"></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <h3>ඇප තොරතුරු</h3>
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
                <?php while ($bail = $table2Details->fetch_assoc()): ?>
                    <tr>
                        <td><input type="text" name="bails[<?= $bail['bail_id'] ?>][bail_name]" value="<?= htmlspecialchars($bail['bail_name']) ?>"></td>
                        <td><input type="date" name="bails[<?= $bail['bail_id'] ?>][bail_getDate]" value="<?= $bail['bail_getDate'] ?>"></td>
                        <td><input type="number" name="bails[<?= $bail['bail_id'] ?>][bail_price]" value="<?= $bail['bail_price'] ?>"></td>
                        <td><input type="date" name="bails[<?= $bail['bail_id'] ?>][bail_finalDate]" value="<?= $bail['bail_finalDate'] ?>"></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <button type="submit">Save Changes</button>
        </form>
    <?php else: ?>
        <p>No member found.</p>
    <?php endif; ?>
</div>
</body>
</html>
