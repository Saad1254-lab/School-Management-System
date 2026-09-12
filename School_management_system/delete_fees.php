<?php
include 'db.php'; // Include the database connection

// Get the fee ID from the URL
$fee_id = isset($_GET['id']) ? $_GET['id'] : '';

// Check if fee ID is provided
if ($fee_id) {
    // Prepare SQL to fetch student_id based on fee_id
    $select_query = "SELECT student_id FROM fees WHERE fee_id = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $fee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the fee record exists
    if ($result->num_rows > 0) {
        // Fetch the student_id
        $row = $result->fetch_assoc();
        $student_id = $row['student_id'];

        // Now, proceed with deleting the fee record
        $delete_query = "DELETE FROM fees WHERE fee_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $fee_id);

        // Execute the deletion
        if ($stmt->execute()) {
            echo "<p class='text-center'>Fee record deleted successfully.</p>";
        } else {
            echo "<p class='text-center'>Error deleting the fee record.</p>";
        }

        // Redirect back to the student’s fee records page
        header("Location: view_ledger.php?id=" . $student_id); // Redirect using student_id
        exit();
    } else {
        echo "<p class='text-center'>No fee record found with this ID.</p>";
    }
} else {
    echo "<p class='text-center'>Invalid fee ID.</p>";
}
?>
