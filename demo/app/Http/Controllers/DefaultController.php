<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/27/15
 * Time: 11:11 PM
 */

namespace App\Http\Controllers;

use File;
use Kings\ImageRemoteUploader\Uploaders\Picasa;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class DefaultController extends Controller
{
    public function getIndex()
    {
        return view('default.index');
    }

    public function postDoUpload(Request $request)
    {
        set_time_limit(30 * 1000);

        $files = $request->file('files');
        $totalUploadedFiles = count($files);
        $tmpDir = public_path('upload/tmp');
        $relativePath = 'upload/picasa/' . Session::getId();
        $thisSessionDir = public_path($relativePath);
        $response['files'] = [];

        // Contain array of images for upload to picasa (after uploaded & extra from zip file)
        $imagesForUpload = [];

        if (!is_dir($thisSessionDir)) {
            File::makeDirectory($thisSessionDir, 0755);
        }

        for ($i = 0; $i < $totalUploadedFiles; $i++) {
            /** @var UploadedFile $file */
            $file = $files[$i];
            $tmpImages = [];
            if ($this->isValidImage($file)) {
                $tmpImages[] = $this->processImages($file->getRealPath(), $thisSessionDir, $file->getClientOriginalName());
            } elseif ($this->isZipFile($file)) {
                $tmpImages = $this->processZipFile($file, $tmpDir, $thisSessionDir);
            }
            $imagesForUpload = array_merge($imagesForUpload, $tmpImages);
        }


        $album = $this->albumRepo->choseAlbum(count($imagesForUpload), 'otk', $this->picasa);
        // Process upload to picasa ...
        foreach ($imagesForUpload as $image) {
            $filepath = public_path($relativePath . '/' . $image);
            $url = $this->doPicasaUpload($filepath, $album->g_id);
            if ($url) {
                $response['files'][] = [
                    'url' => $url,
                ];
            }
        }

        File::deleteDirectory($thisSessionDir);

        return Response::json($response);
    }

    protected function doPicasaUpload($filepath, $album)
    {
        try {
            $url = $this->picasa->doUpload($filepath, $album);
            return $url;
        } catch (\Exception $e) {
            \Log::debug('#1Fail upload file: ' . $filepath);
            \Log::debug($e);
            return null;
        }
    }

    /**
     * Unzip file and move images to $sessionDir
     * @param UploadedFile $file
     * @param string $tmpDir
     * @param string $sessionDir
     * @return string[]
     */
    protected function processZipFile($file, $tmpDir, $sessionDir)
    {
        $tmpDir .= '/' . uniqid('extracted-');
        $zip = new ZipArchive();
        $zip->open($file->getRealPath());
        $zip->extractTo($tmpDir);
        $images = $this->moveDirImages($tmpDir, $sessionDir);
        File::deleteDirectory($tmpDir);

        return $images;
    }

    /**
     * Move all images in directory to $sessionDir and return list of filenames
     * @param string $dir
     * @param string $sessionDir
     * @return string[]
     */
    protected function moveDirImages($dir, $sessionDir)
    {
        chdir($dir);
        $handle = opendir('.');
        $images = [];
        while (false != ($entry = readdir($handle))) {
            // Skip current & parent folder link
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            if (is_dir($entry)) {
                $images = array_merge($images, $this->moveDirImages(realpath($entry), $sessionDir));
            } elseif ($this->isValidImage(new SymfonyFile($entry))) {
                $images[] = $this->processImages(realpath($entry), $sessionDir);
            }
        }

        return $images;
    }

    /**
     * Move image to session dir & return new filename
     * @param string $path
     * @param string $sessionDir
     * @param null $originalFilename
     * @return string
     */
    protected function processImages($path, $sessionDir, $originalFilename = null)
    {
        if ($originalFilename) {
            $newFilename = $this->randomizeFilename($originalFilename);
        } else {
            $newFilename = $this->randomizeFilename($path);
        }

        $dest = $sessionDir . '/' . $newFilename;

        File::move($path, $dest);
        chmod($dest, 0755);

        return $newFilename;
    }

    /**
     * @param SymfonyFile $file
     * @return bool
     */
    protected function isValidImage($file)
    {
        $ext = strtolower($file->getMimeType());

        return in_array($ext, ['image/jpg', 'image/jpeg', 'image/png']);
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    protected function isZipFile($file)
    {
        return $file->getMimeType() == 'application/zip';
    }

    protected function randomizeFilename($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = Str::slug(pathinfo($path, PATHINFO_FILENAME));
        if ($filename > 64) {
            $filename = substr($filename, 0, 64);
        }

        return uniqid($filename . '-') . '.' . $ext;

    }
}