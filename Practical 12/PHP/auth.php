<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function require_admin()
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_field()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return '<input type="hidden" name="csrf_token" value="' .
        h($_SESSION['csrf_token']) . '">';
}

function check_csrf_token()
{
    $submitted_token = $_POST['csrf_token'] ?? '';
    $saved_token = $_SESSION['csrf_token'] ?? '';

    if (!is_string($saved_token) || $saved_token === '' || !is_string($submitted_token) || !hash_equals($saved_token, $submitted_token)) {
        http_response_code(403);
        exit('The form expired or was invalid. Go back, refresh the page, and try again.');
    }
}

function upload_poster($file)
{
    if (
        !isset($file['error'], $file['tmp_name'], $file['size']) ||
        is_array($file['error']) ||
        !is_string($file['tmp_name']) ||
        !is_numeric($file['size'])
    ) {
        return ['filename' => null, 'error' => 'The uploaded file information is invalid.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['filename' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['filename' => null, 'error' => 'The upload did not complete. Please choose the file again.'];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['filename' => null, 'error' => 'The selected file was not uploaded correctly.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['filename' => null, 'error' => 'The poster must be 2 MB or smaller.'];
    }

    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->file($file['tmp_name']);
    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed_types[$mime_type])) {
        return ['filename' => null, 'error' => 'Choose a JPG, JPEG, PNG, or WebP image.'];
    }

    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false || $image_info['mime'] !== $mime_type) {
        return ['filename' => null, 'error' => 'The selected file is not a valid image.'];
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed_types[$mime_type];
    $destination = __DIR__ . '/uploads/posters/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['filename' => null, 'error' => 'The poster could not be saved. Check the uploads/posters folder permissions.'];
    }

    return ['filename' => $filename, 'error' => null];
}

function delete_poster_file($filename)
{
    if ($filename === null || $filename === '') {
        return;
    }

    $safe_filename = basename($filename);
    $path = __DIR__ . '/uploads/posters/' . $safe_filename;

    if (is_file($path)) {
        if (!unlink($path)) {
            error_log('Could not delete poster file: ' . $path);
        }
    }
}
