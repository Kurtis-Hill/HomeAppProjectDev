<?php

namespace App\Tests\Devices\AMQP\Consumers;

use App\Devices\AMQP\Consumers\DeviceRequestConsumer;
use App\Sensors\AMQP\Consumers\UploadCurrentReadingSensorDataConsumer;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeviceRequestConsumerTest extends KernelTestCase
{
    private DeviceRequestConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private ?RelayRepository $relayRepository;

    private ContainerInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->sut = $this->diContainer->get(DeviceRequestConsumer  ::class);
        $this->relayRepository = $this->diContainer->get(RelayRepository::class);

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_successful_request_returns_correctly(): void
    {
//        dd('as');
//        $httpClient = new TraceableHttpClient();
//        $response = new MockResponse([], ['http_code' => 200]);
//        $httpClient = new MockHttpClient($response);
//
////        $httpClient->expects($this->once())
////            ->method('getStatusCode')
////            ->willReturn(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
//
////        $this->diContainer->set('test.Symfony\Contracts\HttpClient\HttpClientInterface', $httpClient);
////        $this->diContainer->set(TraceableHttpClient::class, $httpClient);
//        $this->diContainer->set(TraceableHttpClient::class, new TraceableHttpClient($httpClient));
////        $this->diContainer->set(HttpClientInterface::class, new TraceableHttpClient($httpClient));
//
//        /** @var Relay $relay */
//        $relay = $this->relayRepository->findAll()[0];
//
//        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
//            GenericRelay::NAME,
//            !$relay->getCurrentReading(),
//        );
//
//        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
//            $relay->getSensorID(),
//            $boolCurrentReadingUpdateRequestDTO,
//        );
//
//        $amqpMessage = $this->createMock(AMQPMessage::class);
//        $amqpMessage->expects($this->once())
//            ->method('getBody')
//            ->willReturn(serialize($requestSensorCurrentReadingUpdateMessageDTO));
//
////        dd('s');
//
//        $this->sut->execute($amqpMessage);
    }
}
