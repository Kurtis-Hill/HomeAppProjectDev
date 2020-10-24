<?php


namespace App\Traits\API;



use App\API\HTTPStatusCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

trait HomeAppAPIResponseTrait
{
    // 20x Successfull
    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendSuccessfulResponse(array $data = []): JsonResponse
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
        else {
            return new JsonResponse(['title' => 'Request Accepted Successfully Updated', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_CREATED);
        }
    }

    public function sendPartialContentResponse(array $data = []): JsonResponse
    {
        if (!empty($data)) {
            return new JsonResponse(
                [
                    'title' => 'Request Accepted Only Partial Response Sent',
                    'responseData' => $data
                ],
                HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }
        else {
            return new JsonResponse(['title' => 'Request Accepted Only Partial Response Sent', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_PARTIAL_CONTENT);
        }
    }

    // 40x Client Error Response

    public function sendBadRequestResponse()
    {

    }

    public function sendUnauthorised()
    {

    }



    public function sendNotFoundResponse()
    {

    }

    // 50x Server Error Response

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
        else {
            return new JsonResponse(['title' => 'Server Error Please Try Again', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
