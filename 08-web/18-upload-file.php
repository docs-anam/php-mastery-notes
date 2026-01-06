<?php
/*
Summary: File Upload in PHP

1. HTML Form:
    - Use <form> with method="POST" and enctype="multipart/form-data".
    - Include an <input type="file" name="file"> element.
    - Example:
*/
?>
<!-- HTML Form Example -->
<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="file">
    <button type="submit" name="submit">Upload</button>
</form>
<?php
/*
2. PHP Handling:
    - Uploaded files are stored in the $_FILES superglobal.
    - $_FILES['file']['name']: Original filename.
    - $_FILES['file']['tmp_name']: Temporary file path on server.
    - $_FILES['file']['size']: File size in bytes.
    - $_FILES['file']['error']: Error code (0 means no error).
    - $_FILES['file']['type']: MIME type.
    - Example:
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileErr  = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    /*
    3. Moving Uploaded File:
        - Use move_uploaded_file($_FILES['file']['tmp_name'], $destination) to move the file from temporary to permanent location.
        - Example:
    */
    $destination = __DIR__ . '/uploads/' . basename($fileName);

    /*
    4. Security Considerations:
        - Always validate file type and size.
        - Rename files to avoid overwriting and security issues.
        - Store files outside the web root if possible.
        - Check for upload errors using $_FILES['file']['error'].
        - Example:
    */
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($fileErr === 0) {
        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            // Rename file to avoid conflicts
            $newFileName = uniqid('upload_', true) . '_' . basename($fileName);
            $destination = __DIR__ . '/uploads/' . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                echo "File uploaded successfully as $newFileName";
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Invalid file type or size.";
        }
    } else {
        echo "File upload error: $fileErr";
    }
}
/*
5. Example Workflow:
    - Check if form is submitted and file is uploaded.
    - Validate file (type, size, etc.).
    - Move file to desired directory.
    - Handle errors and provide user feedback.
    - (See code above.)

6. Common Pitfalls:
    - Not setting enctype="multipart/form-data" in form.
    - Not checking for upload errors.
    - Allowing unsafe file types (e.g., .php files).

7. Configuration:
    - upload_max_filesize and post_max_size in php.ini control upload limits.
    - file_uploads must be enabled in php.ini.
*/
?>