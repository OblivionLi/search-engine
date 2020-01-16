<?php

include('../config.php');

if (isset($_POST["src"])) {
    $stmt = mysqli_prepare($db,"UPDATE images SET broken = 1 WHERE imageUrl = ?");
    $stmt->bind_param('s', $_POST['src']);
    $stmt->execute();
}