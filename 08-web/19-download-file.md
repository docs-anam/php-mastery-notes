# File Download Handling

## Overview

File downloads allow users to retrieve files from your server. This chapter covers how to properly implement file downloads with appropriate headers, streaming, and security considerations.

---

## Table of Contents

1. Download Basics
2. HTTP Headers for Downloads
3. Simple File Downloads
4. Streaming Downloads
5. Range Requests
6. Security Considerations
7. Download Resumption
8. Complete Examples

---

## Download Basics

### HTTP Response Structure

```
HTTP Headers:
  Content-Type: application/pdf
  Content-Length: 1024000
  Content-Disposition: attachment; filename="document.pdf"
  Cache-Control: no-cache

[Blank Line]

File Content (Binary)
```

### Download Flow

```
1. User clicks download link
2. Server identifies file
3. Server sets appropriate headers
4. Server sends file contents
5. Browser receives file
6. Browser saves to Downloads folder
```

---

## HTTP Headers for Downloads

### Content-Type Header

```php
<?php
// Different file types

// PDF
header('Content-Type: application/pdf');

// Images
header('Content-Type: image/jpeg');
header('Content-Type: image/png');
header('Content-Type: image/gif');

// Documents
header('Content-Type: application/msword');  // .doc
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');  // .docx
header('Content-Type: application/vnd.ms-excel');  // .xls

// Archives
header('Content-Type: application/zip');
header('Content-Type: application/x-rar-compressed');

// Video/Audio
header('Content-Type: video/mp4');
header('Content-Type: audio/mpeg');

// Text
header('Content-Type: text/plain; charset=UTF-8');

// Generic binary
header('Content-Type: application/octet-stream');
?>
```

### Content-Disposition Header

```php
<?php
// Inline - display in browser
header('Content-Disposition: inline; filename="image.jpg"');

// Attachment - save to disk
header('Content-Disposition: attachment; filename="document.pdf"');

// With encoding
header('Content-Disposition: attachment; filename*=UTF-8\'\'document.pdf');
?>
```

### Content-Length Header

```php
<?php
// Let browser know file size
$file = '/path/to/file.pdf';
$size = filesize($file);

header('Content-Length: ' . $size);

// Browser can:
// - Calculate download time
// - Show progress bar
// - Enable resume functionality
?>
```

### Cache-Control Headers

```php
<?php
// Don't cache downloads
header('Cache-Control: no-cache, no-store, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// For large files that shouldn't change
header('Cache-Control: public, max-age=86400');

// For dynamic content
header('Cache-Control: private, max-age=0');
?>
```

---

## Simple File Downloads

### Basic Download

```php
<?php
// download.php - Simple file download

$file = '/secure/path/to/document.pdf';

// Security: Validate file path
$base_dir = '/var/downloads/';
$requested_file = realpath($file);

if ($requested_file === false || strpos($requested_file, $base_dir) !== 0) {
    http_response_code(403);
    exit('Access denied');
}

// Check file exists
if (!file_exists($requested_file) || !is_file($requested_file)) {
    http_response_code(404);
    exit('File not found');
}

// Send headers
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($requested_file));
header('Content-Disposition: attachment; filename="' . basename($requested_file) . '"');
header('Cache-Control: no-cache');

// Send file
readfile($requested_file);
?>
```

### Download via ID

```php
<?php
// download.php?id=123 - Download file by ID

$file_id = (int) ($_GET['id'] ?? 0);

if ($file_id <= 0) {
    http_response_code(400);
    exit('Invalid file ID');
}

// Get file from database
$db = new PDO('sqlite:files.db');
$stmt = $db->prepare('SELECT * FROM files WHERE id = ?');
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    exit('File not found');
}

// Validate file path (prevent directory traversal)
$file_path = '/var/uploads/' . $file['filename'];
$real_path = realpath($file_path);

if ($real_path === false || strpos($real_path, '/var/uploads/') !== 0) {
    http_response_code(403);
    exit('Invalid file');
}

// Check file exists
if (!file_exists($real_path)) {
    http_response_code(404);
    exit('File not found');
}

// Send headers
$filename = $file['original_name'] ?? basename($real_path);
header('Content-Type: ' . $file['mime_type']);
header('Content-Length: ' . filesize($real_path));
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Send file
readfile($real_path);
?>
```

---

## Streaming Downloads

### readfile() for Small Files

```php
<?php
// readfile() - Simple approach for small files
// Reads entire file into memory then outputs

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="document.pdf"');

readfile($file);
?>
```

### fopen/fread for Large Files

```php
<?php
// fopen/fread - Better for large files
// Streams in chunks to avoid memory issues

$file = '/path/to/large_file.zip';

header('Content-Type: application/zip');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="archive.zip"');
header('Cache-Control: no-cache');

$fp = fopen($file, 'rb');

if ($fp === false) {
    http_response_code(500);
    exit('Error opening file');
}

// Stream file in 8KB chunks
while (!feof($fp)) {
    echo fread($fp, 8192);
    flush();
}

fclose($fp);
?>
```

### Built-in PHP Server Optimization

```php
<?php
// Let PHP send file efficiently
// Best for all file sizes

$file = '/path/to/file.zip';

// Send file
header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="' . basename($file) . '"');

// Use readfile() or fopen() depending on size
if (filesize($file) > 10 * 1024 * 1024) {
    // Large file - stream
    $fp = fopen($file, 'rb');
    while (!feof($fp)) {
        echo fread($fp, 8192);
    }
    fclose($fp);
} else {
    // Small file - simple
    readfile($file);
}
?>
```

---

## Range Requests

### HTTP Range Support

```php
<?php
// Support resumable downloads
// Allows download to resume from last position

$file = '/path/to/file.zip';

if (!file_exists($file)) {
    http_response_code(404);
    exit('File not found');
}

$file_size = filesize($file);
$start = 0;
$end = $file_size - 1;

// Check for range request
if (isset($_SERVER['HTTP_RANGE'])) {
    if (preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
        $start = intval($matches[1]);
        $end = $matches[2] !== '' ? intval($matches[2]) : $file_size - 1;
        
        // Validate range
        if ($start > $end || $start >= $file_size || $end >= $file_size) {
            http_response_code(416);
            header('Content-Range: bytes */' . $file_size);
            exit;
        }
        
        // Partial content
        http_response_code(206);
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $file_size);
    }
}

// Set headers
header('Content-Type: application/octet-stream');
header('Content-Length: ' . ($end - $start + 1));
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Accept-Ranges: bytes');

// Send requested range
$fp = fopen($file, 'rb');
fseek($fp, $start);

$bytes_remaining = $end - $start + 1;
while ($bytes_remaining > 0) {
    $bytes_to_read = min(8192, $bytes_remaining);
    echo fread($fp, $bytes_to_read);
    $bytes_remaining -= $bytes_to_read;
}

fclose($fp);
?>
```

---

## Security Considerations

### Path Traversal Prevention

```php
<?php
// VULNERABLE
$file = '/uploads/' . $_GET['file'];
readfile($file);
// Attacker can use: ?file=../../etc/passwd

// SECURE
$file_id = (int) $_GET['id'];
$db = new PDO('sqlite:files.db');
$stmt = $db->prepare('SELECT path FROM files WHERE id = ?');
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    exit('File not found');
}

// Validate path
$base = '/var/uploads/';
$real_path = realpath($file['path']);

if ($real_path === false || strpos($real_path, $base) !== 0) {
    exit('Invalid file');
}
?>
```

### Access Control

```php
<?php
// Verify user has permission to download

session_start();

$file_id = (int) $_GET['id'];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Authentication required');
}

// Get file and check ownership
$db = new PDO('sqlite:files.db');
$stmt = $db->prepare('SELECT * FROM files WHERE id = ? AND user_id = ?');
$stmt->execute([$file_id, $_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(403);
    exit('Access denied');
}

// Safe to download
$real_path = realpath('/var/uploads/' . $file['filename']);
readfile($real_path);
?>
```

### Filename Sanitization

```php
<?php
// Sanitize filename in headers

$filename = $_GET['name'] ?? 'download';

// Remove directory separators
$filename = basename($filename);

// Remove special characters
$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

// Limit length
$filename = substr($filename, 0, 255);

// Add extension if missing
if (!pathinfo($filename, PATHINFO_EXTENSION)) {
    $filename .= '.bin';
}

header('Content-Disposition: attachment; filename="' . $filename . '"');
?>
```

---

## Complete Examples

### Download Manager

```php
<?php
// download.php - Complete download system

const UPLOAD_DIR = '/var/uploads/';
const DB_PATH = 'sqlite:files.db';

session_start();

// Validate input
$file_id = (int) ($_GET['id'] ?? 0);

if ($file_id <= 0) {
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Invalid file ID');
}

// Get file from database
$db = new PDO(DB_PATH);
$stmt = $db->prepare('SELECT * FROM files WHERE id = ?');
$stmt->execute([$file_id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    header('Content-Type: text/plain');
    exit('File not found');
}

// Verify ownership
if (!isset($_SESSION['user_id']) || $file['user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit('Access denied');
}

// Validate file path
$file_path = UPLOAD_DIR . $file['filename'];
$real_path = realpath($file_path);

if ($real_path === false || strpos($real_path, UPLOAD_DIR) !== 0) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit('Invalid file path');
}

// Verify file exists
if (!file_exists($real_path) || !is_file($real_path)) {
    http_response_code(404);
    header('Content-Type: text/plain');
    exit('File not found on disk');
}

// Log download
$stmt = $db->prepare('INSERT INTO file_downloads (file_id, user_id, ip, timestamp) VALUES (?, ?, ?, NOW())');
$stmt->execute([$file_id, $_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);

// Get file info
$file_size = filesize($real_path);
$original_name = $file['original_name'] ?? basename($real_path);

// Send headers
header('Content-Type: ' . ($file['mime_type'] ?? 'application/octet-stream'));
header('Content-Length: ' . $file_size);
header('Content-Disposition: attachment; filename="' . addslashes($original_name) . '"');
header('Cache-Control: no-cache, no-store');
header('Pragma: no-cache');
header('Accept-Ranges: bytes');

// Stream file
$fp = fopen($real_path, 'rb');

if ($fp === false) {
    http_response_code(500);
    exit('Error reading file');
}

while (!feof($fp)) {
    echo fread($fp, 8192);
    flush();
}

fclose($fp);
?>
```

### Temporary Download Link

```php
<?php
// generate_download_link.php - Create temporary download links

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

$file_id = (int) ($_POST['file_id'] ?? 0);

if ($file_id <= 0) {
    exit('Invalid file');
}

// Generate token
$token = bin2hex(random_bytes(32));
$expiry = time() + 3600;  // 1 hour

// Store in database
$db = new PDO('sqlite:files.db');
$stmt = $db->prepare('
    INSERT INTO download_tokens (file_id, token, expiry, user_id)
    VALUES (?, ?, ?, ?)
');
$stmt->execute([$file_id, $token, $expiry, $_SESSION['user_id']]);

// Generate link
$link = '/download.php?token=' . $token;

echo json_encode(['link' => $link]);
?>

<?php
// download.php - Download using token

$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    exit('No token provided');
}

// Get download token
$db = new PDO('sqlite:files.db');
$stmt = $db->prepare('
    SELECT f.* FROM files f
    JOIN download_tokens dt ON f.id = dt.file_id
    WHERE dt.token = ? AND dt.expiry > ?
');
$stmt->execute([$token, time()]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    exit('Token invalid or expired');
}

// Proceed with download
$real_path = realpath('/var/uploads/' . $file['filename']);

if ($real_path === false || !file_exists($real_path)) {
    http_response_code(404);
    exit('File not found');
}

// Send file
header('Content-Type: ' . $file['mime_type']);
header('Content-Length: ' . filesize($real_path));
header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');

readfile($real_path);

// Delete token after use
$stmt = $db->prepare('DELETE FROM download_tokens WHERE token = ?');
$stmt->execute([$token]);
?>
```

---

## Key Functions

```php
<?php
// File reading functions

readfile($file)             // Read and output entire file
fopen($file, 'rb')          // Open file for reading
fread($fp, $bytes)          // Read bytes from file
fseek($fp, $offset)         // Seek to position
fclose($fp)                 // Close file
filesize($file)             // Get file size
file_exists($file)          // Check if file exists

// Header functions

header('Content-Type: ...')           // Set MIME type
header('Content-Length: ...')         // Set file size
header('Content-Disposition: ...')    // Set download behavior
header('Accept-Ranges: bytes')        // Enable range requests
http_response_code(206)               // Partial content
?>
```

---

## See Also

- [File Upload](18-upload-file.md)
- [HTTP Headers](13-header.md)
- [Response Codes](14-response-code.md)
- [Security](11-xss-cross-site-scripting.md)
