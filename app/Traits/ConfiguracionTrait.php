<?php

namespace App\Traits;

trait ConfiguracionTrait
{
    public static function configuracion()
    {
        $jsonString = file_get_contents(base_path('config.json'));
        return json_decode($jsonString, true);
    }
}
