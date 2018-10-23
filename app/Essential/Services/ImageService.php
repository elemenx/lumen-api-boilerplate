<?php

namespace App\Essential\Services;

use Qcloud\Cos\Exception\ServiceResponseException;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $object = null;
    private $disk = null;

    public function __construct($object, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width = $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height = $max_height;
        }
        $this->object = $object;
        $this->disk = app('filesystem')->disk('cosv5');
    }

    public function save($saveAs = '')
    {
        if (is_object($this->object)) {
            $path = $this->object->store('uploads/' . date('Ym') . '/' . date('d'), 'cosv5');
            $filePath = $this->disk->get($path);
            $img = app('image')->make($filePath);
        } elseif (is_string($this->object)) {
            $path = 'uploads/' . date('Ym') . '/' . date('d') . '/' . time() . str_random(8) . '.jpg';
            $img = app('image')->make($this->object);
        }
        if ($this->max_width > 0 && $this->max_height == 0) {
            $this->max_width = min($this->max_width, $img->width());
            $img->resize($this->max_width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->fit($this->max_width, $this->max_height);
        }
        if (!empty($saveAs) && file_exists($saveAs)) {
            file_put_contents($saveAs, $img->encode());
        }
        try {
            if ($this->disk->put($path, $img->encode())) {
                return $path;
            }
        } catch (ServiceResponseException $e) {
            return false;
        }

        return false;
    }

    public function delete()
    {
        try {
            if (!empty($this->object) && $this->disk->exists($this->object)) {
                $this->disk->delete($this->object);
                return true;
            }
        } catch (ServiceResponseException $e) {
            return false;
        }
        return false;
    }
}
