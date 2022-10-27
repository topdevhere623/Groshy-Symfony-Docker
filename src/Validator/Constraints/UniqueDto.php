<?php

declare(strict_types=1);

namespace Groshy\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueDto extends Constraint
{
    public const NOT_UNIQUE_ERROR = 'e777db8d-3af0-41f6-8a73-55255375cdca';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public $em;

    public $entityClass;

    public $errorPath;

    public $fieldMapping = [];

    public $ignoreNull = true;

    public $message = 'This value is already used.';

    public $repositoryMethod = 'findBy';

    public function getDefaultOption()
    {
        return 'entityClass';
    }

    public function getRequiredOptions()
    {
        return ['fieldMapping', 'entityClass'];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return UniqueDtoValidator::class;
    }
}
