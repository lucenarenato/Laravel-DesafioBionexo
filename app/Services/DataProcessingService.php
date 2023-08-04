<?php

namespace App\Services;

use Exception;
use Smalot\PdfParser\Parser;
use App\Services\PdfToCsvService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Services\RegexUtils;

class DataProcessingService
{
    protected $pdfToCsvService;

    public function __construct(PdfToCsvService $pdfToCsvService)
    {
        $this->pdfToCsvService = $pdfToCsvService;
    }

    public function readPdf($path)
    {
        try {
            $parser = new Parser();
            $document = $parser->parseFile($path);
            $pages = $document->getPages();

            $tempCsvPath = Storage::disk('local_s3')->path('temp/LeituraPDF.csv');

            foreach ($pages as $page) {
                $text = RegexUtils::fetchValues($page->getText());

                // Ao invés de abrir e fechar o arquivo CSV a cada página, acumule os dados e converta ao final
                $this->pdfToCsvService->accumulateDataForConversion($text);
            }

            // Converta os dados acumulados para CSV
            $this->pdfToCsvService->convertAccumulatedDataToCsv($tempCsvPath);

            return Storage::disk('local_s3')->files('temp');
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error($error);
            throw $e;
        }
    }

    public static function readPdfOut($path)
    {
        try {
            Storage::disk('local_s3')->delete(Storage::disk('local_s3')->allFiles('out'));
            $parser = new Parser();
            $document = $parser->parseFile($path);
            $pages = $document->getPages();
            $loop = 0;

            foreach ($pages as $page) {
                $loop++;
                // buscar valores via expressão regular
                $text = RegexUtils::fetchValues($page->getText());
                PdfToCsvService::receiveDataGenerateCSV(Storage::disk('local_s3')->path('out/out.xls'), $text);
            }
            return Storage::disk('local_s3')->files('out');

        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error($error);
            throw $e;
        }

    }
}
