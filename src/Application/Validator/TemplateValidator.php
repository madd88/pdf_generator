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
            ],
            'medical' =>  [
                'name' => v::stringType()->notEmpty()->setName('Name'),
                'dob' => v::date('Y-m-d')->notEmpty()->setName('Date of Birth'),
                'streetAddress' => v::stringType()->notEmpty()->setName('Street Address'),
                'town' => v::stringType()->notEmpty()->setName('Town'),
                'state' => v::stringType()->length(2, 2)->notEmpty()->setName('State'),
                'zip' => v::postalCode('RU')->notEmpty()->setName('ZIP Code'),
                'phoneNumber' => v::optional(v::phone()),
                'nameAdd' => v::stringType()->setName('Additional name'),
                'phoneNumberAdd' => v::optional(v::phone()),
                'email' => v::optional(v::email()),
                'appointmentDate' => v::date('Y-m-d')->notEmpty()->setName('Appointment Date'),
                'cause' => v::stringType()->notEmpty()->setName('Cause'),
                'excuseFrom' => v::in(['Work', 'Collage'])->notEmpty()->setName('Excuse From'),
                'excuseUntil' => v::date('Y-m-d')->notEmpty()->setName('Excuse Until'),
                'weight' => v::positive()->notEmpty()->setName('Weight'),
                'insuranceProvider' => v::stringType()->setName('Insurance Provider'),
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