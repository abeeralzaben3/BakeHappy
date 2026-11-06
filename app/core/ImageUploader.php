<?php
class ImageUploader
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    private const MAX_SIZE = 2 * 1024 * 1024; // 2MB

    // Returns [savedPath|null, errorMessage|null]. No file chosen = [null, null].
    public static function upload(array $file, string $folder): array
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return [null, 'Image must be 2MB or less.'];
        }
        if ($error !== UPLOAD_ERR_OK) {
            return [null, 'Upload failed. Please try again.'];
        }
        if ($file['size'] > self::MAX_SIZE) {
            return [null, 'Image must be 2MB or less.'];
        }

        // Check the REAL file type, not the name the user gave it
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            return [null, 'Only JPG, PNG or WEBP images are allowed.'];
        }

        $name = bin2hex(random_bytes(8)) . '.' . self::ALLOWED[$mime];
        $dir  = __DIR__ . '/../../public/uploads/' . $folder . '/';

        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            return [null, 'Could not save the image.'];
        }
        return ['uploads/' . $folder . '/' . $name, null];
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            $file = __DIR__ . '/../../public/' . $path;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    // Returns a usable image URL for a path, a full URL, or nothing
public static function url(?string $path): string
{
    if (!$path) {
        return BASE_URL . '/assets/img/placeholder.png';
    }
    if (preg_match('#^https?://#', $path)) {
        return $path;
    }
    return BASE_URL . '/' . $path;
}
}