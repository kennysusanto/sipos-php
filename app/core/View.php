<?php

class View
{
    public static function render($path, $data = [])
    {
        extract($data);
        require $path;
    }
}
