<?php
$dir = 'uploads';

// Check if the directory doesn't exist
if (!is_dir($dir)) {
    // Create directory with permissions 0755 (read/write/execute for owner, read/execute for group and others)
    if (!mkdir($dir, 0755)) {
        die("Failed to create directory 'uploads'");
    }
    echo "Directory 'uploads' created successfully!";
} else {
    echo "Directory 'uploads' already exists.";
}
?>
