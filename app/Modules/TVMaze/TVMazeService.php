<?php
namespace App\Modules\TVMaze;

class TVMazeService
{
    /**
     * @var TVMazeHttpAdapter
     */
    protected $tvMazeHttpAdapter;

    public function __construct(TVMazeHttpAdapter $tvMazeHttpAdapter)
    {
        $this->tvMazeHttpAdapter = $tvMazeHttpAdapter;
    }

    /**
     * @param string $searchPhrase
     * @return array
     * @throws TVMazeResponseException
     */
    public function search(string $searchPhrase): array
    {
        return $this->filterResultsToExactPhraseOnly(
            $this->tvMazeHttpAdapter->searchTVShow($searchPhrase),
            $searchPhrase
        );
    }

    private function filterResultsToExactPhraseOnly(array $results, string $searchPhrase): array
    {
        $finalResults = [];
        foreach ($results as $singleResult) {
            if (stripos($singleResult->show->name, $searchPhrase) !== false) {
                $finalResults[] = $singleResult->show;
            }
        }

        return $finalResults;
    }
}