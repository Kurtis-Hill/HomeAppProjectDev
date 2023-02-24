<?php


namespace App\Common\API\Traits;

use App\Common\API\HTTPStatusCodes;
use App\Devices\Controller\GetDeviceController;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait HomeAppAPITrait
{
    public const NO_RESPONSE_MESSAGE = 'No Response Message';

    public const REQUEST_SUCCESSFUL = 'Request Successful';

    public const REQUEST_ACCEPTED = 'Request Accepted';

    public const REQUEST_ACCEPTED_SUCCESS_CREATED = 'Request Accepted Successfully Created';

    public const REQUEST_PARTIALLY_ACCEPTED = 'Request Accepted Only Partial Response Sent';

    public const NOTHING_FOUND = 'Nothing Found';

    public const SERVER_ERROR_TRY_AGAIN = 'Server Error Please Try Again';

    public const NOT_AUTHORIZED_TO_BE_HERE = 'You Are Not Authorised To Be Here';

    public const BAD_REQUEST_NO_DATA_RETURNED = 'Bad Request No Data Returned';

    // 20x Successfull
    public function sendSuccessfulJsonResponse(array $data = [], $title = self::REQUEST_SUCCESSFUL): JsonResponse
    {
        if (!empty($data)) {
            return $this->returnJsonResponse(
                [
                    'title' => $title,
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_OK
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => $title,
                'payload' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_OK
        );
    }

    public function sendSuccessfulUpdateJsonResponse(array $data = [], string $title = self::REQUEST_SUCCESSFUL): JsonResponse
    {
        if (!empty($data)) {
            return $this->returnJsonResponse(
                [
                    'title' => $title,
                    'payload' => $data
                ],
                Response::HTTP_ACCEPTED
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => $title,
                'payload' => self::NO_RESPONSE_MESSAGE
            ],
            Response::HTTP_ACCEPTED
        );
    }

    // 20x Successful
    public function sendSuccessfulResponse(string $data = null): Response
    {
        if ($data !== null) {
            return $this->returnResponse(
                $data,
                HTTPStatusCodes::HTTP_OK
            );
        }

        return $this->returnResponse(
            'Request Successful',
            HTTPStatusCodes::HTTP_OK
        );
    }

    public function sendSuccessfulUpdatedResponse(string $data = null): Response
    {
        if ($data !== null) {
            return $this->returnResponse(
                $data,
                HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY
            );
        }

        return $this->returnResponse(
            'Request Successful',
            HTTPStatusCodes::HTTP_OK
        );
    }

    public function sendCreatedResourceJsonResponse(
        array $data = [],
        string $title = self::REQUEST_ACCEPTED_SUCCESS_CREATED
    ): JsonResponse {
        if (!empty($data)) {
            return $this->returnJsonResponse(
                [
                    'title' => $title,
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_CREATED
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => $title,
                'payload' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_CREATED
        );
    }

    public function sendPartialContentJsonResponse(array $errors = [], array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return $this->returnJsonResponse(
                [
                    'title' => self::REQUEST_PARTIALLY_ACCEPTED,
                    'errors' => $errors,
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => self::REQUEST_PARTIALLY_ACCEPTED,
                'payload' => self::NO_RESPONSE_MESSAGE,
                'errors' => $errors,
            ],
            HTTPStatusCodes::HTTP_PARTIAL_CONTENT
        );
    }

    public function sendMultiStatusJsonResponse(array $errors = [], array $data = [], string $title = 'Part of the request was accepted'): JsonResponse
    {
        return $this->returnJsonResponse(
            [
                'title' => $title,
                'payload' => $data,
                'errors' => $errors
            ],
            HTTPStatusCodes::HTTP_MULTI_STATUS_CONTENT
        );
    }

    public function sendPartialContentResponse(string $data = null): Response
    {
        if ($data !== null) {
            return $this->returnResponse(
                $data,
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => self::REQUEST_PARTIALLY_ACCEPTED,
                'payload' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_PARTIAL_CONTENT
        );
    }

    // 40x Client Error Response
    public function sendBadRequestJsonResponse(array $errors = [], string $title = GetDeviceController::BAD_REQUEST_NO_DATA_RETURNED): JsonResponse
    {
        if (!empty($errors)) {
            return $this->returnJsonResponse(
                [
                    'title' => $title,
                    'errors' => $errors,
                ],
                HTTPStatusCodes::HTTP_BAD_REQUEST
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => $title,
                'payload' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_BAD_REQUEST
        );
    }

    public function sendNotFoundResponse(array $errors = []): JsonResponse
    {
        if (!empty($errors)) {
            return $this->returnJsonResponse(
                [
                    'title' => self::NOTHING_FOUND,
                    'errors' => $errors
                ],
                HTTPStatusCodes::HTTP_NOT_FOUND
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => self::NOTHING_FOUND,
                'errors' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_NOT_FOUND
        );
    }

    // 50x Server Error Response
    public function sendInternalServerErrorJsonResponse(array $data = [], string $title = self::SERVER_ERROR_TRY_AGAIN): JsonResponse
    {
        if (!empty($data)) {
            return $this->returnJsonResponse(
                [
                    'title' => $title,
                    'errors' => $data
                ],
                HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => self::SERVER_ERROR_TRY_AGAIN,
                'errors' => self::NO_RESPONSE_MESSAGE
            ],
            HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function sendForbiddenAccessJsonResponse(array $errors = []): JsonResponse
    {
        if (!empty($errors)) {
            return $this->returnJsonResponse(
                [
                    'title' => self::NOT_AUTHORIZED_TO_BE_HERE,
                    'errors' => $errors,
                ],
                HTTPStatusCodes::HTTP_FORBIDDEN
            );
        }

        return $this->returnJsonResponse(
            [
                'title' => self::NOT_AUTHORIZED_TO_BE_HERE,
                'errors' => [self::NO_RESPONSE_MESSAGE]
            ],
            HTTPStatusCodes::HTTP_FORBIDDEN
        );
    }

    private function returnResponse(string $data, int $statusCode): Response
    {
        return new Response($data, $statusCode);
    }


    private function returnJsonResponse(array $data, int $statusCode): JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }
    /**
     * @throws ExceptionInterface
     */
    #[ArrayShape(["mixed"])]
    public function normalizeResponse(mixed $data): array
    {
        $normalizer = [new ObjectNormalizer()];
        $normalizer = new Serializer($normalizer);

        return $normalizer->normalize($data);
    }

    public function deserializeRequest(string|array $data, ?string $class = null, ?string $format = null, array $extraContexts = []): mixed
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return (new Serializer(
            $normalizers,
            $encoders)
        )->deserialize(
            $data,
            $class,
            $format,
            $extraContexts
        );
    }
}
