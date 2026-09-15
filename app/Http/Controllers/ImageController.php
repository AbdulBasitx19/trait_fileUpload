<?php

namespace App\Http\Controllers;

use App\models\Image;
use App\Traits\FileUploader;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    //
    use FileUploader;

    public function index(){
        $images = Image->latest()->get();
        return view('images.index', compact('images'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'disk' => 'required|in:public,local',
        ]);

        $fileData = $this->uploadFile(
            $request->file('file'),
            'images',
            $request->disk,
        );

        Image::create([
            'title' => $request->title,
            'original_name' => $fileData['original_name'],
            'file_path' => $fileData['path'],
            'disk' => $request->disk,
            'mime_type' => $fileData['mime_type'],
            'file_size' => $fileData['file_size'],
        ]);

        return redirect()->route('images.index')->with('success', 'Image uploaded successfully!');
    }

    public function destroy(Image $image){
        $this->deleteFile($image->file_path, $image->disk);
        $image->delete();
        return redirect()->route('images.index')->with('success', 'Image deleted successfully!');
    }
}
