<?php


namespace App\Services;

use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileService
{
    private $bucket;

    public function __construct()
    {
        $this->bucket = env('AWS_BUCKET');
    }

    public function uploadFile(UploadedFile $file, $directory = 'blog_title_images')
    {
        $fileName = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $directory . '/' . $fileName;
        Storage::disk('s3')->put($filePath, file_get_contents($file, 'public'));
        $imageUrl = 'https://sws-portfolio.s3.ap-northeast-2.amazonaws.com/' . $filePath;
        return $imageUrl;
    }


    public function uploadBase64Image($base64Image, $extension, $directory = 'blog_images')
    {
        $imageContents = base64_decode($base64Image);

        $fileName = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

        $filePath = $directory . '/' . $fileName;

        try {
            $uploaded = Storage::disk('s3')->put($filePath, $imageContents);

            if (!$uploaded) {
                throw new \Exception("Failed to upload the image to S3.");
            }
            $imageUrl = 'https://sws-portfolio.s3.ap-northeast-2.amazonaws.com/' . $filePath;
            return $imageUrl;
        } catch (S3Exception $e) {
            file_put_contents('php://stderr', 'S3 Error: ' . $e->getMessage() . "\n");
            return null;
        } catch (\Exception $e) {
            file_put_contents('php://stderr', 'General Error: ' . $e->getMessage() . "\n");
            return null;
        }
    }






    public function deleteFile($imageUrl)
    {
        if (!$imageUrl) return;
        $baseUrl = 'https://sws-portfolio.s3.ap-northeast-2.amazonaws.com/';
        $key = str_replace($baseUrl, '', $imageUrl);
        Storage::disk('s3')->delete($key);
    }

    public function isValidImageUrl($imageUrl)
    {
        try {
            $headers = get_headers($imageUrl, 1);
            return strpos($headers[0], '200 OK') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
