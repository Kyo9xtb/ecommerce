<?php

namespace App\Helper;

class CommandDataHelper
{
    public static function extract(array $fields, object $command): array
    {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $command->$field ?? null;
        }
        return $data;
    }
}