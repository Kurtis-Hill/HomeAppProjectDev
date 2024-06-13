<?php
declare(strict_types=1);

namespace App\Services\Request;

use App\Builders\Request\RequestDTOBuilder;
use App\DTOs\RequestDTO;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Traits\ValidatorProcessorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestQueryParameterHandler
{
    use ValidatorProcessorTrait;

    public const RESPONSE_TYPE = 'responseType';

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
        mixed $limit = null,
    ): RequestDTO {
        $requestTypeDTO = RequestDTOBuilder::buildRequestDTO(
            $responseType,
            is_numeric($page) ? (int) $page : $page,
            is_numeric($limit) ? (int) $limit : $limit,
        );

        $validationErrors = $this->validator->validate($requestTypeDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new ValidatorProcessorException($this->getValidationErrorAsArray($validationErrors));
        }

        return $requestTypeDTO;
    }
}
