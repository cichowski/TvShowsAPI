<?php
namespace App\Library;

class PaginatedResults
{
    /**
     * @var int|null
     */
    private $perPage;
    /**
     * @var int|null
     */
    private $currentPage;
    /**
     * @var array
     */
    private $results;
    /**
     * @var string
     */
    private $errorMessage = '';
    /**
     * @var int
     */
    private $numberOfItems;
    /**
     * @var int
     */
    private $numberOfPages;

    /**
     * @param int|null $currentPage
     * @param int|null $perPage
     */
    public function __construct(?int $currentPage, ?int $perPage)
    {
        $this->currentPage = $currentPage ?? 1;
        $this->perPage = $perPage;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
        $this->numberOfItems = count($this->results);
        if ($this->perPage === null) {
            $this->perPage = $this->numberOfItems;
            $this->currentPage = 1;
            $this->numberOfPages = 1;
        } else {
            $this->numberOfPages = (int)ceil($this->numberOfItems / $this->perPage);
        }
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
        $this->numberOfItems = 0;
        $this->numberOfPages = 0;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errorMessage);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage,
            'no_pages' => $this->numberOfPages,
            'no_items' => $this->numberOfItems,
            'results' => !empty($this->results) ? array_slice($this->results, ($this->currentPage - 1) * $this->perPage, $this->perPage) : [],
            'error_message' => !empty($this->errorMessage) ? $this->errorMessage : '',
        ];
    }
}