<?php
include('../connect.php'); 

$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$priority = mysqli_real_escape_string($conn, $_POST['priority']);
$date = mysqli_real_escape_string($conn, $_POST['date']);

// File upload
$evidence_paths = [];
$upload_dir = "uploads/";

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

foreach ($_FILES['evidence']['tmp_name'] as $key => $tmp_name) {
    $error = $_FILES['evidence']['error'][$key];

    if ($error !== UPLOAD_ERR_OK) {
        echo "File upload error: " . $_FILES['evidence']['name'][$key] . " (Code: $error)";
        exit;
    }

    $original_name = basename($_FILES['evidence']['name'][$key]);
    $file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $original_name); 
    $file_type = $_FILES['evidence']['type'][$key];
    $file_size = $_FILES['evidence']['size'][$key];

    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file_type, $allowed_types)) {
        echo "Invalid file type: $file_name";
        exit;
    }

    if ($file_size > 5 * 1024 * 1024) {
        echo "File too large: $file_name";
        exit;
    }

    $unique_name = time() . '_' . $file_name;
    $destination = $upload_dir . $unique_name;

    if (move_uploaded_file($tmp_name, $destination)) {
        $evidence_paths[] = $destination;
    } else {
        echo "Failed to move uploaded file: $file_name";
        exit;
    }
}

$evidence_json = mysqli_real_escape_string($conn, json_encode($evidence_paths));


$sql = "INSERT INTO incidents (title, description, category, priority, incident_date, evidence_files)
        VALUES ('$title', '$description', '$category', '$priority', '$date', '$evidence_json')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Incident submitted successfully'); window.location.href='users.php';</script>";
} else {
    echo "Database Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>