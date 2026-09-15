<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Traits\FileUploader;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    //
     use FileUploader;

    public function index(){
        $documents = Document::latest()->get();
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request){
        $request->validate([
           'title' => 'required|string|max:255',
           'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
           'disk' => 'required|in:public,local',
        ]);

        $fileData = $this->uploadFile(
            $request->file('file'),
            'documents',
            $request->disk,
        );

        Document::create([
            'title' => $request->title,
            'original_name' => $fileData['original_name'],
            'file_path' => $fileData['path'],
            'disk' => $request->disk,
            'mime_type' => $fileData['mime_type'],
            'file_size' => $fileData['file_size'],
        ]);

        return redirect()->route('documents.index')->with('success', 'Document Uploaded Successfully.');
    }

    public function destroy(Document $document){
        $this->deleteFile($document->file_path, $document->disk);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully!');

    }
}
