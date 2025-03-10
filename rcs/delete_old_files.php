<?php
// Array of directories where your files are located
$directories = array(
    'uploads/compose_upload_files/',
    'uploads/pj_report_file/',
    'uploads/compose_variables/',
    'uploads/whatsapp_images/',
    'uploads/whatsapp_docs/',
    'uploads/whatsapp_videos/'
);

// Get current time
$currentTime = time();

// Calculate timestamp for one month ago
$oneMonthAgo = strtotime('-1 month', $currentTime);

site_log_generate("Delete old files using cron" . $_SESSION['watsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
// Loop through each directory
foreach ($directories as $directory) {
    // Loop through files in the directory
    $files = scandir($directory);
    foreach ($files as $file) {
        // Skip . and .. directories
        if ($file == '.' || $file == '..') {
            continue;
        }
        // Get file's last modification time
        $filePath = $directory . $file;
        $fileModificationTime = filemtime($filePath);
        // Check if file is older than one month
        if ($fileModificationTime < $oneMonthAgo) {
            // Delete the file
            unlink($filePath);
            site_log_generate("Deleted cron using File name - Deleted :" . $filePath . date("Y-m-d H:i:s"));
            // echo "Deleted: $filePath\n";
        }
    }
}

site_log_generate("All Files are deleted successfully ! " . date("Y-m-d H:i:s"));
?>
