<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use App\Models\Image as Model;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $object = null;
    private $disk = null;
    private $savedToModel = false;

    public function __construct($object, $savedToModel = true, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width = $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height = $max_height;
        }
        $this->object = $object;
        $this->disk = app('filesystem')->disk('public');
        $this->savedToModel = $savedToModel;
    }

    public function save()
    {
        if (is_object($this->object)) {
            $path = $this->object->store('uploads/' . date('Ym') . '/' . date('d'), 'public');
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
        $this->disk->put($path, $img->encode());
        if ($this->savedToModel) {
            return Model::create([
                'path'    => $path,
                'width'   => $img->width(),
                'height'  => $img->height(),
                'user_id' => auth_user() ? auth_user()->id : 0
            ]);
        }
        return $path;
    }

    public function delete()
    {
        if ($this->object instanceof Model) {
            if (!empty($this->object->path) && $this->disk->exists($this->object->path)) {
                $this->disk->delete($this->object->path);
            }
            $this->object->delete();
            return true;
        } elseif (is_string($this->object) && !empty($this->object) && $this->disk->exists($this->object)) {
            $this->disk->delete($this->object);
            return true;
        }
        return false;
    }
}
