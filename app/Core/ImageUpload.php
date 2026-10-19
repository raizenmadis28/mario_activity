<?php
namespace App\Core;

class ImageUpload
{
    private const MAX_SIZE = 5 * 1024 * 1024;
    private const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp'];
    private const QUALITY  = 82;

    /**
     * Handle upload → converts to WebP.
     * @return string Relative path (e.g. 'uploads/products/20261019_abc.webp') or '' on failure
     */
    public static function handle(array $file, string $subfolder): string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return '';
        if ($file['size'] > self::MAX_SIZE) return '';
        if (!@getimagesize($file['tmp_name'])) return '';

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED, true)) return '';

        $img = @imagecreatefromstring(file_get_contents($file['tmp_name']));
        if ($img === false) return '';

        // Preserve transparency
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $dir = __DIR__ . '/../../uploads/' . $subfolder;
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.webp';
        $fullPath = $dir . '/' . $filename;

        if (!imagewebp($img, $fullPath, self::QUALITY)) {
            imagedestroy($img);
            return '';
        }
        imagedestroy($img);

        return 'uploads/' . $subfolder . '/' . $filename;
    }

    public static function delete(?string $relPath): void
    {
        if (empty($relPath)) return;
        $full = __DIR__ . '/../../' . ltrim($relPath, '/');
        if (is_file($full)) @unlink($full);
    }
}