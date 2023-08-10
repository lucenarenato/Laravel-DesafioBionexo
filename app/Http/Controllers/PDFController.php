<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\PdfDocument;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class PDFController extends Controller
{
    public function converterPdf()
    {
        $text = Pdf::getText(Storage::disk('local_s3')->path('Leitura PDF.PDF'));

        $arrayOfPages = preg_split('/["\n"]/', $text);
        //dd($arrayOfPages);
        $pages = [];
        preg_match('/(\/)(.*)/', $arrayOfPages[4], $match);

        $qtePage = intval($match[2]);

        for ($i = 1; $i < $qtePage; $i++) {
            $indexQtde = str_pad($i, 4, 0, STR_PAD_LEFT);
            $pattern = "/(.)($indexQtde)(.)/";
            //dump($pattern);

            foreach ($arrayOfPages as $page) {
                if (preg_match($pattern, $page, $match)) {
                    $pages[] = $match[0];
                }
            }
        }

        dd($pages);

        // for ($i = 0; $i < sizeof($arrayOfPages); $i++) {
        //     if (preg_match('/(.*)(0001)(.*)/', $arrayOfPages[$i], $match)) {
        //        dd($match[2]);
        //     }
        // }
    }

    public function teste()
    {
        $text = Pdf::getText(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
        $arrayOfPages = preg_split('/["\n"]/', $text);


        preg_match('/(\/)(.*)/', $arrayOfPages[4], $match);

        $qtePage = intval($match[2]);
        $pages = [];

        for ($i = 1; $i < $qtePage; $i++) {
            $indexQtde = str_pad($i, 4, 0, STR_PAD_LEFT);

            $pattern = "/(.*)($indexQtde)(.*)/";


            if (preg_match($pattern, $arrayOfPages[$i], $match)) {
                $pages = $match[2];

            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pdfmysql",
     *     tags={"Desafio"},
     *     summary="pdfmysql file",
     *     description="pdfmysql",
     *     security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Success pdfmysql" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function pdfMysql()
    {
        $results = PdfDocument::search('Operadora')
            ->paginate(20);
        return response()->json($results, Response::HTTP_OK);
    }

}
