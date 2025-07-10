<?php
/*
 * Detailed Summary: Downloading Files in PHP
 *
 * 1. Purpose:
 *    - Enable users to securely download files (images, documents, etc.) from a server using a browser.
 *
 * 2. Key Steps:
 *    a. Identify and validate the file requested by the user.
 *    b. Set correct HTTP headers to prompt the browser to download the file.
 *    c. Output the file content to the browser.
 *
 * 3. Important Headers:
 *    - Content-Type: Specifies the file’s MIME type (e.g., application/pdf, image/png, application/zip).
 *    - Content-Disposition: attachment; filename="example.txt" (forces download and suggests a filename).
 *    - Content-Length: Size of the file in bytes.
 *    - Content-Description: Describes the file transfer.
 *    - Cache-Control, Pragma, Expires: Control caching and ensure the file is not cached.
 *
 * 4. Security Considerations:
 *    - Always validate and sanitize file names/paths to prevent directory traversal (e.g., using basename()).
 *    - Restrict file access to authorized users if necessary.
 *    - Never allow direct user input to specify absolute file paths.
 *    - Do not expose sensitive or system files.
 *
 * 5. Example 1: Downloading a Text File
 *    Uncomment to test:
 */
/*
$file = 'files/example.txt';
if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    flush();
    readfile($file);
    exit;
} else {
    http_response_code(404);
    echo "File not found.";
}
*/

/*
 * 6. Example 2: Downloading an Image File (e.g., PNG)
 *    Uncomment to test:
 */
/*
$image = 'images/photo.png';
if (file_exists($image)) {
    header('Content-Description: File Transfer');
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="' . basename($image) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($image));
    flush();
    readfile($image);
    exit;
} else {
    http_response_code(404);
    echo "Image not found.";
}
*/

/*
 * 7. Example 3: Downloading a File Specified by User Input (with Validation)
 *    This is the recommended way for dynamic downloads.
 *    Usage: download-file.php?file=example.txt
 */
$allowed_files = ['example.txt', 'photo.png'];
if (isset($_GET['file']) && in_array($_GET['file'], $allowed_files)) {
    $file = 'files/' . basename($_GET['file']);
    if (file_exists($file)) {
        $mime = mime_content_type($file);
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        flush();
        readfile($file);
        exit;
    } else {
        http_response_code(404);
        echo "File not found.";
    }
} else {
    // Only show this message if not already sent a file
    // (Prevents output after headers)
    if (!headers_sent()) {
        http_response_code(400);
        echo "Invalid file request.";
    }
}

/*
 * 8. Notes:
 *    - Use readfile() for small/medium files. For large files, use chunked reading to save memory.
 *    - Always validate user input and restrict downloadable files to a safe list.
 *    - Set appropriate MIME types for better browser compatibility.
 */
?>