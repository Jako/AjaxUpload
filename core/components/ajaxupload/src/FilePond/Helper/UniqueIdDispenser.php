<?php

namespace TreehillStudio\AjaxUpload\FilePond\Helper;

class UniqueIdDispenser
{
    private static int $counter = 0;

    public static function dispense(): string
    {
        return md5(uniqid(self::$counter++, true));
    }
}
