<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Star Wars Movies",
     *      description="Star Wars Movies Documentation",
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )

     *
     * @OA\Tag(
     *     name="Star Wars",
     *     description="API Endpoints of Star Wars"
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
