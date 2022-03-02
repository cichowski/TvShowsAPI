<?php

use App\Library\PaginatedResults;

class PaginatedResultsTest extends TestCase
{
    private const PER_PAGE = 10;

    /**
     * @var PaginatedResults
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new PaginatedResults(1, self::PER_PAGE);
    }

    public function testItHasErrorsOnlyWhenErrorMessageIsSet()
    {
        $this->assertFalse($this->sut->hasErrors());

        $this->sut->setErrorMessage('errors');
        $this->assertTrue($this->sut->hasErrors());
        $this->assertNotEmpty($this->sut->toArray()['error_message']);
    }

    public function testItReturnOnlyPerPageNumberOfResults()
    {
        $results = array_fill(0, self::PER_PAGE * 2, 'test result');
        $this->sut->setResults($results);
        $this->assertCount(self::PER_PAGE, $this->sut->toArray()['results']);
    }
}