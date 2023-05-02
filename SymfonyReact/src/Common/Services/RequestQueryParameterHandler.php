<?php

namespace App\Common\Services;

use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\DTO\Request\RequestDTO;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestQueryParameterHandler
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws ValidatorProcessorException
     */
    public function handlerRequestQueryParameterCreation(
        mixed $responseType = RequestTypeEnum::ONLY->value,
        mixed $page = null,
        mixed $limit = null
    ): RequestDTO {
//        $responseType = match ($responseType) {
//            RequestTypeEnum::FULL => RequestTypeEnum::FULL,
//            RequestTypeEnum::SENSITIVE_FULL => RequestTypeEnum::SENSITIVE_FULL,
//            RequestTypeEnum::SENSITIVE_ONLY => RequestTypeEnum::SENSITIVE_ONLY,
//            default => RequestTypeEnum::ONLY,
//        };
//        if ($responseType === RequestTypeEnum::FULL) {
            $requestTypeDTO = RequestDTOBuilder::buildRequestDTO(
                $responseType,
                is_numeric($page) ? (int) $page : $page,
                is_numeric($limit) ? (int) $limit : $limit,
            );
//            try {
                $validationErrors = $this->validator->validate($requestTypeDTO);

                if ($this->checkIfErrorsArePresent($validationErrors)) {
                    throw new ValidatorProcessorException($this->getValidationErrorAsArray($validationErrors));
                }

                return $requestTypeDTO;
//            } catch (ExceptionInterface) {
//                return $this->sendInternalServerErrorJsonResponse();
//            }
//            $deviceDTO = $deviceResponseDTOBuilder->buildFullDeviceResponseDTO($devices, true);
//        } else {
//            $deviceDTO = $deviceResponseDTOBuilder->buildFullDeviceResponseDTO($devices);
//        }
    }
}
