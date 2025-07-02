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
            'ein_letter'      => [
                'businessName'      => v::stringType()->notEmpty(),
                'businessType'      => v::in(['Profit LLC', 'Profit Corp']),
                'businessAddress'   => v::stringType()->notEmpty(),
                'businessTown'      => v::stringType()->notEmpty(),
                'businessState'     => v::stringType()->notEmpty(),
                'businessZip'       => v::postalCode('US'),
                'incorporationDate' => v::date('Y-m-d'),
                'ein'               => v::regex('/^\d{2}-\d{7}$/'),
                'ownerName'         => v::stringType()->notEmpty(),
            ],
            'geico_insurance' => [
                'name'             => v::stringType()->notEmpty(),
                'addressLine1'     => v::stringType()->notEmpty(),
                'town'             => v::stringType()->notEmpty(),
                'state'            => v::stringType()->length(2, 2),
                'zip'              => v::postalCode('US'),
                'vehicleYear'      => v::intVal()->between(1900, date('Y') + 1),
                'vehicleModel'     => v::stringType()->notEmpty(),
                'vin'              => v::regex('/^[A-HJ-NPR-Z0-9]{17}$/'),
                'effectiveDate'    => v::date('Y-m-d'),
                'additionalDriver' => v::optional(v::stringType()),
            ],
            'hippo_policy'    => [
                'homeownerName'    => v::stringType()->notEmpty(),
                'homeownerStreet'  => v::stringType()->notEmpty(),
                'homeownerTown'    => v::stringType()->notEmpty(),
                'homeownerState'   => v::stringType()->length(2, 2),
                'homeownerZIP'     => v::postalCode('US'),
                'propertyAddress'  => v::optional(v::stringType()),
                'builtYear'        => v::intVal()->between(1800, date('Y')),
                'squareFootage'    => v::intVal()->min(100),
                'creationDate'     => v::date('Y-m-d'),
                'constructionType' => v::stringType()->notEmpty(),
            ],
            'medical'         => [
                'name'              => v::stringType()->notEmpty()->setName('Name'),
                'dob'               => v::date('Y-m-d')->notEmpty()->setName('Date of Birth'),
                'streetAddress'     => v::stringType()->notEmpty()->setName('Street Address'),
                'town'              => v::stringType()->notEmpty()->setName('Town'),
                'state'             => v::stringType()->length(2, 2)->notEmpty()->setName('State'),
                'zip'               => v::postalCode('RU')->notEmpty()->setName('ZIP Code'),
                'phoneNumber'       => v::optional(v::phone()),
                'nameAdd'           => v::stringType()->setName('Additional name'),
                'phoneNumberAdd'    => v::optional(v::phone()),
                'email'             => v::optional(v::email()),
                'appointmentDate'   => v::date('Y-m-d')->notEmpty()->setName('Appointment Date'),
                'cause'             => v::stringType()->notEmpty()->setName('Cause'),
                'excuseFrom'        => v::in(['Work', 'Collage'])->notEmpty()->setName('Excuse From'),
                'excuseUntil'       => v::date('Y-m-d')->notEmpty()->setName('Excuse Until'),
                'weight'            => v::positive()->notEmpty()->setName('Weight'),
                'insuranceProvider' => v::stringType()->setName('Insurance Provider'),
            ],

            'invoice' => [
                // Business Section
                'business.name' => v::stringType()->notEmpty()->setName('Business Name'),
                'business.address' => v::stringType()->notEmpty()->setName('Business Address'),
                'business.town' => v::stringType()->notEmpty()->setName('Business Town'),
                'business.state' => v::stringType()->notEmpty()->setName('Business State'),
                'business.zip' => v::stringType()->notEmpty()->setName('Business ZIP'),
                'business.email' => v::optional(v::email()),
                'business.phone' => v::optional(v::phone()),
                'business.einVatId' => v::optional(v::stringType()),

                // Customer Section
                'customer.businessPersonalName' => v::stringType()->notEmpty()->setName('Customer Business/Personal Name'),
                'customer.officerPersonalName' => v::stringType()->notEmpty()->setName('Customer Officer/Personal Name'),
                'customer.address' => v::stringType()->notEmpty()->setName('Customer Address'),
                'customer.town' => v::stringType()->notEmpty()->setName('Customer Town'),
                'customer.state' => v::stringType()->notEmpty()->setName('Customer State'),
                'customer.zip' => v::stringType()->notEmpty()->setName('Customer ZIP'),
                'customer.shippingAddress' => v::optional(v::stringType()),
                'customer.shippingTown' => v::optional(v::stringType()),
                'customer.shippingState' => v::optional(v::stringType()),
                'customer.shippingZip' => v::optional(v::stringType()),
                'customer.email' => v::optional(v::email()),

                // Items Validation
                'items' => v::arrayType()->length(1, 4)->each(
                    v::arrayType()->keySet(
                        v::key('name', v::stringType()->notEmpty()->setName('Item Name')),
                        v::key('description', v::stringType()->notEmpty()->setName('Item Description')),
                        v::key('quantity', v::intVal()->positive()->setName('Quantity')),
                        v::key('pricePerItem', v::positive()->setName('Price per Item')),
                        v::key('discount', v::optional(v::between(0, 100)->setName('Discount')))
                    )
                )->setName('Items'),

                // Invoice Metadata
                'invoice.date' => v::date('Y-m-d')->notEmpty()->setName('Invoice Date'),
                'invoice.dueDate' => v::optional(v::date('Y-m-d')),
                'invoice.notes' => v::optional(v::stringType()),
                'invoice.status' => v::in([
                    'Pending', 'Created', 'Unpaid', 'Paid', 'Cancelled', 'Declined', 'VOID'
                ])->notEmpty()->setName('Invoice Status'),
                'invoice.projectReference' => v::optional(v::stringType()),

                // Payment Methods Validation
                'paymentMethods' => v::arrayType()->length(0, 3)->each(
                    v::arrayType()->keySet(
                        v::key('type', v::in([
                            'Cash', 'Bank Transfer', 'Credit Card', 'PayPal',
                            'CashApp', 'Zelle', 'Venmo', 'Crypto', 'Other'
                        ])->notEmpty()->setName('Payment Type')),

                        v::key('description', v::stringType()->notEmpty()->setName('Payment Description')),

                        // Cash-specific fields with conditions
                        v::key('cashDeliveryAddress', v::when(
                            v::key('type', v::equals('Cash')),
                            v::stringType()->notEmpty()->setName('Cash Delivery Address'),
                            v::optional(v::stringType())
                        )),
                        v::key('cashDeliveryTown', v::when(
                            v::key('type', v::equals('Cash')),
                            v::stringType()->notEmpty()->setName('Cash Delivery Town'),
                            v::optional(v::stringType())
                        )),
                        v::key('cashDeliveryState', v::when(
                            v::key('type', v::equals('Cash')),
                            v::stringType()->notEmpty()->setName('Cash Delivery State'),
                            v::optional(v::stringType())
                        )),
                        v::key('cashDeliveryZip', v::when(
                            v::key('type', v::equals('Cash')),
                            v::stringType()->notEmpty()->setName('Cash Delivery ZIP'),
                            v::optional(v::stringType())
                        )),

                        // Bank Transfer-specific fields
                        v::key('bankName', v::when(
                            v::key('type', v::equals('Bank Transfer')),
                            v::stringType()->notEmpty()->setName('Bank Name'),
                            v::optional(v::stringType())
                        )),
                        v::key('accountNumber', v::when(
                            v::key('type', v::equals('Bank Transfer')),
                            v::stringType()->notEmpty()->setName('Account Number'),
                            v::optional(v::stringType())
                        )),
                        v::key('routingNumber', v::when(
                            v::key('type', v::equals('Bank Transfer')),
                            v::stringType()->notEmpty()->setName('Routing Number'),
                            v::optional(v::stringType())
                        )),

                        // Digital wallets
                        v::key('account', v::when(
                            v::key('type', v::in(['PayPal', 'CashApp', 'Zelle', 'Venmo'])),
                            v::stringType()->notEmpty()->setName('Account Nick/Email'),
                            v::optional(v::stringType())
                        )),

                        // Crypto-specific fields
                        v::key('cryptoName', v::when(
                            v::key('type', v::equals('Crypto')),
                            v::stringType()->notEmpty()->setName('Crypto Name'),
                            v::optional(v::stringType())
                        )),
                        v::key('cryptoAddress', v::when(
                            v::key('type', v::equals('Crypto')),
                            v::stringType()->notEmpty()->setName('Crypto Address'),
                            v::optional(v::stringType())
                        )),

                        // Credit Card-specific field
                        v::key('paymentSite', v::when(
                            v::key('type', v::equals('Credit Card')),
                            v::stringType()->notEmpty()->setName('Payment Site'),
                            v::optional(v::stringType())
                        )),

                        // Other payment method
                        v::key('methodName', v::when(
                            v::key('type', v::equals('Other')),
                            v::stringType()->notEmpty()->setName('Method Name'),
                            v::optional(v::stringType())
                        )),
                        v::key('methodDescription', v::when(
                            v::key('type', v::equals('Other')),
                            v::stringType()->notEmpty()->setName('Method Description'),
                            v::optional(v::stringType())
                        ))
                    )
                )->setName('Payment Methods'),
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