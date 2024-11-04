<?php

namespace App\Repository\Classes;

use App\Repository\Interfaces\FileRepositoryInterface;
use App\Models\File;
use ZipArchive;
use DB;
use Str;
use Storage;

class FileRepositoryClass implements FileRepositoryInterface
{
    public function __construct(public File $modal)
    {
    }

    public function createMedia(array $data = []): File
    {
        return $this->modal->create($data);
    }

    public function getFiles(
        $selectRaw = "*",
        array $where = [],
        array $with = []
    ) {
        return $this->modal
            ->selectRaw($selectRaw)
            ->where($where)
            ->with($with)
            ->get();
    }

    public function getFile(
        $selectRaw = "*",
        array $where = [],
        array $with = []
    ) {
        return $this->modal
            ->selectRaw($selectRaw)
            ->where($where)
            ->with($with)
            ->first();
    }

    public function getFilesWithIn(
        $selectRaw = "*",
        string $whereInCol = null,
        array $whereIn = [],
        array $where = [],
        array $with = [],
        string $sortCol = "id",
        string $sortType = "desc"
    ) {
        return $this->modal
            ->selectRaw($selectRaw)
            ->where($where)
            ->whereIn($whereInCol, $whereIn)
            ->with($with)
            ->orderBy($sortCol, $sortType)
            ->get();
    }

    public function generateSlug(string $slug, $parent_id)
    {
        $folder = $this->getFile(where: [["id", $parent_id]]);
        $parent_slug = null;
        if (!blank($folder)) {
            $parent_slug = $folder->slug . "/";
        }
        $parent_slug .= Str::of($slug)
            ->slug()
            ->replace("/", "");
        return $parent_slug;
    }

    public function getZipsData($zipPath)
    {
        $fileDetails = [];
        $zip = new ZipArchive();
        if ($zip->open(storage_path($zipPath)) === true) {
            $fileDetails = $this->getZipContents($zip, "");
            $zip->close();
        }
        return $fileDetails;
    }

    public function getZipContents($zip, $folder)
    {
        $contents = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $entryInfo = $zip->statIndex($i);

            if (strpos($entry, $folder) === 0) {
                $entryName = substr($entry, strlen($folder));
                if (!$entryName) {
                    continue;
                }

                $contents[] = [
                    "name" => $entryName,
                    "isDirectory" => $entryInfo["size"] == 0,
                    "size" => $entryInfo["size"],
                    'file_content' => $zip->getFromIndex($i)
                ];

                if ($entryInfo["size"] == 0) {
                    $contents = array_merge(
                        $contents,
                        $this->getZipContents($zip, $entry)
                    );
                }
            }
        }

        return $contents;
    }

    public function saveZipsFileFolder(array $content = [], $parent_id = null)
    {
        DB::beginTransaction();
        try {
            
            if (blank($content)){ return ; }
            
            $unqiue_folder = $this->getUniqueFolderName($content[0]["name"],$parent_id);
            unset($content[0]);

                $data = [
                    "name" => $unqiue_folder,
                    "slug" => $this->generateSlug($unqiue_folder, $parent_id),
                    "is_folder" => File::FOLDER,
                    "parent_id" => $parent_id,
                ];

                $fistZip = File::create($data);
                $parentData = $fistZip;

                foreach ($content as $fileData) {
                    $pathArray = explode("/", $fileData["name"]);
                    $i = 1;
                    foreach ($pathArray as $folderItem) {
                        if (!blank($folderItem)) {
                            $fcFirstArray = [
                                "name" => $folderItem,
                                "parent_id" => $parentData->id,
                            ];
                           $file_slug = $this->generateSlug(
                                    $folderItem,
                                    $parentData->id
                                );
                            $fcSeconArray = [
                                "name" => $folderItem,
                                "slug" => $file_slug,
                                "is_folder" => File::FOLDER,
                                "parent_id" => $parentData->id,
                            ];

                            //if this is file
                           $isFile = !$fileData["isDirectory"] && count($pathArray) == $i;
                            if ($isFile) {
                                $fileNameArray = explode(".", $folderItem);

                                $fcSeconArray["size"] = $fileData["size"];
                                $fcSeconArray["is_folder"] = File::FILE;
                                $fcSeconArray["type"] = end($fileNameArray);
                                $fcFirstArray["is_folder"] = File::FILE;
                                
                                if($fileData['file_content'])
                                { 
                                 
                                  $storage_file ='/zips-files/'.time().$folderItem;
                                  Storage::put($storage_file,$fileData['file_content']);
                                  $fcFirstArray["path"] = $storage_file;
                                  
                                }
                            }

                            $parentData = File::firstOrCreate(
                                $fcFirstArray,
                                $fcSeconArray
                            );
                        }
                        $i++;
                    }
                    $parentData = $fistZip;
                }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }
    
   public function getUniqueFolderName($folderName,$parent_id)
   {
       $fileExist = true;
        while ($fileExist)
        {
            $fileExist = File::where("parent_id", $parent_id)
                ->where("name", $folderName)
                ->exists();
            if ($fileExist)
            {
                $folderName .= "-copy";
            }
        }
        return $folderName;
   }
   
}
