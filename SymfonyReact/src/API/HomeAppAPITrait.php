<?php


namespace App\Traits;



use App\HTTPStatusCodes;
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
            return new JsonResponse(['title' => 'Request Successful', 'responseData' => $data], HTTPStatusCodes::HTTP_OK);
        }
        else {
            return new JsonResponse(['title' => 'Request Successful', 'responseData' => 'No Response Message'], HTTPStatusCodes::HTTP_OK);
        }
    }

    public function sendCreatedResourceResponse()
    {

    }

    public function sendPartialContentResponse()
    {

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

    public function sendInternelServerErrorResponse()
    {

    }



}
