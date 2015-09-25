<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GBEntryControllerTest extends WebTestCase
{
    public function testSendentry()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sendEntry');
    }

    public function testView()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/view');
    }

}
