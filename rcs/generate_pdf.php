<?php
session_start(); // start session
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('vendor/autoload.php');

// Handle the incoming PDF data
$request = json_decode(file_get_contents('php://input'), true);
$content = $request['content'];

// Create a PDF using TCPDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($content, true, 0, true, 0);

// Specify the upload folder path (change this to your actual path)
$uploadFolder = '/var/www/html/whatsapp_service/uploads/generate_pdf/'; // Replace with the actual path to your upload folder

// Specify the filename for the PDF
$filename = $_SESSION['pdf_generate_uname'] . '_onboarding.pdf';

// Combine the upload folder path and filename to create the full path
$fullPath = $uploadFolder . $filename;

// Output the PDF to the specified folder
$pdf->Output($fullPath, 'F'); // Save the PDF to the folder

// Set the appropriate headers for download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename=$filename');

// Read and output the PDF file
readfile($fullPath);

// Check if the file was successfully saved
if (file_exists($fullPath)) {
    // You can send a success response back to the client here if needed
    echo 'PDF has been generated and saved to: ' . $fullPath;
} else {
    // You can send an error response back to the client here if needed
    echo 'Failed to save the PDF to the upload folder.';
}
?>