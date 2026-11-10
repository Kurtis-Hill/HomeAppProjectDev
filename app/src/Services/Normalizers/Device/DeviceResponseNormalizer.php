<?php
declare(strict_types=1);

namespace App\Services\Normalizers\Device;

use App\DTOs\Device\Response\DeviceResponseDTO;
use App\Services\Request\RequestTypeEnum;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DeviceResponseNormalizer implements NormalizerInterface
{
    public function normalize(mixed $device, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!empty($context['groups'])) {
            $classMetadataFactory = new ClassMetadataFactory(
                new AttributeLoader()
            );

            $context = ['groups' => $context['groups']];
        }

        $normalizer = [new ObjectNormalizer($classMetadataFactory ?? null)];
        $normalizer = new Serializer($normalizer);

        $data = $normalizer->normalize($device, $format, $context);

        if (!in_array([RequestTypeEnum::SENSITIVE_FULL->value, RequestTypeEnum::SENSITIVE_ONLY->value], $context, true)) {
            unset($data['secret']);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof DeviceResponseDTO;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [DeviceResponseDTO::class];
    }
}
