<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


trait DriveManagement
{
    
    
    /**
     * putOnDrive
     *
     * @return bool
     */
    public function putOnDrive(): bool
    {
        return true;
    }
    
    /**
     * getMetaData
     *
     * @param  String $itemId
     * @return array
     */
    public function getMetaData($itemId): array
    {
        //get drive meta data gdrive file or directory
        //error but it work
        return Storage::cloud()->getAdapter()->getMetadata($itemId);
    }
    
    /**
     * formatSharedLink
     *
     * @param  String $path
     * @return String
     */
    public function formatSharedLink(String $path): String
    {
        return "https://drive.google.com/file/d/"
            . explode('/', $path)[1] .
            "/view?usp=sharing";
    }
    
    /**
     * listDirectory
     *
     * @param  array $dir
     * @return array
     */
    protected function listDirectory(array $dir = null): array
    {
        $nameList = [];
        // not recursive
        $idList = Storage::cloud()->directories($dir);

        foreach ($idList as $id) {
            $meta = $this->getMetaData($id);
            if ($meta['type'] == 'dir') {
                $name = $meta['name'];
                $path = $meta['path'];
            }
            $nameList[$name] = $path;
        }

        return $nameList;
    }
    
    /**
     * listFilesInDirectory
     *
     * @param  Array $dir
     * @return Array
     */
    protected function listFilesInDirectory($dir = null): Array
    {
        $nameList = [];

        // not recursive
        $idList = Storage::cloud()->files($dir);

        foreach ($idList as $id) {
            $meta = $this->getMetaData($id);
            if ($meta['type'] == 'file') {
                $name = $meta['filename'];
                $path = $meta['path'];
            }
            $nameList[$name] = $path;
        }

        return $nameList;
    }
    
    /**
     * getDirectoryId
     *
     * @param  String $name
     * @param  Array $dir
     * @return String
     */
    public function getDirectoryId(string $name, string $dir = null): string
    {
        try {
            $needle = $this->listDirectory($dir)[$name];
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
        return $needle;
    }
    
    /**
     * getFileId
     *
     * @param  String $filename
     * @param  Array $dir
     * @return String
     */
    public function getFileId(String $filename, array $dir = null): String
    {
        return $this->listFilesInDirectory($dir)[$filename];
    }

    public function formatDirName(String $name): String
    {
        return Str::slug($name, "_");
    }
}
