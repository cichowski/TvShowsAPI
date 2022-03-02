<?php
namespace App\Modules\TVMaze;

/**
 * @author Dariusz Cichowski
 */
class TVMazeService
{

    public function search(string $searchPhrase): array
    {
        return [$searchPhrase];
    }
}