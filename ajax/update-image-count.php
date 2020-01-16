<?php

include('../config.php');

if (isset($_POST['imageUrl'])) {
    $stmt = mysqli_prepare($db,"UPDATE images SET clicks = clicks + 1 WHERE imageUrl = ?");
    $stmt->bind_param('s', $_POST['imageUrl']);
    $stmt->execute();
} else {
    echo "No image url passed to page.";
}