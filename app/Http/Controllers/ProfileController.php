<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Traits\FileUploader;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    use FileUploader;

    public function index(){
        $profiles = Profile::latest()->get();
        return view('profiles.index', compact('profiles'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,jpg,png|max:1024',
            'disk' => 'required|in:public,local',
        ]);

        $fileData = $this->uploadFile($request->file('file'), 'profiles', $request->disk);

        Profile::create([
            'title' => $request->title,
            'original_name' => $fileData['original_name'],
            'file_path' => $fileData['path'],
            'disk' => $request->disk,
            'mime_type' => $fileData['mime_type'],
            'file_size' => $fileData['file_size'],
        ]);
        return redirect()->route('profiles.index')->with('success', 'Profile picture uploaded successfully!');
    }

    public function destroy(Profile $profile){
        $this->deleteFile($profile->file_path, $profile->disk);
        $profile->delete();
        return redirect()->route('profiles.index')->with('success', 'Profile picture deleted successfully!');
    }
}
