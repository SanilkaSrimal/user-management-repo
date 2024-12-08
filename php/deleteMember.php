<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "themiya";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the Member ID from the URL
if (isset($_GET['memberId'])) {
    $memberId = intval($_GET['memberId']);

    // Delete from the family table
    $deleteFamilyQuery = "DELETE FROM family_members WHERE member_id = $memberId";
    $conn->query($deleteFamilyQuery);

    
    
    $deleteLoanQuery = "DELETE FROM loan_details WHERE member_id = $memberId";
    $conn->query($deleteLoanQuery);

    $deleteBailQuery = "DELETE FROM bail_details WHERE member_id = $memberId";
    $conn->query($deleteBailQuery);

    // Delete from the members table
    $deleteMemberQuery = "DELETE FROM members WHERE member_id = $memberId";
    $conn->query($deleteMemberQuery);

    // Redirect back to the search page with a success message
    header('Location: findMember.php?message=MemberDeleted');
    exit;
} else {
    echo "Invalid member ID.";
}
?>