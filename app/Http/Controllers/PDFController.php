<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    public function converterPdf()
    {
        $text = Pdf::getText(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
        $arrayOfPages = preg_split('/["\n"]/', $text);
        //dd($arrayOfPages);
        //preg_match('/(.*)(0001)(.*)/', $arrayOfPages[4], $match);
        preg_match('/(\/)(.*)/', $arrayOfPages[4], $match);

        $qtePage = intval($match[2]);
        $pages = [];

        for ($i = 1; $i < $qtePage; $i++) {
            $indexQtde = str_pad($i, 4, 0, STR_PAD_LEFT);

            //dd($indexQtde);
            $pattern = "/(.*)($indexQtde)(.*)/";
            //dd($pattern);


            if (preg_match($pattern, $arrayOfPages[$i], $match)) {
                $pages = $match[2];
                dd($match[2]);
                dd($indexQtde);

            }
        }

        dd($pages);

        // for ($i = 0; $i < sizeof($arrayOfPages); $i++) {
        //     if (preg_match('/(.*)(0001)(.*)/', $arrayOfPages[$i], $match)) {
        //        dd($match[2]);
        //     }
        // }
    }

}
