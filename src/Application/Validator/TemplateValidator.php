<?php
namespace App\Application\Validator;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class TemplateValidator
{
    private static array $rules = [];

    public static function validate(string $template, array $data): array
    {
        self::initRules();

        if (!isset(self::$rules[$template])) {
            throw new \InvalidArgumentException("Invalid template: $template");
        }

        $errors = [];
        $validator = v::keySet(...self::createKeySet(self::$rules[$template]));

        try {
            $validator->assert($data);
        } catch (NestedValidationException $e) {
            $errors = $e->getMessages();
        }

        return $errors;
    }

    private static function initRules(): void
    {
        if (!empty(self::$rules)) {
            return;
        }

        self::$rules = [
            'ein_letter' => [
                'businessName' => v::stringType()->notEmpty(),
                'businessType' => v::in(['Profit LLC', 'Profit Corp']),
                'businessAddress' => v::stringType()->notEmpty(),
                'businessTown' => v::stringType()->notEmpty(),
                'businessState' => v::stringType()->notEmpty(),
                'businessZip' => v::postalCode('US'),
                'incorporationDate' => v::date('Y-m-d'),
                'ein' => v::regex('/^\d{2}-\d{7}$/'),
                'ownerName' => v::stringType()->notEmpty(),
            ],
            'geico_insurance' => [
                'name' => v::stringType()->notEmpty(),
                'addressLine1' => v::stringType()->notEmpty(),
                'town' => v::stringType()->notEmpty(),
                'state' => v::stringType()->length(2, 2),
                'zip' => v::postalCode('US'),
                'vehicleYear' => v::intVal()->between(1900, date('Y') + 1),
                'vehicleModel' => v::stringType()->notEmpty(),
                'vin' => v::regex('/^[A-HJ-NPR-Z0-9]{17}$/'),
                'effectiveDate' => v::date('Y-m-d'),
                'additionalDriver' => v::optional(v::stringType()),
            ],
            'hippo_policy' => [
                'homeownerName' => v::stringType()->notEmpty(),
                'homeownerStreet' => v::stringType()->notEmpty(),
                'homeownerTown' => v::stringType()->notEmpty(),
                'homeownerState' => v::stringType()->length(2, 2),
                'homeownerZIP' => v::postalCode('US'),
                'propertyAddress' => v::optional(v::stringType()),
                'builtYear' => v::intVal()->between(1800, date('Y')),
                'squareFootage' => v::intVal()->min(100),
                'creationDate' => v::date('Y-m-d'),
                'constructionType' => v::stringType()->notEmpty(),
            ]
        ];
    }

    private static function createKeySet(array $rules): array
    {
        $keySet = [];
        foreach ($rules as $field => $rule) {
            $keySet[] = v::key($field, $rule);
        }
        return $keySet;
    }
}