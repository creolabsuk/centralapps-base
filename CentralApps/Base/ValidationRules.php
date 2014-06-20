<?php
namespace CentralApps\Base\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class ValidationRules
{
    use \CentralApps\Base\Traits\ContainerAware;

    protected $bannedEmailDomains = [];
    protected $bannedEmailDomainsMessage = "Sorry, disposable email services are not permitted";

    public function setBannedEmailDomains(array $domains)
    {
        $this->bannedEmailDomains = $domains;
    }

    public function setBannedEmailDomainsMessage($message)
    {
        $this->bannedEmailDomainsMessage = $message;
    }

    public function getConstraint($input)
    {
        $constraints = $this->getConstraintArray();

        return new Assert\Collection($constraints);
    }

    public function getValidator()
    {
        return \Symfony\Component\Validator\Validation::createValidator();
    }

    public function getViolations($input)
    {
        return $this->getValidator()->validateValue($input, $this->getConstraint($input));
    }

    protected function getNameRules()
    {
        return [
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 2, 'max' => 255))
        ];
    }

    protected function getEmailRules()
    {
        return [
            new Assert\NotBlank(['message' => 'You must provide an email address']),
            new Assert\Email(),
            new Assert\Callback(
                [
                    'methods' => [
                        function ($entity, $context) {
                            $parts = explode('@', $entity);
                            if (in_array($parts[(count($parts) - 1)], $this->bannedEmailDomains)) {
                                $context->addViolation($this->bannedEmailDomainsMessage);
                            }
                        }
                    ]
                ]
            )
        ];
    }

    protected function getOptionalCheckboxRules()
    {
        return [
            new Assert\Optional(
                [
                    new Assert\Range(['min' => 0, 'max' => 1])
                ]
            )
        ];
    }
}
