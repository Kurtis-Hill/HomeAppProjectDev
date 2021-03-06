<?php


namespace App\Traits\API;

use App\API\HTTPStatusCodes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HomeAppAPIResponseTrait
{
    // 20x Successfull
    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendSuccessfulJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Successful',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_OK);
        }

        return new JsonResponse(['title' => 'Request Successful', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_OK);

    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendSuccessfulUpdateJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Successful',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY);
        }

        return new JsonResponse(['title' => 'Request Successful', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY);
    }

    // 20x Successfull
    /**
     * @param $data
     * @return JsonResponse
     */
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
                    'title' => 'Request Accepted Successfully Updated',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_CREATED);
        }

        return new JsonResponse(['title' => 'Request Accepted Successfully Updated', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_CREATED);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendPartialContentJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Only Partial Response Sent',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }

        return new JsonResponse(['title' => 'Request Accepted Only Partial Response Sent', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
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
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }

        return new JsonResponse(['title' => 'Request Accepted Only Partial Response Sent', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
    }
    // 40x Client Error Response

    /**
     * @param array $errors
     * @param array|null $data
     * @return JsonResponse
     */
    public function sendBadRequestJsonResponse(array $errors = [], array $data = null): JsonResponse
    {
        if (!empty($errors)) {
            return new JsonResponse(
                [
                    'title' => 'Bad Request No Data Returned',
                    'payload' => [
                        'errors' => $errors,
                        $data,
                    ]

                ],
                HTTPStatusCodes::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['title' => 'Bad Request No Data Returned', 'payload' => 'No Response Message'], HTTPStatusCodes::HTTP_BAD_REQUEST);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendNotFoundResponse(array $data = [])
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Nothing Found',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            [
                'title' => 'Nothing Found',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_NOT_FOUND);
    }

    // 50x Server Error Response

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendInternelServerErrorJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Server Error Please Try Again',
                    'payload' => $data
                ],
                HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            [
                'title' => 'Server Error Please Try Again',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function sendForbiddenAccessJsonResponse(array $errors = [], array $data = null): JsonResponse
    {
        if (!empty($errors)) {
            return new JsonResponse(
                [
                    'title' => 'You Are Not Authorised To Be Here',
                    'payload' => [
                        'errors' => $errors,
                        $data,
                    ]

                ],
                HTTPStatusCodes::HTTP_FORBIDDEN);
        }

        return new JsonResponse(
            [
                'title' => 'You Are Not Authorised To Be Here',
                'payload' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_FORBIDDEN);
    }
}
