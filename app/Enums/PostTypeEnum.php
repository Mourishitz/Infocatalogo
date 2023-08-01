<?php

namespace App\Enums;

enum PostTypeEnum: string
{
    case PHOTO = 'Foto';

    case VIDEO = 'Vídeo';

    case POST = 'Post';

    public static function getByIndex($index): ?PostTypeEnum
    {
        $cases = self::cases();
        if (array_key_exists($index, $cases)) {
            return $cases[$index];
        }
        return null;
    }
}
