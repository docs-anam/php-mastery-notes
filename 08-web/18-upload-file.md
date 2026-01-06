# File Upload Handling

## Overview

Handling file uploads is a common web development task that requires careful attention to security, validation, and storage. This chapter covers how to securely handle file uploads in PHP.

---

## Table of Contents

1. File Upload Basics
2. HTML Forms
3. Accessing Upload Data
4. Validation
5. Security Considerations
6. File Processing
7. Multiple File Uploads
8. Complete Examples

---

## File Upload Basics

### How File Uploads Work

```
1. User selects file via form
2. Browser sends file in request body
3. Server receives file in temporary location
4. PHP populates $_FILES array
5. Script moves file to permanent storage
6. Response sent to client
```

### Upload Form Requirements

```html
<!-- REQUIRED: enctype="multipart/form-data" -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="upload">
    <button type="submit">Upload</button>
</form>
```

---

## HTML Forms

### Single File Upload

```html
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button type="submit">Upload</button>
</form>
```

### File Type Restrictions

```html
<!-- Client-side validation only (NOT SECURE) -->
<input type="file" name="document" accept=".pdf,.doc,.docx">

<!-- Image files only -->
<input type="file" name="image" accept="image/*">

<!-- Multiple file types -->
<input type="file" name="file" accept=".pdf,image/*,.zip">
```

### Multiple Files

```html
<!-- Upload multiple files at once -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="files[]" multiple>
    <button type="submit">Upload</button>
</form>

<!-- Or separate inputs -->
<input type="file" name="file1">
<input type="file" name="file2">
<input type="file" name="file3">
```

---

## Accessing Upload Data

### $_FILES Array Structure

```php
<?php
// For input: <input type="file" name="upload">

$_FILES['upload'] = [
    'name'      => 'document.pdf',       // Original filename
    'type'      => 'application/pdf',    // MIME type (from client)
    'size'      => 2048,                 // File size in bytes
    'tmp_name'  => '/tmp/php123abc',     // Temporary location
    'error'     => UPLOAD_ERR_OK          // Upload status
];
?>
```

### Accessing Individual Fields

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    $filename = $file['name'];          // document.pdf
    $mime_type = $file['type'];         // application/pdf
    $file_size = $file['size'];         // 2048
    $tmp_path = $file['tmp_name'];      // /tmp/php123abc
    $error_code = $file['error'];       // 0 (UPLOAD_ERR_OK)
}
?>
```

### Multiple Files

```php
<?php
// For input: <input type="file" name="files[]" multiple>

if (isset($_FILES['files'])) {
    // $_FILES['files'] is array of files
    foreach ($_FILES['files']['name'] as $index => $filename) {
        $file_name = $_FILES['files']['name'][$index];
        $file_type = $_FILES['files']['type'][$index];
        $file_size = $_FILES['files']['size'][$index];
        $tmp_name = $_FILES['files']['tmp_name'][$index];
        $error_code = $_FILES['files']['error'][$index];
        
        // Process each file
    }
}
?>
```

---

## Validation

### Error Codes

```php
<?php
$errors = [
    UPLOAD_ERR_OK           => 'No error',
    UPLOAD_ERR_INI_SIZE     => 'File exceeds php.ini limit',
    UPLOAD_ERR_FORM_SIZE    => 'File exceeds form limit',
    UPLOAD_ERR_PARTIAL      => 'File only partially uploaded',
    UPLOAD_ERR_NO_FILE      => 'No file uploaded',
    UPLOAD_ERR_NO_TMP_DIR   => 'Temporary directory missing',
    UPLOAD_ERR_CANT_WRITE   => 'Cannot write file to disk',
    UPLOAD_ERR_EXTENSION    => 'Upload halted by extension'
];

if ($_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['upload']['error'];
    echo $errors[$code];
}
?>
```

### File Size Validation

```php
<?php
// Check php.ini limits
$max_size = min(
    ini_get('post_max_size'),
    ini_get('upload_max_filesize')
);

// Validate upload
if ($_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    echo 'Upload failed';
    exit;
}

if ($_FILES['upload']['size'] > 5 * 1024 * 1024) {  // 5MB
    echo 'File too large';
    exit;
}

if ($_FILES['upload']['size'] === 0) {
    echo 'Empty file';
    exit;
}
?>
```

### File Type Validation

```php
<?php
// WRONG: Trust client's MIME type
if ($_FILES['upload']['type'] === 'image/jpeg') {
    // VULNERABLE!
}

// CORRECT: Use file information functions
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

// Get actual MIME type
$mime_type = mime_content_type($_FILES['upload']['tmp_name']);
// Or use fileinfo extension
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $_FILES['upload']['tmp_name']);

if (!in_array($mime_type, $allowed_types)) {
    echo 'Invalid file type';
    exit;
}

// Additional: verify file signature
function is_valid_image($file_path) {
    $size = getimagesize($file_path);
    return $size !== false;
}

if (!is_valid_image($_FILES['upload']['tmp_name'])) {
    echo 'Invalid image file';
    exit;
}
?>
```

### Filename Validation

```php
<?php
// Sanitize filename
$filename = $_FILES['upload']['name'];

// Get file extension
$ext = pathinfo($filename, PATHINFO_EXTENSION);

// WRONG: Use original filename directly
// copy($_FILES['upload']['tmp_name'], '/uploads/' . $filename);

// CORRECT: Generate safe filename
$safe_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

// Or use basename
$basename = basename($filename);  // Strip paths
$safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '', $basename);

// Validate extension
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($ext), $allowed_ext)) {
    echo 'Invalid file extension';
    exit;
}
?>
```

---

## Security Considerations

### Preventing Vulnerabilities

```php
<?php
// 1. Store uploads outside webroot (if possible)
// Instead of: /var/www/html/uploads/
// Use: /var/uploads/

// 2. Randomize filenames
$filename = uniqid() . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

// 3. Set correct file permissions
chmod('/uploads/' . $filename, 0644);

// 4. Disable script execution in upload directory
// .htaccess (Apache):
// <FilesMatch "\.php$">
//     Deny from all
// </FilesMatch>

// 5. Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
if (strpos($mime, 'image') !== 0) {
    exit('Invalid file type');
}

// 6. Check file size
if ($_FILES['file']['size'] > 10 * 1024 * 1024) {
    exit('File too large (max 10MB)');
}

// 7. Use move_uploaded_file (important!)
if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    chmod($dest, 0644);
    // Success
} else {
    // Failed
}
?>
```

### Upload Directory Security

```apache
# .htaccess - Prevent execution
<FilesMatch "\.(php|php3|php4|php5|phtml|phar)$">
    Deny from all
</FilesMatch>

# Prevent directory listing
Options -Indexes

# Disable .htaccess in subdirectories
<Directory /var/www/uploads>
    AllowOverride None
</Directory>
```

---

## File Processing

### Moving to Permanent Location

```php
<?php
// move_uploaded_file() - MUST use this function

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    // Validate
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo 'Upload failed';
        exit;
    }
    
    // Generate safe destination
    $upload_dir = '/var/uploads/';  // Outside webroot
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $destination = $upload_dir . $filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        chmod($destination, 0644);
        echo 'File uploaded: ' . htmlspecialchars($filename);
    } else {
        echo 'Failed to move file';
    }
}
?>
```

### Processing Images

```php
<?php
// Resize and optimize images

function process_image($tmp_path, $dest_path, $max_width = 800) {
    // Verify it's a real image
    if (getimagesize($tmp_path) === false) {
        throw new Exception('Invalid image');
    }
    
    // Load image
    $image = imagecreatefromstring(file_get_contents($tmp_path));
    
    if (!$image) {
        throw new Exception('Failed to load image');
    }
    
    // Get dimensions
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Resize if needed
    if ($width > $max_width) {
        $ratio = $max_width / $width;
        $new_height = round($height * $ratio);
        
        $resized = imagecreatetruecolor($max_width, $new_height);
        imagecopyresampled($resized, $image, 0, 0, 0, 0,
                          $max_width, $new_height, $width, $height);
        
        $image = $resized;
    }
    
    // Save as JPEG with compression
    imagejpeg($image, $dest_path, 85);
    imagedestroy($image);
}

// Usage
$upload = $_FILES['photo'];
$dest = '/var/uploads/' . uniqid() . '.jpg';

try {
    process_image($upload['tmp_name'], $dest);
    echo 'Image processed';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
```

---

## Multiple File Uploads

### Processing Multiple Files

```php
<?php
// Process array of files

if (isset($_FILES['photos']) && isset($_POST['upload_multiple'])) {
    $uploaded = [];
    $errors = [];
    
    $upload_dir = '/var/uploads/';
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 5 * 1024 * 1024;  // 5MB
    
    foreach ($_FILES['photos']['name'] as $key => $filename) {
        // Check for errors
        $error = $_FILES['photos']['error'][$key];
        
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;  // Skip empty uploads
        }
        
        if ($error !== UPLOAD_ERR_OK) {
            $errors[] = "File $filename: Upload error";
            continue;
        }
        
        // Get file info
        $tmp_name = $_FILES['photos']['tmp_name'][$key];
        $size = $_FILES['photos']['size'][$key];
        $type = $_FILES['photos']['type'][$key];
        
        // Validate size
        if ($size > $max_size) {
            $errors[] = "$filename: File too large";
            continue;
        }
        
        // Validate extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $errors[] = "$filename: Invalid file type";
            continue;
        }
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_name);
        if (strpos($mime, 'image') !== 0) {
            $errors[] = "$filename: Not a valid image";
            continue;
        }
        
        // Move file
        $safe_name = uniqid() . '.' . $ext;
        $dest = $upload_dir . $safe_name;
        
        if (move_uploaded_file($tmp_name, $dest)) {
            chmod($dest, 0644);
            $uploaded[] = $safe_name;
        } else {
            $errors[] = "$filename: Failed to save";
        }
    }
}
?>
```

---

## Complete Examples

### File Upload with Validation

```php
<?php
// upload.php - Secure file upload

const UPLOAD_DIR = '/var/uploads/';
const MAX_FILE_SIZE = 5 * 1024 * 1024;  // 5MB
const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    
    // Validate existence
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'Please select a file';
    }
    // Validate error code
    elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload error: ' . $file['error'];
    }
    // Validate size
    elseif ($file['size'] > MAX_FILE_SIZE) {
        $error = 'File too large (max 5MB)';
    }
    // Validate extension
    else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXT)) {
            $error = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXT);
        }
        // Validate MIME type
        else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            
            $allowed_mimes = [
                'image/jpeg', 'image/png', 'image/gif',
                'application/pdf'
            ];
            
            if (!in_array($mime, $allowed_mimes)) {
                $error = 'File type not allowed';
            }
        }
    }
    
    // If valid, move file
    if (!$error) {
        $filename = uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destination = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            chmod($destination, 0644);
            $message = 'File uploaded successfully';
        } else {
            $error = 'Failed to move file';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>Upload File</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="document" required 
               accept=".jpg,.jpeg,.png,.gif,.pdf">
        <button type="submit">Upload</button>
    </form>
    
    <p>
        <small>
            Max file size: 5MB<br>
            Allowed types: jpg, jpeg, png, gif, pdf
        </small>
    </p>
</body>
</html>
```

---

## Configuration

### php.ini Settings

```ini
; Maximum file upload size
upload_max_filesize = 10M

; Maximum POST body size
post_max_size = 10M

; Temporary upload directory
upload_tmp_dir = "/tmp"

; Automatically delete uploaded files after script execution
max_input_vars = 1000
```

---

## See Also

- [Form POST Handling](12-form-post.md)
- [File Download](19-download-file.md)
- [Security](11-xss-cross-site-scripting.md)
