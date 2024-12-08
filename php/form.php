<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "themiya";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberId = isset($_POST['memberId']) ? intval($_POST['memberId']) : null;
    $teamId = isset($_POST['teamId']) ? intval($_POST['teamId']) : null;
    $headName = isset($_POST['headName']) ? $_POST['headName'] : null;
    $nic = isset($_POST['nic']) ? $_POST['nic'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $familyCount = isset($_POST['familyCount']) ? intval($_POST['familyCount']) : null;

    if ($memberId && $teamId && $headName && $nic && $address && $familyCount) {
        $stmt = $conn->prepare("INSERT INTO members (member_id, team_id, head_name, nic, address, family_count) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssi", $memberId, $teamId, $headName, $nic, $address, $familyCount);

        if ($stmt->execute()) {
            // Process family members only if data exists
            if (!empty($_POST['subNumber']) && is_array($_POST['subNumber'])) {
                $stmtFamily = $conn->prepare("INSERT INTO family_members (member_id, sub_number, name, gender, birthday, relation) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtFamily->bind_param("iissss", $memberId, $subNumber, $name, $gender, $birthday, $relation);

                foreach ($_POST['subNumber'] as $i => $subNumber) {
                    $subNumber = intval($subNumber);
                    $name = $_POST['name'][$i];
                    $gender = $_POST['gender'][$i];
                    $birthday = $_POST['birthday'][$i];
                    $relation = $_POST['relation'][$i];

                    if (!$stmtFamily->execute()) {
                        echo "Error inserting family member: " . $stmtFamily->error;
                    }
                }

                $stmtFamily->close();
            }

            // Process loan details only if data exists
            if (!empty($_POST['getDate']) && is_array($_POST['getDate'])) {
                $stmtRow1 = $conn->prepare("INSERT INTO loan_details (member_id, get_date, price, final_date) VALUES (?, ?, ?, ?)");
                $stmtRow1->bind_param("isss", $memberId, $getDate, $price, $finDate);

                foreach ($_POST['getDate'] as $i => $getDate) {
                    $getDate = $getDate;
                    $price = $_POST['price'][$i];
                    $finDate = $_POST['finDate'][$i];

                    if (!$stmtRow1->execute()) {
                        echo "Error inserting into loan_details: " . $stmtRow1->error;
                    }
                }

                $stmtRow1->close();
            }

            // Process bail details only if data exists
            if (!empty($_POST['bname']) && is_array($_POST['bname'])) {
                $stmtRow2 = $conn->prepare("INSERT INTO bail_details (member_id, bail_name, bail_getDate, bail_price, bail_finalDate) VALUES (?, ?, ?, ?, ?)");
                $stmtRow2->bind_param("issss", $memberId, $bName, $bgetDate, $bprice, $bfinDate);

                foreach ($_POST['bname'] as $i => $bName) {
                    $bName = $bName;
                    $bgetDate = $_POST['bgetDate'][$i];
                    $bprice = $_POST['bprice'][$i];
                    $bfinDate = $_POST['bfinDate'][$i];

                    if (!$stmtRow2->execute()) {
                        echo "Error inserting into bail_details: " . $stmtRow2->error;
                    }
                }

                $stmtRow2->close();
            }

            echo "Data successfully inserted.";
        } else {
            echo "Error inserting member: " . $stmt->error;
        }

        $stmt->close();
        header('Location: form.php');
        exit();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Form</title>
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
            background: url('../img/logo.png') no-repeat center;
            background-size: contain;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.9);
        }
        h1 {
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
        input[type="text"],
        input[type="number"],
        input[type="password"],
        input[type="date"] {
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

        button:active {
            transform: translateY(0);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            font-size: 13px;

        }

        th {
            background-color: #f4f4f4;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #777;
        }

        .add-row-btn {
            margin-left: 10px 0;
            width: 21%;
            background-color: #45a049;
            font-size: 0.8rem;
            
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="#">සමගි සමිතිය</a>
        </div>
        <div class="links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
            <a href="findMember.php">View Members</a>
        </div>
    </div>
    <div class="container">
        <h1>සමගි අවමංගල්‍යාධාර සමිතිය</h1>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <label for="memberId">සාමාජික අංකය</label>
            <input type="number" id="memberId" name="memberId" placeholder="සාමාජික අංකය" required>

            <label for="teamId">කණ්ඩායම් අංකය</label>
            <input type="number" id="teamId" name="teamId" placeholder="කණ්ඩායම් අංකය" required>
            
            <label for="headName">ගෘහ මූලිකයාගේ නම</label>
            <input type="text" id="headName" name="headName" placeholder="ගෘහ මූලිකයාගේ නම" required>
            
            <label for="nic">ජාතික හැදුනුම්පත් අංකය</label>
            <input type="text" id="nic" name="nic" placeholder="ජාතික හැදුනුම්පත් අංකය" required>
            
            <label for="address">ලිපිනය</label>
            <input type="text" id="address" name="address" placeholder="ලිපිනය" required>
            
            <label for="familyCount">පවුලේ සාමාජිකයින් සංඛ්‍යාව</label>
            <input type="number" id="familyCount" name="familyCount" placeholder="පවුලේ සාමාජිකයින් සංඛ්‍යාව" required>

            <label for="familyDetails">පවුලේ සාමාජිකයින්ගේ විස්තර (ගෘහ මූලිකයාද ඇතුලත්ව)</label>
            
            <table id="familyTable">
                <thead>
                    <tr>
                        <th width="20%">අනු අංකය</th>
                        <th>නම</th>
                        <th>ස්ත්‍රී / පුරුෂ</th>
                        <th>උපන් දිනය</th>
                        <th width="15%">සම්බන්ධතාවය</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="number" name="subNumber[]" placeholder="අනු අංකය" required></td>
                        <td><input type="text" name="name[]" placeholder="නම" required></td>
                        <td>
                            <select name="gender[]" required >
                                <option value="">තෝරන්න</option>
                                <option value="male">male</option>
                                <option value="female">female</option>
                            </select>
                        </td>
                        <td><input type="date" name="birthday[]"required ></td>
                        <td><input type="text" name="relation[]" placeholder="පුරවන්න" required></td>
                    </tr>
                </tbody>
            </table>
            
            <button type="button" class="add-row-btn" onclick="addRow()">පේළියක් එක් කරන්න</button>
            <br>

            <label for="externalRows1">ණය ලබාගැනීම්</label>
            <table id="externalRows1">
                <thead>
                    <tr>
                        <th>ණය ලබාගත් දිනය</th>
                        <th>ලබාගත් මුදල</th>
                        <th>ගෙවා අවසන් කලයුතු දිනය</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="getDate[]" placeholder="ණය ලබාගත් දිනය" ></td>
                        <td><input type="text" name="price[]" placeholder="ලබාගත් මුදල" ></td>
                        <td><input type="date" name="finDate[]" placeholder="ගෙවා අවසන් කලයුතු දිනය" ></td>
                    </tr>
                </tbody>
            </table>

            <label for="externalRows2">ඇප තොරතුරු</label>
            <table id="externalRows2">
                <thead>
                    <tr>
                        <th>ණය ලබාගත් අයගේ නම</th>
                        <th>ණය ලබාගත් දිනය</th>
                        <th>ලබාගත් මුදල</th>
                        <th>ගෙවා අවසන් කලයුතු දිනය</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="bname[]" placeholder="නම"></td>
                        <td><input type="date" name="bgetDate[]" placeholder="දිනය" ></td>
                        <td><input type="text" name="bprice[]" placeholder="ලබාගත් මුදල" ></td>
                        <td><input type="date" name="bfinDate[]" placeholder="ගෙවා අවසන් කලයුතු දිනය" ></td>
                    </tr>
                </tbody>
            </table>
            <button type="button"class="add-row-btn"onclick="addRowToExternalTable('externalRows2', 'row2Field[]', 'row2Value[]')">පේළියක් එක් කරන්න</button>

            
            <button type="submit">ඇතුලත් කරන්න</button>
        </form>
    </div>

    <script>
        function addRow() {
            const table = document.getElementById('familyTable').getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" name="subNumber[]" placeholder="අනු අංකය" required></td>
                <td><input type="text" name="name[]" placeholder="නම" required></td>
                <td>
                    <select name="gender[]" required>
                        <option value="">තෝරන්න</option>
                        <option value="male">පුරුෂ</option>
                        <option value="female">ස්ත්‍රී</option>
                    </select>
                </td>
                <td><input type="date" name="birthday[]" required></td>
                <td><input type="text" name="relation[]" placeholder="පුරවන්න" required></td>
            `;
            table.appendChild(newRow);
        }
    </script>
    <script>
        function addRowToExternalTable(tableId) {
            const table = document.getElementById(tableId).getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input type="text" name="bname[]" placeholder="නම" required></td>
            <td><input type="date" name="bgetDate[]" placeholder="දිනය" required></td>
            <td><input type="text" name="bprice[]" placeholder="ලබාගත් මුදල" required></td>
            <td><input type="date" name="bfinDate[]" placeholder="ගෙවා අවසන් කලයුතු දිනය" required></td>
    `;
    table.appendChild(newRow);
}

    </script>
</body>
</html>
