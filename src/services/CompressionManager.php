<?php
namespace App\Services;

class CompressionManager
{
    public static function compressImage(string $filePath, string $mimeType): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filePath);
                if ($image) {
                    imagejpeg($image, $filePath, 75);
                    imagedestroy($image);
                }
                break;
            case 'image/png':
                $image = imagecreatefrompng($filePath);
                if ($image) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, $filePath, 6);
                    imagedestroy($image);
                }
                break;
            case 'image/gif':
                $image = imagecreatefromgif($filePath);
                if ($image) {
                    imagegif($image, $filePath);
                    imagedestroy($image);
                }
                break;
        }
    }

    public static function compressVideo(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $ffmpeg = trim(shell_exec('where ffmpeg 2>$null'));
        if ($ffmpeg === '') {
            return;
        }

        $tempPath = $filePath . '.tmp.mp4';
        $command = escapeshellarg($ffmpeg) . ' -i ' . escapeshellarg($filePath) . ' -vcodec libx264 -crf 28 -preset veryfast -acodec aac -strict -2 ' . escapeshellarg($tempPath);
        shell_exec($command);

        if (file_exists($tempPath)) {
            rename($tempPath, $filePath);
        }
    }
}
