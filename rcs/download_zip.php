<?php
/*session_start(); // start session
error_reporting(E_ALL);
ini_set('display_errors', 1);
$baseFilename = $_SESSION['pdf_generate_uname'] . '_onboarding.zip';
$files = array(
    'uploads/generate_pdf/' . $_SESSION['pdf_generate_uname'] . '_onboarding.pdf',  // Replace with the paths to your files
    'uploads/whatsapp_docs/' . $_SESSION["proof_doc_names"],
    'uploads/whatsapp_images/' . $_SESSION["profile_images"],
);

$zipname = $_SESSION['pdf_generate_uname'] . '_zip.zip';

$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
foreach ($files as $file) {
     $zip->addFile($file, basename($file));
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename=' . $baseFilename);
header('Content-Length: ' . filesize($zipname));
header("Pragma: no-cache"); 
header("Expires: 0"); 
readfile($zipname);*/

session_start(); // start session
error_reporting(E_ALL);
ini_set('display_errors', 1);
$filename = $_SESSION['pdf_generate_uname'] . '_zip.zip';

$files = array(
    'uploads/generate_pdf/' . $_SESSION['pdf_generate_uname'] . '_onboarding.pdf',  // Replace with the paths to your files
    'uploads/whatsapp_docs/' . $_SESSION["proof_doc_names"],
    'uploads/whatsapp_images/' . $_SESSION["profile_images"],
    // Add more files here
);

$zipname = 'uploads/zip_files/' . $_SESSION['pdf_generate_uname'] . '_onboarding.zip'; // Set the zip file name
if (is_file($zipname))
    unlink($zipname);

$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
foreach ($files as $file) {
    $zip->addFile($file, basename($file));
}
$zip->close();

header('Content-Type: application/zip');
header('Content-disposition: attachment; filename=' . $filename);
header('Content-Length: ' . filesize($zipname));
readfile($zipname);
?>