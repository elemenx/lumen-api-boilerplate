<?php

namespace App\Essential\Services;

use App\Essential\Models\File;
use App\Essential\Models\Staff;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileService
{
    public static function bind($ids, $object)
    {
        if (!($user = auth_user())) {
            return false;
        }

        $data = ['object_type' => null];
        foreach (Relation::morphMap() as $type => $class) {
            if ($object instanceof $class) {
                $data['object_type'] = $type;
                break;
            }
        }
        if (empty($data['object_type'])) {
            return false;
        }
        $data['object_id'] = $object->id;

        $uploader_type = $user instanceof Staff ? 'staff' : 'user';
        File::whereIn('id', $ids)
            ->where('uploader_type', $uploader_type)
            ->where('uploader_id', $user->id)
            ->update($data);

        return true;
    }
}
