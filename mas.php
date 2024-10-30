<?php

class Mas
{
    public array $mapping_alias = [
        'email_1' => ['email_1', 'email', 'your-email'],
        'first_name' => ['first_name', 'firstname', 'your-first-name'],
        'last_name' => ['last_name', 'lastname', 'your-last-name'],
        'fiscal_code' => ['fiscal_code', 'fiscalcode', 'tax_id'],
        'gender' => ['gender'],
        'datebirth' => ['datebirth', 'birthdate'],
        'state' => ['state', 'province'],
        'city' => ['city'],
        'postal_code' => ['postal_code', 'postalcode'],
        'country' => ['nation', 'country'],
        'address' => ['address'],
        'telephone_1' => ['telephone_1', 'telephone1', 'telephone'],
        'mobile_1' => ['mobile_1', 'mobile', 'mobile1'],
        'profession' => ['profession'],
        'vat_code' => ['vat_code', 'vatcode'],
        'placebirth' => ['placebirth', 'place_birth'],
        'preferred_language' => ['preferred_language'],
        'zone' => ['zone'],
        'gdpr_marketing' => ['gdpr_marketing', 'gdpr-marketing'],
        'gdpr_profiling' => ['gdpr_profiling', 'gdpr-profiling'],
        'gdpr_thirdparties' => ['gdpr_thirdparties', 'gdpr-thirdparties'],
        'gdpr_outsideeu' => ['gdpr_outsideeu', 'gdpr-outsideeu']
    ];


    /**
     * @param string $key
     * @return string[]
     */
    public function getMappingAlias(string $key): array
    {
        return $this->mapping_alias[$key];
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function getMaFieldName(string $value): ?string
    {
        foreach ($this->mapping_alias as $key => $aliases) {
            if (in_array($value, $aliases)) {
                return $key;
            }
        }
        return null;
    }


    public function placeholderCompany($name): string
    {
        return $GLOBALS['company_prefix_placeholder'] . $name;
    }


}