<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use App\Repository\Classes\FileRepositoryClass;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/tt',function (){
  return view('tt');
})->name('tt');


Route::get('work',function (){
 
  $fruits = [1,2,3,2,2];
  $max = 0;
  $breack = [];
  for($i=0;$i < count($fruits);$i++)
  {
     for($k=$i;$k < count($fruits);$k++)
      {
         if(
           count(array_unique($breack)) == 2 &&
           !in_array($fruits[$k],$breack,true)
          )
         {
           $max = max(count($breack),$max);
           $breack = [];
            break;
         }
         array_push($breack,$fruits[$k]);
      }
  }
  

  

});


Route::get('test',function(FileRepositoryClass $fileRepo){
      $zipFiles = '/app/zips/59NESzpPfgoQiGwmwqf0ycEF94lzVE8ZQOpXofhQ.zip';
      $fileContent = $fileRepo->getZipsData($zipFiles);
      if(!blank($fileContent)){
        $folderData = explode('/',$fileContent[0]['name']);
         array_unshift($fileContent,[
                'name' => $folderData[0],
                'isDirectory' => true,
                'size' => 0
          ]);
      }
      $fileRepo->saveZipsFileFolder($fileContent,7);
});



Route::get('/my-files/{folder?}',[FileController::class,'index'])
       ->middleware(['auth','verified'])
       ->where('folder', '(.*)')
       ->name('home');

Route::get('/get-breadcrum/{path?}',[FileController::class,'getBradcrum'])
       ->where('path', '(.*)')
       ->name('getBreadcrum');
       
Route::post('/upload-media',[FileController::class,'uploadMedia'])
       ->name('uploadMedia');
       
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('add-foldee',[FileController::class,'addFolder'])->name('addFolder');
    Route::get('get-files/{folder?}',[FileController::class,'getFiles'])
    ->where('folder','(.*)')
    ->name('getFiles');
    
});


Route::get('qr',function (){
  
$data = QrCode::size(512)
->format('png')
->generate('https://api.whatsapp.com/send/?phone=+917600479301&text=
Name: 
address:
Liter: 
Phone:
');
        
return response($data)
    ->header('Content-Type', 'image/png')
    ->header('Content-Disposition', 'attachment; filename="filename.png"');



});



require __DIR__.'/auth.php';