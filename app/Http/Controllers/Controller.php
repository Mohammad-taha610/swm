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
     *      @OA\Contact(
     *          email="tahaiqbal610@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )

     *
     * @OA\Tag(
     *     name="Projects",
     *     description="API Endpoints of Projects"
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
