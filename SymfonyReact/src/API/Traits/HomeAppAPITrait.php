<?php


namespace App\API\Traits;

use App\API\HTTPStatusCodes;
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
    // 20x Successfull
    public function sendSuccessfulJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Successful',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_OK
            );
        }

        return new JsonResponse(
            [
                'title' => 'Request Successful',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_OK
        );
    }

    public function sendSuccessfulUpdateJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Successful',
                    'payload' => $data
                ],
                Response::HTTP_ACCEPTED
            );
        }

        return new JsonResponse(
            [
                'title' => 'Request Successful',
                'payload' => 'No Response Message'
            ],
            Response::HTTP_ACCEPTED
        );
    }

    // 20x Successful
    public function sendSuccessfulResponse(string $data = null): Response
    {
        if ($data !== null) {
            return new Response(
                $data,
                HTTPStatusCodes::HTTP_OK
            );
        }

        return new Response('Request Successful', HTTPStatusCodes::HTTP_OK);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendSuccessfulUpdatedResponse(string $data = null): Response
    {
        if ($data !== null) {
            return new Response(
                $data,
                HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY
            );
        }

        return new Response('Request Successful', HTTPStatusCodes::HTTP_OK);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendCreatedResourceJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Successfully Created',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_CREATED
            );
        }

        return new JsonResponse(
            [
                'title' => 'Request Accepted Successfully Created',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_CREATED
        );
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendPartialContentJsonResponse(array $errors = [], array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Only Partial Response Sent',
                    'errors' => $errors,
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT
            );
        }

        return new JsonResponse(
            [
                'title' => 'Request Accepted Only Partial Response Sent',
                'payload' => 'No Response Message',
                'errors' => $errors,
            ],
            HTTPStatusCodes::HTTP_PARTIAL_CONTENT
        );
    }

    public function sendMultiStatusJsonResponse(array $errors = [], array $data = [], string $title = 'Part of the request was accepted'): JsonResponse
    {
        return new JsonResponse(
            [
                'title' => $title,
                'payload' => $data,
                'errors' => $errors
            ],
            HTTPStatusCodes::HTTP_MULTI_STATUS_CONTENT
        );
    }

    /**
     * @param string|null $data
     * @return Response
     */
    public function sendPartialContentResponse(string $data = null): Response
    {
        if ($data !== null) {
            return new Response(
                $data,
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT
            );
        }

        return new JsonResponse(
            [
                'title' => 'Request Accepted Only Partial Response Sent',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_PARTIAL_CONTENT
        );
    }

    // 40x Client Error Response
    public function sendBadRequestJsonResponse(array $errors = [], string $title = 'Bad Request No Data Returned'): JsonResponse
    {
        if (!empty($errors)) {
            return new JsonResponse(
                [
                    'title' => $title,
                    'errors' => $errors,
                ],
                HTTPStatusCodes::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(['title' => $title, 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_BAD_REQUEST);
    }

    /**
     * @param array $errors
     * @return JsonResponse
     */
    public function sendNotFoundResponse(array $errors = []): JsonResponse
    {
        if (!empty($errors)) {
            return new JsonResponse(
                [
                    'title' => 'Nothing Found',
                    'errors' => $errors
                ],
                HTTPStatusCodes::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            [
                'title' => 'Nothing Found',
                'errors' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_NOT_FOUND
        );
    }

    // 50x Server Error Response

    public function sendInternalServerErrorJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Server Error Please Try Again',
                    'errors' => $data
                ],
                HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'title' => 'Server Error Please Try Again',
                'errors' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function sendForbiddenAccessJsonResponse(array $errors = []): JsonResponse
    {
        if (!empty($errors)) {
            return new JsonResponse(
                [
                    'title' => 'You Are Not Authorised To Be Here',
                    'errors' => $errors,
                ],
                HTTPStatusCodes::HTTP_FORBIDDEN
            );
        }

        return new JsonResponse(
            [
                'title' => 'You Are Not Authorised To Be Here',
                'errors' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_FORBIDDEN
        );
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

    public function deserializeRequest(string $data, string $class, string $format, array $extraContexts = []): mixed
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($data, $class, $format, $extraContexts);
    }
}
