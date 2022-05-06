<?php

namespace App\ArgumentResolver;

use App\Attribute\RequestBody;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class RequestBodyArgumentResolver implements ArgumentValueResolverInterface
{
    public function __construct(private readonly SerializerInterface $serializer,
                                private readonly ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return count($argument->getAttributes(RequestBody::class, ArgumentMetadata::IS_INSTANCEOF)) > 0;
    }


    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            $model = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                JsonEncoder::FORMAT
            );
        } catch (Throwable $throwable) {
            throw new Exception($throwable);
        }

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            throw new Exception($errors);
        }

        yield $model;
    }
}
