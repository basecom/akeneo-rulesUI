<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\DataTransformer;

use Basecom\Bundle\RulesEngineBundle\Form\Type\ConditionType;
use Akeneo\Pim\Structure\Component\Model\Attribute;
use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Justus Klein <klein@basecom.de>
 * @author Jordan Kniest <j.kniest@basecom.de>
 */
class AttributeCodeToAttributeTransformer implements DataTransformerInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * Constructor of CodeToAttributeTransformer.
     *
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(AttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Transforms an object (Attribute) to a string (Attribute-Code).
     *
     * @param Attribute|null $attribute
     *
     * @return string
     */
    public function transform($attribute): string
    {
        if (is_string($attribute)) {
            return $attribute;
        }

        return null === $attribute ? '' : $attribute->getCode();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param string $attributeCode
     *
     * @throws TransformationFailedException if object (Attribute) is not found
     *
     * @return Attribute|string|null
     */
    public function reverseTransform($attributeCode)
    {
        if (!$attributeCode) {
            return null;
        }

        if ($attributeCode instanceof Attribute) {
            return $attributeCode;
        }

        if (in_array($attributeCode, ConditionType::getAdditionalFieldCodes(), true)) {
            return $attributeCode;
        }

        /** @var Attribute|null $attribute */
        $attribute = $this->attributeRepository->findOneBy(['code' => $attributeCode]);

        if (null === $attribute) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An Attribute with the Attribute-Code "%s" does not exist!',
                $attributeCode
            ));
        }

        return $attribute;
    }
}
