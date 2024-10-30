<?php

/* --------------------------------------------: */
/*              CLASSES DEFINITIONS            : */

/* --------------------------------------------: */

class MasFormField
{
    public string $id;
    public string $name;
    public string $description;
    public bool $required;
    public string $default_value;
    public array $mappings;
    public bool $hide = false;


    public function __construct(string $id, string $name, string $description, bool $required, string $default_value, array $mappings, bool $hide = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->required = $required;
        $this->default_value = $default_value;
        $this->mappings = $mappings;
        $this->hide = $hide;
    }

    public function mas_getMapping(string $className)
    {
        foreach ($this->mappings as $mapping) {
            if ($mapping instanceof $className) {
                return $mapping;
            }
        }
        return null;
    }
}

class MasWpcf7Mapping
{
    public $plugin_name = 'Contact Form 7'; // for frontend help table
    public $allowed_aliases;
    public $type;

    public function __construct(array $allowed_aliases, string $type)
    {
        $this->allowed_aliases = $allowed_aliases;
        $this->type = $type;

    }

    public function mas_parseData($data)
    {
        switch ($this->type) {
            case 'acceptance':
                // convert data to bool
                if (is_array($data)) {
                    return $data[0] == '1';
                }
                return $data == '1';
            case 'select':
            case 'Array':
                // convert the array into a single string
                if (is_array($data) && count($data) > 0) {
                    return $data[0];
                } else {
                    return '';
                }
            case 'multivalue':
                return $data;
            default:
                if (is_array($data) && count($data) > 0) {
                    return $data[0];
                }
                return $data;
        }
    }
}

/* --------------------------------------------: */
/*                FIELDS MAPPINGS              : */
/* --------------------------------------------: */

// @formatter:off
$tags=[];
$custom_variables=[];
$tagsDescription=[];

$mas = new Mas();


$opt_custom_variables = get_option($GLOBALS['opt_cv_list'],array());
$hide = $GLOBALS['company_field_hide'];

for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
  $tagsDescription[]='tag'.$i;
  //'tag'.$i.$GLOBALS['company_prefix_placeholder']
    $tagsDescriptionCompany[]=$mas->placeholderCompany('tag'.$i);
  $tags[]=
    new MasFormField(
    /* id: */ 'tag'.$i,
        /* name: */ 'Tag',
        /* description: */ __('Tag', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ ['tag'.$i],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ true
    );
    $tags[]=
        new MasFormField(
        /* id: */ $mas->placeholderCompany('tag'.$i),
            /* name: */ 'Tag',
            /* description: */ __('Tag', 'marketing-automation-suite'),
            /* required: */ false,
            /* default_value: */ false,
            /* mappings: */ [
                new MasWpcf7Mapping(
                /* allowed_aliases: */ [$mas->placeholderCompany('tag'.$i)],
                    /* type: */ 'text'
                ),

            ],
            /*hide : */ $hide
        );
 }

$tags[]=new MasFormField(
/* id: */ 'tag',
    /* name: */ 'Tags',
    /* description: */ __('Tag1..Tag10','marketing-automation-suite'),
    /* required: */ false,
    /* default_value: */ false,
    /* mappings: */ [
        new MasWpcf7Mapping(
        /* allowed_aliases: */ $tagsDescription,
            /* type: */ 'text'
        ),
    ]
);

$tags[]=new MasFormField(
/* id: */ 'tag',
    /* name: */ 'Tags',
    /* description: */ __($mas->placeholderCompany('Tag1').'..'.$mas->placeholderCompany('Tag10'),'marketing-automation-suite'),
    /* required: */ false,
    /* default_value: */ false,
    /* mappings: */ [
        new MasWpcf7Mapping(
        /* allowed_aliases: */ $tagsDescription,
            /* type: */ 'text'
        ),
    ],
    /*hide : */ $hide
);


if(!empty($opt_custom_variables)){
    foreach ($opt_custom_variables as $cv){
        $name = str_replace($GLOBALS['cv_placeholder'],'',$cv['name']);
        $custom_variables[]=
            new MasFormField(
            /* id: */ $cv['name'],
                /* name: */ 'Custom Variable',
                /* description: */ __('MA Custom Variable '. $name . ' type ' . $cv['type'], 'marketing-automation-suite'),
                /* required: */ false,
                /* default_value: */ false,
                /* mappings: */ [
                    new MasWpcf7Mapping(
                    /* allowed_aliases: */ [$cv['name']],
                        /* type: */ $cv['type']
                    ),
                ]
            );
        $custom_variables[]=new MasFormField(
        /* id: */ $mas->placeholderCompany($cv['name']),
            /* name: */ 'Custom Variable Company',
            /* description: */ __('MA Custom Variable '. $name . ' type ' . $cv['type'], 'marketing-automation-suite'),
            /* required: */ false,
            /* default_value: */ false,
            /* mappings: */ [
                new MasWpcf7Mapping(
                /* allowed_aliases: */ [$mas->placeholderCompany($cv['name'])],
                    /* type: */$cv['type']
                ),
            ],
            /*hide : */ $hide
        );
    }
}



$GLOBALS["FORM_FIELDS"] = [
    new MasFormField(
    /* id: */ 'email_1',
        /* name: */ 'Email 1',
        /* description: */ __('Primary user email address. This is also the unique ID for the user information inside MA.', 'marketing-automation-suite'),
        /* required: */ true,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('email_1'),
                /* type: */ 'email'
            ),
        ]
    ),
    new MasFormField(
    /* id: */ 'first_name',
        /* name: */ 'First Name',
        /* description: */ __('First name of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('first_name'),
                /* type: */ 'text'
            ),
        ]
    ),
    new MasFormField(
    /* id: */ 'last_name',
        /* name: */ 'Last Name',
        /* description: */ __('Last name of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('last_name'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'fiscal_code',
        /* name: */ 'Fiscal Code / Tax Code',
        /* description: */ __('Tax code / fiscal code of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('fiscal_code'),
                /* type: */ 'text'
            ),

        ]
    ),

    new MasFormField(
    /* id: */ $mas->placeholderCompany('fiscalcode'),
        /* name: */ 'Fiscal Code / Tax Code',
        /* description: */ __('Tax code / fiscal code of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('fiscalcode'),$mas->placeholderCompany('fiscal_code'), $mas->placeholderCompany('tax_id')],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),

    new MasFormField(
    /* id: */ 'gender',
        /* name: */ 'Gender',
        /* description: */ __('Gender of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('gender'),
                /* type: */ 'select'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'datebirth',
        /* name: */ 'Birth Date',
        /* description: */ __('Date of birth of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('datebirth'),
                /* type: */ 'date'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'country',
        /* name: */ 'Country',
        /* description: */ __('Nation/Country ISO CODE of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('country'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('nation'),
        /* name: */ 'Nation',
        /* description: */ __('Nation/Country ISO CODE of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('nation'),$mas->placeholderCompany('country')],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'city',
        /* name: */ 'City',
        /* description: */ __('City of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('city'),
                /* type: */ 'text'
            ),
        ]
    ),

    new MasFormField(
    /* id: */ $mas->placeholderCompany('city'),
        /* name: */ 'City',
        /* description: */ __('City of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('city')],
                /* type: */ 'text'
            ),
        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'postal_code',
        /* name: */ 'Postal Code',
        /* description: */ __('Postal code of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('postal_code'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('postalcode'),
        /* name: */ 'Postal Code',
        /* description: */ __('Postal code of the Company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('postalcode'),$mas->placeholderCompany('postal_code')],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'state',
        /* name: */ 'State',
        /* description: */ __('State/Province of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('state'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('state'),
        /* name: */ 'State',
        /* description: */ __('State/Province of the Company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('province'), $mas->placeholderCompany('state')],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'address',
        /* name: */ 'Address',
        /* description: */ __('Address of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('address'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('address'),
        /* name: */ 'Address',
        /* description: */ __('Address of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('address')],
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'telephone_1',
        /* name: */ 'Telephone 1',
        /* description: */ __('Primary telephone number of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('telephone_1'),
                /* type: */ 'tel'
            ),

        ]
    ),
     new MasFormField(
    /* id: */ $mas->placeholderCompany('telephone_1'),
        /* name: */ 'Telephone 1',
        /* description: */ __('Primary telephone number of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('telephone1'),$mas->placeholderCompany('telephone')],
                /* type: */ 'tel'
            ),

        ],
         /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'mobile_1',
        /* name: */ 'Mobile Phone 1',
        /* description: */ __('Primary mobile phone number of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('mobile_1'),
                /* type: */ 'tel'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'profession',
        /* name: */ 'Profession',
        /* description: */ __('Profession of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('profession'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'vat_code',
        /* name: */ 'Vat Code',
        /* description: */ __('Vat Code of the company.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('vat_code'),
                /* type: */ 'text'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'placebirth',
        /* name: */ 'Birth Place',
        /* description: */ __('Birth Place of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('placebirth'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'preferred_language',
        /* name: */ 'Preferred Language',
        /* description: */ __('Preferred Language ISO CODE of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('preferred_language'),
                /* type: */ 'text'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ 'zone',
        /* name: */ 'Zone',
        /* description: */ __('Zone of the user.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('zone'),
                /* type: */ 'text'
            ),

        ]
    ),

    new MasFormField(
    /* id: */ 'name_company',
        /* name: */ 'Company Name',
        /* description: */ __('Company Name.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ '',
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ ['name_company','company_name'],
                /* type: */ 'text'
            ),
        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'gdpr_marketing',
        /* name: */ 'GDPR Marketing',
        /* description: */ __('Consent for the Person GDPR marketing.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('gdpr_marketing'),
                /* type: */ 'acceptance'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('gdpr_marketing'),
        /* name: */ 'GDPR Marketing Company',
        /* description: */ __('Consent for the Company GDPR marketing.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('gdpr_marketing'),$mas->placeholderCompany('gdpr-marketing')],
                /* type: */ 'acceptance'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'gdpr_profiling',
        /* name: */ 'GDPR Profiling',
        /* description: */ __('Consent for the Person GDPR profiling.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('gdpr_profiling'),
                /* type: */ 'acceptance'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('gdpr_profiling'),
        /* name: */ 'GDPR Profiling Company',
        /* description: */ __('Consent for the Company GDPR profiling.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('gdpr_profiling'),$mas->placeholderCompany('gdpr-profiling')],
                /* type: */ 'acceptance'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'gdpr_thirdparties',
        /* name: */ 'GDPR Third Parties',
        /* description: */ __('Consent for the Person GDPR for third parties.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ $mas->getMappingAlias('gdpr_thirdparties'),
                /* type: */ 'acceptance'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('gdpr_thirdparties'),
        /* name: */ 'GDPR Third Parties Company',
        /* description: */ __('Consent for the Company GDPR for third parties.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('gdpr_thirdparties'),$mas->placeholderCompany('gdpr-thirdparties')],
                /* type: */ 'acceptance'
            ),

        ],
        /*hide : */ $hide
    ),
    new MasFormField(
    /* id: */ 'gdpr_outsideeu',
        /* name: */ 'GDPR outsideeu',
        /* description: */ __('Consent for the Person GDPR for outsideeu.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */$mas->getMappingAlias('gdpr_outsideeu'),
                /* type: */ 'acceptance'
            ),

        ]
    ),
    new MasFormField(
    /* id: */ $mas->placeholderCompany('gdpr_outsideeu'),
        /* name: */ 'GDPR outsideeu Company',
        /* description: */ __('Consent for the Company GDPR for outsideeu.', 'marketing-automation-suite'),
        /* required: */ false,
        /* default_value: */ false,
        /* mappings: */ [
            new MasWpcf7Mapping(
            /* allowed_aliases: */ [$mas->placeholderCompany('gdpr_outsideeu'),$mas->placeholderCompany('gdpr-outsideeu')],
                /* type: */ 'acceptance'
            ),

        ],
        /*hide : */ $hide
    ),
];

/*$form_field = array_filter($GLOBALS["FORM_FIELDS"],function ($item) {
    return !$item->hide;
});

$tags = array_filter($tags,function ($item) {
    return !$item->hide;
});

$custom_variables = array_filter($custom_variables,function ($item) {
    return !$item->hide;
});


$tags = array_filter($tags,function ($item) {
    return !$item->hide;
});

//$GLOBALS["FORM_FIELDS"] = array_merge($form_field,$tags,$custom_variables);

*/

$GLOBALS["FORM_FIELDS"] = array_merge($GLOBALS["FORM_FIELDS"],$tags,$custom_variables);



// @formatter:on

