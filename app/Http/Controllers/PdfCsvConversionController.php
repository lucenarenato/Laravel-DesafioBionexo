<?php

namespace App\Http\Controllers;

use App\Services\RegularExpressionTreatmentService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfCsvConversionController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/convert-pdf-to-csv",
     *     operationId="readingFile",
     *     tags={"Desafio"},
     *     summary="reading file txt",
     *     description="reading file textfile.txt",
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
    public function convertPdfToCsv()
    {
      $files = RegularExpressionTreatmentService::readPdf(Storage::disk('local_s3')->path('Leitura PDF.PDF'));
      return response()->json($files, 200);
    }
}
