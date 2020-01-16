<?php

include('../config.php');

if (isset($_POST['linkId'])) {
    $stmt = mysqli_prepare($db,"UPDATE sites SET clicks = clicks + 1 WHERE id = ?");
    $stmt->bind_param('i', $_POST['linkId']);
    $stmt->execute();
} else {
    echo "No Link passed to page.";
}