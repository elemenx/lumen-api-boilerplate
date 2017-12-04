<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $file = null;
    private $disk = null;

    public function __construct($file, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width =  $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height =  $max_height;
        }
        $this->file = $file;
        $this->disk = app('filesystem')->disk('public');
    }

    public function save()
    {
        if (is_object($this->file)) {
            $path = $this->file->store('uploads/'.date('Ym').'/'.date('d'), 'public');
            $filePath = $this->disk->get($path);
            $img = app('image')->make($filePath);
        } elseif (is_string($this->file)) {
            $path = 'uploads/'.date('Ym').'/'.date('d').'/'.time().str_random(8).'.jpg';
            $img = app('image')->make($this->file);
        }
        if ($this->max_width > 0 && $this->max_height == 0) {
            $img->resize($this->max_width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->fit($this->max_width, $this->max_height);
        }
        $this->disk->put($path, $img->encode());
        return $path;
    }

    public function delete()
    {
        if (!is_string($this->file) || empty($this->file) || !$this->disk->exists($this->file)) {
            return false;
        }
        $this->disk->delete($this->file);
        return true;
    }
}
