<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Global API",
 *    version="1.0.0",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      description="Swagger OpenApi",
 *      @OA\License(
 *          name="Bionexo",
 *          url="https://bionexo.com/"
 *      )
 * )
 *
 *
 * @OA\Tag(
 *     name="Projects",
 *     description="API Endpoints of Projects"
 * )
 *
 * @OA\PathItem(
 *     path="/modules/"
 * )
 *
 * @OA\Response(response="Unauthorized", description="If no token...")
 *
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
