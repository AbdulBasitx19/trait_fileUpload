<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document - FileUpload Trait</title>
</head>
<body style="font-family: font-family: Arial, sans-serif; margin: 0px; padding: 40px ; background-color=#f4f4f9;">
    <div style="max-width: 1000px; margin: 0 auto"></div>

    <div style="margin-bottom: 30px; display: flex; gap: 15px">
        <a href="{{ route('documents.index')}}" style="padding: 10px 20px; background-color: #11355c; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Documents</a>
        <a href="{{ route('images.index')}}" style="padding: 10px 20px; background-color:  #11355c; color:white; text-decoration:none; border-radius:5px;">Images</a>
        <a href="{{ route('profile.index')}}" style="padding: 10px 20px; background-color: #11355c; color: white; text-decoration: none; border-radius: 5px;">Profiles</a>
    </div>

    @if(session('success'))
    <p style="background-color: #d4edda , color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        {{ session('success')}}
    </p>
    @endif

    <div style="background-color: white; padding: 30px; border-radius: 8px; margin-bottom:30px;  box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color:#1f048c;">Upload Document</h2>
        <form action="{{ route('documents.store')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px">
                <label style="display: block; margin-bottom:8px; font-weight: bold;">Title:</label>
                    <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    @error('title')  <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span> @enderror
            </div>
             <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Select File (PDF, DOC, DOCX - Max 2MB):</label>
                <input type="file" name="file" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                @error('file')
                    <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

             <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Storage Type:</label>
                <div style="display: flex; gap: 30px;">
                    <label style="display: flex; align-items: center;">
                        <input type="radio" name="disk" value="public" checked style="margin-right: 8px;">
                        <strong style="color: #28a745;">Public</strong>
                    </label>
                    <label style="display: flex; align-items: center;">
                        <input type="radio" name="disk" value="local" style="margin-right: 8px;">
                        <strong style="color: #dc3545;">Private</strong>
                    </label>
                </div>
                @error('disk')
                 <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" style="background-color: #1e3fb6; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;">Upload Document</button>
        </form>
    </div>

    <!-- Documents List -->
        <div style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333;">All Documents ({{ $documents->count() }})</h2>
            @if($documents->isNotEmpty())
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #00ff6e; color: white;">
                        <th style="padding: 12px; text-align: left;">Title</th>
                        <th style="padding: 12px; text-align: left;">File Name</th>
                        <th style="padding: 12px; text-align: left;">Disk</th>
                        <th style="padding: 12px; text-align: left;">Size</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $doc)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;"> {{ $doc->title}}</td>
                            <td style="padding: 12px;"> {{ $doc->original_name}}</td>
                            <td style="padding: 12px;">
                                @if($doc->disk === 'public')
                                    <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">Public</span>
                                @else 
                                    <span style="background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">Private</span>
                                @endif
                            </td>
                            <td style="padding: 12px;">{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                            <td style="padding: 12px; text-align: center;">
                                @if($doc->disk === 'public')
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" style="background-color: #17a2b8; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px;">View</a>
                                @endif
                                <form action="{{ route('documents.destroy', $doc->id)}}"method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr> 
                    @endforeach
                </tbody>
            </table>
            @else
                <p style="color: #999; text-align: center; padding: 20px;">No documents uploaded yet.</p>
            @endif
        </div>
           
</body>
</html>