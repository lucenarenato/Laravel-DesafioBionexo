<?php

namespace App\Http\Controllers;

use App\Models\InformationRecord;
use App\Repositories\InformationRecordRepository;
use App\Services\ServiceSelenium;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SeleniumController extends Controller
{
    private $serviceSelenium;
    protected $informationRecordRepository;

    public function __construct(ServiceSelenium $serviceSelenium, InformationRecordRepository $informationRecordRepository)
    {
        $this->serviceSelenium = $serviceSelenium;
        $this->informationRecordRepository = $informationRecordRepository;
    }

    /**
     * @OA\Get (
     *     path="/api/access-page",
     *     operationId="AccessPage",
     *     tags={"Desafio"},
     *     summary="Access Page Table",
     *     description="Access Page Table",
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
    public function acessPageSave()
    {
        $page = $this->serviceSelenium->accessPage();
        $this->informationRecordRepository->store($page);

        return response()->json($page, Response::HTTP_OK);
    }
}
