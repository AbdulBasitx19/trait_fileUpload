# 📁 Laravel File Upload System Using Traits

A clean,Laravel project demonstrating **file uploading** (Public & Private storage) using **PHP Traits** for maximum code reusability across multiple controllers.

## 🎯 Project Overview

This project showcases how **Traits** can be used to share file-handling logic across multiple controllers without code duplication. Instead of writing the same upload/delete logic in every controller, we create a single `FileUploader` trait and reuse it in:

- **DocumentController** (PDFs, DOCs)
- **ImageController** (JPG, PNG)
- **ProfileController** (Profile pictures)

Each controller handles a different entity, but all three share the **exact same file upload logic** through the trait.

## 🚀 Key Features

- ✅ **Trait-Based Architecture**: Single `FileUploader` trait reused across 3 controllers.
- ✅ **Public & Private Storage**: Files can be uploaded to either `public` (web-accessible) or `local` (secure, non-web-accessible) disk.
- ✅ **Unique Filename Generation**: Prevents file overwrites using `time()` + slugified original name.
- ✅ **Automatic Cleanup**: Deleting a record also removes the physical file from storage.
- ✅ **Code Reusability**: Upload logic written once, used 3 times (DRY principle).
- ✅ **Clean Controllers**: Controllers stay thin and focused on HTTP handling.

## 🔍 Trait vs Helper - What's the Difference?

| Aspect | Helper Function | Trait |
|--------|-----------------|-------|
| **Structure** | Standalone global function | Class-like structure |
| **Access** | Called directly: `formatSize($bytes)` | Used inside class: `$this->formatSize($bytes)` |
| **State Access** | Cannot access `$this` | Can access class properties via `$this` |
| **OOP Friendly** | ❌ No | ✅ Yes |
| **Best For** | Simple utilities (formatting, math) | Shared behavior across related classes |
| **Example** | `formatDate()`, `calculateTax()` | `FileUploader`, `SoftDeletes` |

**In this project:** We use a Trait (not a Helper) because file uploading needs access to class context and follows OOP principles.

## 🏛️ Architecture & Flow

```text
User Request
    ↓
[Controller] → Validates input, calls Trait method
    ↓
[Trait: FileUploader] → Generates unique name, saves file to disk
    ↓
[Storage] → File saved (public/ or local/)
    ↓
[Database] → Metadata saved (path, size, mime type)
```

## 📂 Project Structure

```text
app/
├── Http/Controllers/
│   ├── DocumentController.php    # uses FileUploader trait
│   ├── ImageController.php       # uses FileUploader trait
│   └── ProfileController.php     # uses FileUploader trait
│
├── Traits/
│   └── FileUploader.php          # ⭐ Main trait with upload/delete logic
│
└── Models/
    ├── Document.php
    ├── Image.php
    └── Profile.php

resources/views/
├── documents/index.blade.php
├── images/index.blade.php
└── profiles/index.blade.php

storage/app/
├── public/
│   ├── documents/                # Public documents
│   ├── images/                   # Public images
│   └── profiles/                 # Public profile pictures
└── private_documents/            # Private files (non-web accessible)
```

## 💻 Code Example - How Trait is Used

### **The Trait (Written Once)**
```php
// app/Traits/FileUploader.php
trait FileUploader
{
    public function uploadFile(UploadedFile $file, string $folder, string $disk = 'public'): array
    {
        $uniqueName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $uniqueName, $disk);
        
        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }

    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->delete($path);
    }
}
```

### **Using Trait in Controllers (3 Times)**
```php
// DocumentController.php
class DocumentController extends Controller
{
    use FileUploader; // ✅ Trait imported
    
    public function store(Request $request)
    {
        $fileData = $this->uploadFile($request->file('file'), 'documents', $request->disk);
        Document::create([...$fileData, 'title' => $request->title]);
    }
}

// ImageController.php
class ImageController extends Controller
{
    use FileUploader; // ✅ Same trait, different folder
    
    public function store(Request $request)
    {
        $fileData = $this->uploadFile($request->file('file'), 'images', $request->disk);
        Image::create([...$fileData, 'title' => $request->title]);
    }
}

// ProfileController.php
class ProfileController extends Controller
{
    use FileUploader; // ✅ Same trait again!
    
    public function store(Request $request)
    {
        $fileData = $this->uploadFile($request->file('file'), 'profiles', $request->disk);
        Profile::create([...$fileData, 'title' => $request->title]);
    }
}
```

**Result:** 60+ lines of duplicate code reduced to just 3 lines per controller! 🎉

## 🛠️ Tech Stack

- **Backend**: Laravel 11.x / 12.x, PHP 8.2+
- **Database**: MySQL
- **Frontend**: Blade Templates, Inline CSS
- **Architecture**: Trait-Based Code Reuse
- **File Handling**: Laravel Storage Facade (`public` & `local` disks)

## 📦 Installation & Setup

### **1. Clone the Repository**
```bash
git clone https://github.com/AbdulBasitx19/fileUpload_trait.git
cd fileUpload_trait
```

### **2. Install Dependencies**
```bash
composer install
```

### **3. Setup Environment**
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trait_fileUpload
DB_USERNAME=root
DB_PASSWORD=
```

### **4. Run Migrations**
```bash
php artisan migrate
```

### **5. Create Storage Symlink (For Public Files)**
```bash
php artisan storage:link
```

**If command fails on Windows**, run CMD as Administrator and use:
```bash
mklink /D public\storage storage\app\public
```

### **6. Start Development Server**
```bash
php artisan serve
```

### **7. Visit Application**
Open browser: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 🎓 Key Learning Outcomes

### **1. Why Use Traits?**
- **Code Reusability**: Write once, use everywhere
- **Maintainability**: Change logic in one place, affects all controllers
- **Clean Code**: Controllers stay focused on HTTP handling
- **DRY Principle**: No duplicate code across controllers

### **2. Public vs Private Storage**
- **Public (`storage/app/public`)**: Accessible via URL (`http://yoursite.com/storage/file.pdf`)
- **Private (`storage/app/local`)**: Not web-accessible, requires controller to serve file
- **Symlink**: `php artisan storage:link` connects `public/storage` to `storage/app/public`

### **3. Unique Filename Strategy**
```php
time() . '_' . Str::slug(originalName) . '.' . extension
// Example: 1698765432_my-resume.pdf
```
- Prevents overwrites
- Keeps original name readable
- Timestamp ensures uniqueness

### **4. Complete File Deletion**
```php
// 1. Delete physical file from storage
Storage::disk($disk)->delete($path);

// 2. Delete database record
$model->delete();
```
Ensures no orphaned files remain on server.

## 🧪 Testing the Application

1. **Upload a Document (Public)**:
   - Go to `/documents`
   - Upload a PDF with "Public" storage
   - Click "View" → File opens in new tab

2. **Upload an Image (Private)**:
   - Go to `/images`
   - Upload a JPG with "Private" storage
   - Notice: No "View" button (private files aren't web-accessible)

3. **Upload Profile Picture**:
   - Go to `/profiles`
   - Upload a profile picture
   - Test both Public and Private options

4. **Test Deletion**:
   - Click "Delete" on any file
   - Verify file disappears from UI
   - Check `storage/app/public/` folder → Physical file also deleted

## 📊 Comparison: With Trait vs Without Trait

### **❌ Without Trait (Code Duplication)**
```php
// DocumentController
public function store(Request $request) {
    $uniqueName = time() . '_' . $file->getClientOriginalName();
    $path = $file->storeAs('documents', $uniqueName, $disk);
    // ... 20 more lines of same code
}

// ImageController
public function store(Request $request) {
    $uniqueName = time() . '_' . $file->getClientOriginalName(); // DUPLICATE!
    $path = $file->storeAs('images', $uniqueName, $disk);
    // ... 20 more lines (SAME CODE!)
}

// ProfileController
public function store(Request $request) {
    $uniqueName = time() . '_' . $file->getClientOriginalName(); // DUPLICATE AGAIN!
    $path = $file->storeAs('profiles', $uniqueName, $disk);
    // ... 20 more lines (SAME CODE AGAIN!)
}
```
**Total: 60+ lines of duplicate code**

### **✅ With Trait (Code Reuse)**
```php
// DocumentController
use FileUploader;
public function store(Request $request) {
    $fileData = $this->uploadFile($request->file('file'), 'documents', $request->disk);
    // ... 5 lines
}

// ImageController
use FileUploader;
public function store(Request $request) {
    $fileData = $this->uploadFile($request->file('file'), 'images', $request->disk);
    // ... 5 lines
}

// ProfileController
use FileUploader;
public function store(Request $request) {
    $fileData = $this->uploadFile($request->file('file'), 'profiles', $request->disk);
    // ... 5 lines
}
```
**Total: 15 lines (75% reduction!)**

## 🔮 Future Enhancements

- Add AWS S3 integration
- Implement user authentication
- Add file categories and search
- Multiple file upload support
- File preview thumbnails
- Download counter tracking

## 🤝 Contributing

This is an educational project. Feel free to fork, experiment, and learn!

## 📄 License

Open-sourced for educational purposes under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Abdul Basit**  
- GitHub: [@AbdulBasitx19](https://github.com/AbdulBasitx19)

---

*Built with ❤️ using Laravel and PHP Traits - Demonstrating Clean Code & DRY Principles.*