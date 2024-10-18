<?php

namespace App\Tests\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Services\Common\Client\HomeAppAlertClientInterface;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\Transaction\MultiExec;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OutOfBoundsAlertHandlerTest extends TestCase
{
    private const REDIS_KEY = 'out_of_bounds_alert_%s';

    public function test_function_does_not_continue_if_redis_key_is_found()
    {

    }

    public function test_setting_redis_key()
    {

    }

    public function test_no_alert_is_sent_if_exception_is_thrown()
    {

    }


    public function test_send_alert_is_sent_if_no_redis_key_is_found()
    {

    }

}
