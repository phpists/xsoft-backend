<?php


namespace App\Http\Services;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Збереження зображень
     * @param $disk
     * @param $path
     * @param $image
     * @param null $oldFile
     * @return bool|string
     */
    public static function saveFile($disk, $path, $image, $oldFile = null): bool|string
    {
        if (!$image) {
            return false;
        }

        try {
            $imageName = time() . mt_rand(1, 9999) . '.' . $image->getClientOriginalExtension();
            $save = Storage::disk($disk)->putFileAs($path, $image, $imageName);
            if ($save) {
                if ($oldFile) {
                    Storage::disk($disk)->delete("$path/$oldFile");
                }
                return $imageName;
            }
        } catch (\Exception $e) {
            Log::error("File save error: " . $e->getMessage());
        }

        return false;
    }

    public static function removeFile($disk, $path, $image)
    {
        return Storage::disk($disk)->delete($path . '/' . $image);
    }

    /**
     * Збереження зображень з url
     * @param $disk
     * @param $path
     * @param $url
     * @return bool|string
     */
    public static function saveFileFromUrl($disk, $path, $url)
    {
        $fileContent = file_get_contents($url);
        $fileNameWithoutExtension = time() . mt_rand(1, 9999);
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $fileName = $fileNameWithoutExtension . '.' . $extension;

        Storage::disk($disk)->put($path . '/' . $fileName, $fileContent);

        return $fileName;
    }

    public static function a()
    {
        return 'some';
    }
}
