<?php
// Retrieve user ID from POST data
$userId = $_POST['userId'];

// Specify the directories where the files are located
$directories = array(
    'uploads/whatsapp_images/',
    'uploads/whatsapp_docs/',
    'uploads/whatsapp_videos/',
    'uploads/compose_variables/'
);

// Iterate through each directory
foreach ($directories as $directory) {

    // Get the files matching the _preview_ pattern for the current directory
    $previewFiles = glob($directory . $userId . '_preview_*');

    // Get the files matching the _csvpreview_ pattern for the current directory
    $csvPreviewFiles = glob($directory . $userId . '_csvpreview_*');

    // Process files matching _preview_ pattern
    if ($previewFiles !== false) {
        foreach ($previewFiles as $file) {
            // Check if the file exists and is writable
            if (is_file($file) && is_writable($file)) {
                // Delete the file
                if (unlink($file)) {
                    echo "File $file deleted.<br>";
                } else {
                    echo "Error deleting file $file.<br>";
                }
            } else {
                echo "Unable to delete file $file (file not writable).<br>";
            }
        }
    }

    // Process files matching _csvpreview_ pattern
    if ($csvPreviewFiles !== false) {
        foreach ($csvPreviewFiles as $file) {
            // Check if the file exists and is writable
            if (is_file($file) && is_writable($file)) {
                // Delete the file
                if (unlink($file)) {
                    echo "File $file deleted.<br>";
                } else {
                    echo "Error deleting file $file.<br>";
                }
            } else {
                echo "Unable to delete file $file (file not writable).<br>";
            }
        }
    }
}

?>

