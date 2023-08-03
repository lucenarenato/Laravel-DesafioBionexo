<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ServiceUtil
{
    public static function convertPdfTextToCsv($filename, $data)
    {
        if(!$data) return true;

            $header = array_keys($data[0]);

            $CSV = fopen($filename, 'a+');
            fputcsv($CSV, $header, ';');

            foreach ($data as $line) {
                fputcsv($CSV, $line, ';');
            }

            fclose($CSV);

        return true;
    }

}
