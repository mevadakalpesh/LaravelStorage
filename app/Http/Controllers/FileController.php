<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Classes\FileRepositoryClass;
use Inertia\Inertia;
use App\Models\File;
use Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\FileRequest;

class FileController extends Controller
{
    public function __construct(
      public FileRepositoryClass $fileRepo
    )
    {
    }
    
    public function index(Request $request,$folder = null)
    {  
      
      $folder = $this->fileRepo->getFile(
        where:[
           ['slug',$folder]
        ]
      );
      
      $breadcumData = $this->getBreadcumData($folder);
      
       return Inertia::render('FileManage',[
         'folder' => $folder,
         'breadcumData' => $breadcumData
       ]);
    }
    
    public function addFolder(FileRequest $request) :RedirectResponse
    { 
      
      $parent_id = !blank($request->parent_id) ? $request->parent_id : null;
      $data = [
        'name' => $request->name,
        'slug' => $this->fileRepo->generateSlug($request->name,$parent_id),
        'is_folder' => File::FOLDER,
        'parent_id' => $parent_id
      ];
      
      $this->fileRepo->createMedia($data);
      return Redirect::back();
      
    }
    
    
    public function getFiles(Request $request,$folder = null)
    { 
      $folder = !blank($folder) ? $folder : null;
      
      $files =  $this->fileRepo->getFiles(
          where:[
            ['parent_id',$folder]
          ]
        );
      return response()->json(['message' => $folder,'result' => $files]);
    } 
    
    
    
    public function getBreadcumData($folder)
    {
        $bredcrum = [];
        if(!blank($folder)){
          $slug = isset($folder->slug) ? $folder->slug : $folder;
          while(!empty($slug))
          {
            $bredcrum[] .= $slug;
            $index = strripos($slug,'/');
            $slug = substr($slug,0,$index);
          }
        }
        $breadcrumData = $this->fileRepo->getFilesWithIn(
          whereIn:$bredcrum,
          whereInCol:'slug',
          sortCol:'created_at',
          sortType:'asc'
        );
        return $breadcrumData;
    }
    
    public function getBradcrum(Request $request,$path = null)
    {
       $bradcrumdata = $this->getBreadcumData($path);
      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'result' => $bradcrumdata
      ]);
    }
    
    public function uploadMedia(Request $request)
    {
      $file = $request->file('file');
      $fileName = $file->hashName();
      $file->store('/zips');
      $zipFiles = '/app/zips/'.$fileName;
      $fileContent = $this->fileRepo->getZipsData($zipFiles);
      $parent_id = $request->parent_id;
      
      
      if(!blank($fileContent))
      {
        $folderData = explode('/',$fileContent[0]['name']);
         array_unshift($fileContent,[
                'name' => $folderData[0],
                'isDirectory' => true,
                'size' => 0
          ]);
      }
      
      $this->fileRepo->saveZipsFileFolder($fileContent,$parent_id);
      
    }
}
