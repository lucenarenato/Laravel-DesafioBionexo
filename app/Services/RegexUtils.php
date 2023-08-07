<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\RegexRepository;

class RegexUtils
{
    public static function fetchValues($text)
    {
        try {
            //dd($text);
            // remover quebra de linhas / trasnforma em array
            $arrayOfPages = preg_split('/["\n"]/', $text);
            $string1 = '/1 \- Registro ANS/';
            $string13 = '/13 \- Número da Guia no Prestador/';
            //dd($text, $arrayOfPages);
            //busca exata em cada pagina
            if (preg_match($string1, $text)) {
                // paginas sem procedimentos
                return RegexRepository::searchTextOnPageWithoutProcedures($arrayOfPages);
            } elseif (preg_match($string13, $text)) {
                // paginas com procedimentos
                return RegexRepository::searchTextOnPageWithProcedures($arrayOfPages);
            } else {
                return [];
            }

        } catch (Exception $error) {
            Log::error($error->getMessage());
            Log::error($error->getTraceAsString());
            return $error->getMessage();
        }
    }
}
