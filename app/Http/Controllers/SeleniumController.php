<?php

namespace App\Http\Controllers;

use App\Models\InformationRecord;
use App\Repositories\InformationRecordRepository;
use App\Services\ServiceSelenium;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    public function accessPageSave()
    {
        $page = $this->serviceSelenium->accessPage();
        $this->informationRecordRepository->store($page);

        return response()->json($page, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/read-form",
     *     tags={"Desafio"},
     *     summary="Fill the form",
     *     description="Fill in the link form",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="username", type="string", example="Renato Lucena"),
     *              @OA\Property(property="password", type="string", example="secret"),
     *              @OA\Property(property="comments", type="string", example="Sou novo dev php senior da Bionexo")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Success Fill in the link form" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function sendDataToForm(Request $request)
    {
        $params = $request->all();
        if ($params) {
            return $this->serviceSelenium->sendFormParamns($params);
        }

    }

    /**
     * @OA\Get (
     *     path="/api/download",
     *     operationId="DownloadFile",
     *     tags={"Desafio"},
     *     summary="Download file txt",
     *     description="Download file textfile.txt",
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
    public function download()
    {
        $download = $this->serviceSelenium->directLinkDownload();
        return response()->json($download, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/upload",
     *     tags={"Desafio"},
     *     summary="Upload file",
     *     description="Upload file and rename",
     *     security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Success upload file" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function uploadFile()
    {
        Storage::disk('local_s3')->move('textfile.txt', 'Teste TKS.txt');
        $upload = $this->serviceSelenium->uploadFile('Teste TKS.txt');
        return response()->json($upload, Response::HTTP_OK);
    }
}
