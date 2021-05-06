<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Support\Facades\Redirect;
use Storage;
use File;
use Auth;
use PDF;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function AuthLogin(){
        
        if(Session::get('login_normal')){

            $admin_id = Session::get('admin_id');
        }else{
            $admin_id = Auth::id();
        }
            if($admin_id){
                return Redirect::to('dashboard');
            }else{
                return Redirect::to('admin')->send();
            } 
        
       
    }
    public function upload_file(){

        $filename = 'Anthony Robbins.pdf';
        $filePath = public_path('uploads/document/Đánh Thức Con Người Phi Thường Trong Bạn45.pdf');
        $fileData = File::get($filePath);
        Storage::cloud()->put($filename, $fileData);
        return 'File PDF Uploaded';

    }
    public function create_document()
    {
        Storage::cloud()->put('test.txt', 'Storage 1');
        dd('created');
    }
    public function upload_image(){
        $filename = 'Chapter4-567xdcv.jpg';
        $filePath = public_path('frontend/images/chapter/Chapter4-567xdcv.jpg');
        $fileData = File::get($filePath);
        Storage::cloud()->put($filename, $fileData);
        return 'Image Uploaded';
    }
    public function upload_video(){
        $filename = 'video_hd720.mp4';
        $filePath = public_path('frontend/images/samplevideo.mp4');
        $fileData = File::get($filePath);
        Storage::cloud()->put($filename, $fileData);
        return 'Video Uploaded';
    }

    public function download_document($path,$name){
        
        
        $contents = collect(Storage::cloud()->listContents('/', false))
        ->where('type', '=', 'file')
        ->where('path', '=', $path)
        ->first(); 
        
        $filename_download = $name;
       
    
        $rawData = Storage::cloud()->get($path);

        return response($rawData, 200)
        ->header('Content-Type', $contents['mimetype'])
        ->header('Content-Disposition', " attachment; filename=$filename_download ");
        
        return redirect()->back();
    }


    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function create_folder(){
        Storage::cloud()->makeDirectory('Storage 3');
        dd('created folder');
    }
    public function rename_folder(){

        $folderinfo = collect(Storage::cloud()->listContents('/', false))
        ->where('type', 'dir')
        ->where('name', 'document')
        ->first();

        Storage::cloud()->move($folderinfo['path'], 'Storage');
        dd('renamed folder');
    }
    public function delete_folder(){

        $folderinfo = collect(Storage::cloud()->listContents('/', false))
        ->where('type', 'dir')
        ->where('name', 'Storage')
        ->first();

        Storage::cloud()->delete($folderinfo['path']);
        dd('deleted folder');
    }

    public function list_document()
    {
        $dir = '/';
        $recursive = true; // Có lấy file trong các thư mục con không?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive))
        ->where('type', '!=' ,'dir');

        return $contents;
    }

    public function delete_document($path){

        $fileinfo = collect(Storage::cloud()->listContents('/', false))
        ->where('type', 'file')
        ->where('path', $path)
        ->first();

        Storage::cloud()->delete($fileinfo['path']);
       
        return redirect()->back();
    }

    public function read_data(){
        $this->AuthLogin();

        $dir = '/';
        $recursive = false; // Có lấy file trong các thư mục con không?

        $contents = collect(Storage::cloud()->listContents($dir, $recursive))
        ->where('type', '!=' ,'dir');

        return view('admin.document.read')->with(compact('contents'));
    }

}
