<?php

namespace App\Http\Controllers;

use App\Services\DataProcessingService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfCsvConversionController extends Controller
{
    protected $dataProcessingService;

    public function __construct(DataProcessingService $dataProcessingService)
    {
        $this->dataProcessingService = $dataProcessingService;
    }

    public function convertPdfToCsv()
    {
        $this->dataProcessingService->readPdfOut(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
        $files = $this->dataProcessingService->readPdf(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
        return response()->json($files, 200);
    }

    /**
     * @OA\Get (
     *     path="/api/convert-pdf-to-xls",
     *     operationId="readingFileXLS",
     *     tags={"Desafio"},
     *     summary="convert pdf to xls",
     *     description="convert pdf to xls",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *          response=200,
     *          description="Successful",
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="User not authorized. Wrong login or password.",
     *          @OA\JsonContent()
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Operation return error messages",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="Sorry. Please try again"))
     *     ),
     * )
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertPdfToXLS()
    {
        $files = DataProcessingService::readPdfOut(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
        return response()->json($files, 200);
    }
}
