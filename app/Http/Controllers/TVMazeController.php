<?php
namespace App\Http\Controllers;

use App\Library\PaginatedResults;
use App\Modules\TVMaze\TVMazeResponseException;
use App\Modules\TVMaze\TVMazeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class TVMazeController extends Controller
{
    /**
     * TV Maze cache each query result for 60 minutes anyway.
     */
    private const CACHE_TIME_IN_MINUTES = 60;

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
     * @link [GET] /
     * @SWG\Get(
     *     path="/",
     *     summary="Get search result",
     *     @SWG\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search phrase",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="p",
     *         in="query",
     *         description="Number of requested result page",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="s",
     *         in="query",
     *         description="Page size, number of hits on single response page",
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of search hits",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation errors"
     *     ),
     *     @SWG\Response(
     *         response=424,
     *         description="Error when getting response from third party API"
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function search(Request $request): JsonResponse
    {
        $this->validate(request(), [
            'q' => 'required',
            'p' => 'integer|min:1',
            's' => 'integer|min:1',
        ]);

        $results = $this->getResponseData(
            $request->get('q'),
            $request->get('p'),
            $request->get('s')
        );

        return response()->json(
            $results->toArray(),
            $results->hasErrors() ? JsonResponse::HTTP_FAILED_DEPENDENCY : JsonResponse::HTTP_OK
        );
    }

    /**
     * @param string $searchPhrase
     * @param int|null $page
     * @param int|null $pageSize
     * @return PaginatedResults
     */
    private function getResponseData(string $searchPhrase, ?int $page = null, ?int $pageSize = null): PaginatedResults
    {
        $perPage = $pageSize !== null ? $pageSize : ($page !== null ? config('tvshows.resultsPerPage') : null);
        $responseData = new PaginatedResults($page, $perPage);

        if (Cache::has($searchPhrase)) {
            $responseData->setResults(Cache::get($searchPhrase));
        } else {
            try {
                $showList = $this->service->search($searchPhrase);
                $responseData->setResults($showList);
                Cache::put($searchPhrase, $showList, self::CACHE_TIME_IN_MINUTES * 60);
            } catch (TVMazeResponseException $e) {
                $responseData->setErrorMessage($e->getMessage());
            }
        }

        return $responseData;
    }
}
