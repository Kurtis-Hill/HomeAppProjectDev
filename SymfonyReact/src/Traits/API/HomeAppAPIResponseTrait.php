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
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_OK);
        }
        else {
            return new JsonResponse(['title' => 'Request Successful', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_OK);
        }
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
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY);
        }

        return new JsonResponse(['title' => 'Request Successful', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_OK);
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
    public function sendCreatedResourceResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Successfully Updated',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_CREATED);
        }

        return new JsonResponse(['title' => 'Request Accepted Successfully Updated', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_CREATED);
    }

    public function sendPartialContentJsonResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Only Partial Response Sent',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }

        return new JsonResponse(['title' => 'Request Accepted Only Partial Response Sent', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
    }


    public function sendPartialContentResponse(string $data = null): Response
    {
        if ($data !== null) {
            return new Response(
                $data,
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }

        return new JsonResponse(['title' => 'Request Accepted Only Partial Response Sent', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
    }
    // 40x Client Error Response

    public function sendBadRequestResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Bad Request No Data Returned',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['title' => 'Bad Request No Data Returned', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_BAD_REQUEST);
    }

    public function sendUnauthorised()
    {

    }



    public function sendNotFoundResponse(array $data = [])
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Nothing Found',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            [
                'title' => 'Nothing Found',
                'responseData' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_NOT_FOUND);
    }

    // 50x Server Error Response

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function sendInternelServerErrorResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Server Error Please Try Again',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            [
                'title' => 'Server Error Please Try Again',
                'responseData' => 'No Response Message'
            ],
            HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }
}
