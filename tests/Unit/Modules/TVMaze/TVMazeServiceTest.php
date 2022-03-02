<?php

use App\Modules\TVMaze\TVMazeHttpAdapter;
use App\Modules\TVMaze\TVMazeResponseException;
use App\Modules\TVMaze\TVMazeService;
use PHPUnit\Framework\MockObject\MockObject;

class TVMazeServiceTest extends TestCase
{
    /**
     * @var TVMazeService
     */
    private $sut;
    /**
     * @var TVMazeHttpAdapter | MockObject
     */
    private $httpAdapterMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpAdapterMock = $this->getMockBuilder(TVMazeHttpAdapter::class)->disableOriginalConstructor()->getMock();
        $this->sut = new TVMazeService($this->httpAdapterMock);
    }

    /**
     * @dataProvider filterResultsToExactPhraseDataProvider()
     * @param string $phrase
     * @param array $correctOnes
     * @param array $incorrectOnes
     * @throws TVMazeResponseException
     */
    public function testItFilterResultsToExactPhraseOnly(string $phrase, array $correctOnes, array $incorrectOnes)
    {
        $results = [];
        foreach ($correctOnes as $resultHit) {
            $results[] = $resultHit->show;
        }


        $this->httpAdapterMock->method('searchTVShow')->willReturn($correctOnes + $incorrectOnes);
        $this->assertEquals(
            $this->sut->search($phrase),
            $results
        );
    }

    public function filterResultsToExactPhraseDataProvider(): array
    {
        return [
            [
                'phrase' => 'Deadwood',
                'correct' => [
                    $this->mockShowResult('Deadwood'),
                    $this->mockShowResult('UnDeadwood'),
                ],
                'incorrect' => [
                    $this->mockShowResult('Deadpool'),
                    $this->mockShowResult('Redwood Kings'),
                ],
            ],
            [
                'phrase' => 'Simpsons',
                'correct' => [
                    $this->mockShowResult('The Simpsons'),
                ],
                'incorrect' => [
                    $this->mockShowResult("Simon's cat"),
                ],
            ],
        ];
    }

    private function mockShowResult(string $showName): StdClass
    {
        $mock = new StdClass();
        $mock->show = new StdClass();
        $mock->show->name = $showName;

        return $mock;
    }
}