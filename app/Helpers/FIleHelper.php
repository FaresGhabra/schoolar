<?php

namespace App\Helpers;

class FileHelper
{
    static public function getFileFormat($filePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileFormat = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $fileFormat;
    }

    static public function checkFormat($filePath, $formats)
    {
        $format = FileHelper::getFileFormat($filePath);
        $good = false;
        foreach ($formats as $f) {
            if ($f === $format)
                $good = true;
        }
        return $good;
    }

    static public function deleteFile($path) {
        if (is_file($path)) unlink($path);
    }

    static public function deleteDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!FileHelper::deleteDir($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}