<?php
// admin/upload_helper.php

class UploadHelper {
    private static $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'];
    private static $max_size = 5 * 1024 * 1024; // 5MB

    /**
     * Handles the upload of a single file.
     * @param array $file The element from $_FILES
     * @param string $target_dir The directory to save the file
     * @param string $prefix Prefix for the filename
     * @return string|false Returns the relative path to the uploaded file from the root, or false on failure.
     */
    public static function handle($file, $target_dir = 'uploads/', $prefix = 'img_') {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check size
        if ($file['size'] > self::$max_size) {
            return false;
        }

        // Check type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::$allowed_types)) {
            return false;
        }

        // Create directory if not exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Generate safe name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!$ext) {
            // Map common mimes if extension is missing
            $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/x-icon' => 'ico'];
            $ext = $map[$mime] ?? 'bin';
        }
        
        $filename = $prefix . bin2hex(random_bytes(8)) . '.' . $ext;
        $target_path = rtrim($target_dir, '/') . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Return path relative to the site root (assuming admin is one level deep)
            return 'admin/' . $target_path;
        }

        return false;
    }
}
?>
