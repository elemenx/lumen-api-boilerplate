<?php

namespace App\Essential\Observers;

use App\Essential\Models\File;

class FileObserver
{
    public function deleting(File $file)
    {
        app('filesystem')->disk('cosv5')->delete($file->path);
    }
}
