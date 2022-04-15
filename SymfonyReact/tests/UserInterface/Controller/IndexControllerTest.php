<?php

namespace UserInterface\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    /**
     * @dataProvider variousRoutesDataProvider
     */
    public function test_various_routes_return_correct_response(): void
    {

    }

    public function variousRoutesDataProvider(): Generator
    {
        yield [
            'index',
        ];
        yield [
            'cards',
        ];
        yield [
            'navbar',
        ];
        yield [
            'sensors',
        ];
        yield [
            'devices',
        ];
    }
}
