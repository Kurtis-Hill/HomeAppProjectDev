<?php


namespace App\Common\API\Traits;

use App\Common\API\HTTPStatusCodes;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait HomeAppAPITrait
{
    public const NO_RESPONSE_MESSAGE = 'No Response Message';

    public const REQUEST_SUCCESSFUL = 'Request Successful';

    public const REQUEST_ACCEPTED_SUCCESS_CREATED = 'Request Accepted Successfully Created';

    public const REQUEST_PARTIALLY_ACCEPTED = 'Request Accepted Only Partial Response Sent';

    public const NOTHING_FOUND = 'Nothing Found';

    public const SERVER_ERROR_TRY_AGAIN = 'Server Error Please Try Again';

    public const NOT_AUTHORIZED_TO_BE_HERE = 'You Are Not Authorised To Be Here';

    public const BAD_REQUEST_NO_DATA_RETURNED = 'Bad Request No Data Returned';

    // 20x Successfull
    /**
      * @param array $data
      * @param string $title
      * @return JsonResponse
     */
    public function sendSuccessfulJsonResponse(array $data = [], string $title = self::REQUEST_SUCCESSFUL): JsonResponse
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

    /**
     * @param array $data
     * @param string $title
     * @return JsonResponse
     */
    public function sendSuccessfullyAddedToBeProcessedJsonResponse(array $data = [], string $title = self::REQUEST_SUCCESSFUL): JsonResponse
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

    /**
     * @param array $data
     * @param string $title
     * @return JsonResponse
     */
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

    /**
     * @param mixed[] $errors
     * @param mixed[] $data
     * @return JsonResponse
     */
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

    /**
     * @param mixed[] $errors
     * @param mixed[] $data
     * @param string $title
     * @return JsonResponse
     */
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
    /**
     * @param array $errors
     * @param string $title
     * @return JsonResponse
     */
    public function sendBadRequestJsonResponse(array $errors = [], string $title = self::BAD_REQUEST_NO_DATA_RETURNED): JsonResponse
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

    /**
     * @param array $errors
     * @return JsonResponse
     */
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

    /**
     * @param array $data
     * @param string $title
     * @return JsonResponse
     */
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

    /**
     * @param array $errors
     * @return JsonResponse
     */
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

    /**
     * @param array $data
     * @param int $statusCode
     * @return JsonResponse
     */
    private function returnJsonResponse(array $data, int $statusCode): JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }
    /**
     * @throws ExceptionInterface
     */
    public function normalize(mixed $data, array $groups = []): mixed
    {
        $context[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT] = 10;

        if (!empty($groups)) {
            $annotationClassMetadataFactory = new ClassMetadataFactory(
                new AttributeLoader(
                    new AnnotationReader()
                )
            );

            $context['groups'] = $groups;
        }

        $normalizer = [new ObjectNormalizer($annotationClassMetadataFactory ?? null)];
        $normalizer = new Serializer($normalizer);

        return $normalizer->normalize($data, null, $context ?? []);
    }

    /**
     * @param string|mixed[] $data
     * @param string|null $class
     * @param string|null $format
     * @param array $extraContexts
     * @param bool $docExtractor
     * @return mixed
     */
    public function deserializeRequest(
        string|array $data,
        ?string $class = null,
        ?string $format = null,
        array $extraContexts = [],
        bool $docExtractor = false,
    ): mixed {
        $encoders = match ($format) {
            'xml' => [new XmlEncoder()],
            'json' => [new JsonEncoder()],
            default => [new XmlEncoder(), new JsonEncoder()],
        };

        if ($docExtractor === true) {
            $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
            $normalizers = [
                new ArrayDenormalizer(),
                new ObjectNormalizer(
                    null,
                    new CamelCaseToSnakeCaseNameConverter(),
                    null,
                    $extractor
                )
            ];
        } else {
            $normalizers = [new ObjectNormalizer()];
        }

        return (new Serializer(
            $normalizers,
            $encoders
        ))->deserialize(
            $data,
            $class,
            $format,
            $extraContexts
        );
    }
}
