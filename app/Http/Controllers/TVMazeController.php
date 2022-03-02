<?php
namespace App\Http\Controllers;

use App\Modules\TVMaze\TVMazeResponseException;
use App\Modules\TVMaze\TVMazeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TVMazeController extends Controller
{

    /**
     * @var TVMazeService
     */
    private $service;

    /**
     * @param TVMazeService $service
     */
    public function __construct(TVMazeService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws TVMazeResponseException
     */
    public function search(Request $request): JsonResponse
    {
        $this->validate(request(), [
            'q' => 'required|min:1',
        ]);

        return response()->json(
            $this->service->search($request->get('q')),
            JsonResponse::HTTP_OK
        );
    }

}
