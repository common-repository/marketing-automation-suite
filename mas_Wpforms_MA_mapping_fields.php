<?php


class MasWpFormsMAMappingField
{

    public array $mapping = [
        'email_1',
        'first_name',
        'last_name',
        'fiscal_code',
        'gender',
        'datebirth',
        'state',
        'city',
        'postal_code',
        'country',
        'address',
        'telephone_1',
        'mobile_1',
        'profession',
        'vat_code',
        'placebirth',
        'preferred_language',
        'zone',
        'gdpr_marketing',
        'gdpr_profiling',
        'gdpr_thirdparties',
        'gdpr_outsideeu'
    ];

    function __construct()
    {
        $opt_custom_variables = get_option($GLOBALS['opt_cv_list'], array());

        for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
            $this->mapping[] = 'tag' . $i;
        }

        foreach ($opt_custom_variables as $cv) {
            $this->mapping[] = $cv['name'];
        }


    }

    /**
     * @param $sections
     * @param $form_data
     * @return mixed
     */
    function mas_wpformsSettingsSection($sections, $form_data)
    {
        $sections['marketing_automation'] = __('Marketing Automation', 'marketing-automation-suite');
        return $sections;
    }


    /**
     * @param $gender
     * @return ?string
     */
    function mas_findGender($gender): ?string
    {
        $genderSuite = null;
        $gender = (is_array($gender)) ? $gender[0] : $gender;

        switch (strtolower($gender)) {
            case "m" :
            case "uomo" :
            case "man" :
            case "male" :
            case "maschio" :
                $genderSuite = "Male";
                break;
            case "f" :
            case "donna" :
            case "woman" :
            case "female" :
            case "femmina" :
                $genderSuite = "Female";
                break;
        }
        return $genderSuite;
    }

    /**
     * @param string|null $state
     * @return string|null
     */

    function mas_findProvince(?string $state = null): ?string
    {
        if (empty($state)) {
            return null;
        }

        $province = [
            "Agrigento" => "AG",
            "Alessandria" => "AL",
            "Ancona" => "AN",
            "Aosta" => "AO",
            "Arezzo" => "AR",
            "Ascoli Piceno" => "AP",
            "Asti" => "AT",
            "Avellino" => "AV",
            "Bari" => "BA",
            "Barletta-Andria-Trani" => "BT",
            "Belluno" => "BL",
            "Benevento" => "BN",
            "Bergamo" => "BG",
            "Biella" => "BI",
            "Bologna" => "BO",
            "Bolzano" => "BZ",
            "Brescia" => "BS",
            "Brindisi" => "BR",
            "Cagliari" => "CA",
            "Caltanissetta" => "CL",
            "Campobasso" => "CB",
            "Carbonia-Iglesias" => "CI",
            "Caserta" => "CE",
            "Catania" => "CT",
            "Catanzaro" => "CZ",
            "Chieti" => "CH",
            "Como" => "CO",
            "Cosenza" => "CS",
            "Cremona" => "CR",
            "Crotone" => "KR",
            "Cuneo" => "CN",
            "Enna" => "EN",
            "Fermo" => "FM",
            "Ferrara" => "FE",
            "Firenze" => "FI",
            "Foggia" => "FG",
            "Forlì-Cesena" => "FC",
            "Frosinone" => "FR",
            "Genova" => "GE",
            "Gorizia" => "GO",
            "Grosseto" => "GR",
            "Imperia" => "IM",
            "Isernia" => "IS",
            "La Spezia" => "SP",
            "L'Aquila" => "AQ",
            "Latina" => "LT",
            "Lecce" => "LE",
            "Lecco" => "LC",
            "Livorno" => "LI",
            "Lodi" => "LO",
            "Lucca" => "LU",
            "Macerata" => "MC",
            "Mantova" => "MN",
            "Massa - Carrara" => "MS",
            "Matera" => "MT",
            "Medio Campidano" => "VS",
            "Messina" => "ME",
            "Milano" => "MI",
            "Modena" => "MO",
            "Monza e della Brianza" => "MB",
            "Napoli" => "NA",
            "Novara" => "NO",
            "Nuoro" => "NU",
            "Ogliastra" => "OG",
            "Olbia - Tempio" => "OT",
            "Oristano" => " or ",
            "Padova" => "PD",
            "Palermo" => "PA",
            "Parma" => "PR",
            "Pavia" => "PV",
            "Perugia" => "PG",
            "Pesaro e Urbino" => "PU",
            "Pescara" => "PE",
            "Piacenza" => "PC",
            "Pisa" => "PI",
            "Pistoia" => "PT",
            "Pordenone" => "PN",
            "Potenza" => "PZ",
            "Prato" => "PO",
            "Ragusa" => "RG",
            "Ravenna" => "RA",
            "Reggio Calabria" => "RC",
            "Reggio Emilia" => "RE",
            "Rieti" => "RI",
            "Rimini" => "RN",
            "Roma" => "RM",
            "Rovigo" => "RO",
            "Salerno" => "SA",
            "Sassari" => "SS",
            "Savona" => "SV",
            "Siena" => "SI",
            "Siracusa" => "SR",
            "Sondrio" => "SO",
            "Taranto" => "TA",
            "Teramo" => "TE",
            "Terni" => "TR",
            "Torino" => "TO",
            "Trapani" => "TP",
            "Trento" => "TN",
            "Treviso" => "TV",
            "Trieste" => "TS",
            "Udine" => "UD",
            "Varese" => "VA",
            "Venezia" => "VE",
            "Verbano - cusio - ossola" => "VB",
            "Verbano/cusio/ossola" => "VB",
            "Vercelli" => "VC",
            "Verona" => "VR",
            "Vibo Valentia" => "VV",
            "Vicenza" => "VI",
            "Viterbo" => "VT"
        ];

        return $province[ucfirst(strtolower($state))] ?? strtoupper($state);

    }


    /**
     * @param $instance
     * @return void
     */
    function mas_wpformsSettingsSectionContent($instance)
    {
        echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-marketing_automation">';
        echo '<div class="wpforms-panel-content-section-title">' . __('Marketing Automation', 'marketing-automation-suite') . ' - ' . __('Fields Mapping', 'marketing-automation-suite') . ' </div>';
        $mas = new Mas();

        /*wpforms_panel_field(
            'select',
            'settings',
            'form_name',
            $instance->form_data,
            __('Form Name', 'marketing-automation-suite'),
            array(
                'field_map' => array('form_name'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/

        wpforms_panel_field(
            'select',
            'settings',
            'email_1',
            $instance->form_data,
            __('Email Address', 'marketing-automation-suite'),
            array(
                'field_map' => array('email'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        /* wpforms_panel_field(
             'select',
             'settings',
             'contact_code',
             $instance->form_data,
             __('Contact Code', 'marketing-automation-suite'),
             array(
                 'field_map' => array('text'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/
        /* wpforms_panel_field(
             'select',
             'settings',
             'email_2',
             $instance->form_data,
             __('Email Address 2', 'marketing-automation-suite'),
             array(
                 'field_map' => array('email'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/

        wpforms_panel_field(
            'select',
            'settings',
            'first_name',
            $instance->form_data,
            __('First Name', 'marketing-automation-suite'),
            array(
                'field_map' => array('text', 'name'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        wpforms_panel_field(
            'select',
            'settings',
            'last_name',
            $instance->form_data,
            __('Last Name', 'marketing-automation-suite'),
            array(
                'field_map' => array('text', 'name'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'fiscal_code',
            $instance->form_data,
            __('Fiscal Code', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );


        wpforms_panel_field(
            'select',
            'settings',
            'gender',
            $instance->form_data,
            __('Gender', 'marketing-automation-suite'),
            array(
                'field_map' => array('select'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'country',
            $instance->form_data,
            __('Nation/Country ISO CODE', 'marketing-automation-suite'),
            array(
                'field_map' => ['address', 'hidden', 'select', 'checkbox'],
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );


        wpforms_panel_field(
            'select',
            'settings',
            'state',
            $instance->form_data,
            __('Province/State', 'marketing-automation-suite'),
            array(
                'field_map' => array('address', 'text', 'select', ''),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );


        wpforms_panel_field(
            'select',
            'settings',
            'city',
            $instance->form_data,
            __('City', 'marketing-automation-suite'),
            array(
                'field_map' => ['address', 'text'],
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'postal_code',
            $instance->form_data,
            __('Postal Code', 'marketing-automation-suite'),
            array(
                'field_map' => ['address', 'text', 'number'],
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );




        wpforms_panel_field(
            'select',
            'settings',
            'address',
            $instance->form_data,
            __('Address', 'marketing-automation-suite'),
            array(
                'field_map' => ['address', 'text'],
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        /*
          wpforms_panel_field(
            'select',
            'settings',
            'message',
            $instance->form_data,
            __('Message Text', 'marketing-automation-suite'),
            array(
                'field_map' => array('textarea'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/
        wpforms_panel_field(
            'select',
            'settings',
            'telephone_1',
            $instance->form_data,
            __('Telephone 1', 'marketing-automation-suite'),
            array(
                'field_map' => array('phone', 'text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );


        /* wpforms_panel_field(
             'select',
             'settings',
             'telephone_2',
             $instance->form_data,
             __('Telephone 2', 'marketing-automation-suite'),
             array(
                 'field_map' => array('phone', 'text'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/
        wpforms_panel_field(
            'select',
            'settings',
            'mobile_1',
            $instance->form_data,
            __('Mobile phone 1', 'marketing-automation-suite'),
            array(
                'field_map' => array('phone', 'text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        /*wpforms_panel_field(
            'select',
            'settings',
            'mobile_2',
            $instance->form_data,
            __('Mobilephone 2', 'marketing-automation-suite'),
            array(
                'field_map' => array('phone', 'text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/


        /*wpforms_panel_field(
            'select',
            'settings',
            'skype',
            $instance->form_data,
            __('Skype URL Profile', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'linkedin',
            $instance->form_data,
            __('Linkedin URL Profile', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        wpforms_panel_field(
            'select',
            'settings',
            'facebook',
            $instance->form_data,
            __('Facebook URL Profile', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        wpforms_panel_field(
            'select',
            'settings',
            'instagram',
            $instance->form_data,
            __('Instagram URL Profile', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'twitter',
            $instance->form_data,
            __('Twitter URL Profile', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/


        /*wpforms_panel_field(
            'select',
            'settings',
            'zone',
            $instance->form_data,
            __('Zone', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );


        wpforms_panel_field(
            'select',
            'settings',
            'note',
            $instance->form_data,
            __('Note', 'marketing-automation-suite'),
            array(
                'field_map' => array('textarea'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'website',
            $instance->form_data,
            __('Website URL', 'marketing-automation-suite'),
            array(
                'field_map' => array('url'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/


        /*wpforms_panel_field(
            'select',
            'settings',
            'tag_1',
            $instance->form_data,
            __('Person Tag1', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/


        wpforms_panel_field(
            'select',
            'settings',
            'profession',
            $instance->form_data,
            __('Profession', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'company_name',
            $instance->form_data,
            __('Company Name', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'placebirth',
            $instance->form_data,
            __('Birth Place', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'preferred_language',
            $instance->form_data,
            __('Preferred Language', 'marketing-automation-suite'),
            array(
                'field_map' => array('text', 'hidden'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'zone',
            $instance->form_data,
            __('Zone', 'marketing-automation-suite'),
            array(
                'field_map' => array('text', 'checkbox', 'select', 'hidden'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'vat_code',
            $instance->form_data,
            __('Vat Code', 'marketing-automation-suite'),
            array(
                'field_map' => array('text'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );
        wpforms_panel_field(
            'select',
            'settings',
            'datebirth',
            $instance->form_data,
            __('Birthday Date', 'marketing-automation-suite'),
            array(
                'field_map' => array('date-time'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        /* wpforms_panel_field(
             'select',
             'settings',
             'preferred_language',
             $instance->form_data,
             __('Preferred Language', 'marketing-automation-suite'),
             array(
                 'field_map' => array('text'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/


        /* wpforms_panel_field(
             'select',
             'settings',
             'title',
             $instance->form_data,
             __('Title', 'marketing-automation-suite'),
             array(
                 'field_map' => array('text'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/


        /*wpforms_panel_field(
            'select',
            'settings',
            'tag',
            $instance->form_data,
            __('Tag', 'marketing-automation-suite'),
            array(
                'field_map' => array('hidden'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );*/


        /* wpforms_panel_field(
             'select',
             'settings',
             'gdpr_collection',
             $instance->form_data,
             __('GDPR Collection', 'marketing-automation-suite'),
             array(
                 'field_map' => array('checkbox', 'gdpr-checkbox'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );
         wpforms_panel_field(
             'select',
             'settings',
             'gdpr_other1',
             $instance->form_data,
             __('GDPR Other 1', 'marketing-automation-suite'),
             array(
                 'field_map' => array('checkbox', 'gdpr-checkbox'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );
         wpforms_panel_field(
             'select',
             'settings',
             'gdpr_other2',
             $instance->form_data,
             __('GDPR Other 2', 'marketing-automation-suite'),
             array(
                 'field_map' => array('checkbox', 'gdpr-checkbox'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );
         wpforms_panel_field(
             'select',
             'settings',
             'gdpr_other3',
             $instance->form_data,
             __('GDPR Other 3', 'marketing-automation-suite'),
             array(
                 'field_map' => array('checkbox', 'gdpr-checkbox'),
                 'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
             )
         );*/

        /*GDPR PURPOSES*/
        wpforms_panel_field(
            'select',
            'settings',
            'gdpr_marketing',
            $instance->form_data,
            __('GDPR Marketing', 'marketing-automation-suite'),
            array(
                'field_map' => array('checkbox', 'gdpr-checkbox'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'gdpr_profiling',
            $instance->form_data,
            __('GDPR Profiling', 'marketing-automation-suite'),
            array(
                'field_map' => array('checkbox', 'gdpr-checkbox'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'gdpr_thirdparties',
            $instance->form_data,
            __('GDPR Thirdparties', 'marketing-automation-suite'),
            array(
                'field_map' => array('checkbox', 'gdpr-checkbox'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        wpforms_panel_field(
            'select',
            'settings',
            'gdpr_outsideeu',
            $instance->form_data,
            __('GDPR Outside EU', 'marketing-automation-suite'),
            array(
                'field_map' => array('checkbox', 'gdpr-checkbox'),
                'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
            )
        );

        /* FINE GDPR PURPOSES */

        /*TAGS*/
        for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
            wpforms_panel_field(
                'select',
                'settings',
                'tag' . $i,
                $instance->form_data,
                __('Person Tag' . $i, 'marketing-automation-suite'),
                array(
                    'field_map' => array('text', 'select', 'hidden'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

        }

        /*CV*/
        $opt_custom_variables = get_option($GLOBALS['opt_cv_list'], array());
        foreach ($opt_custom_variables as $cv) {
            wpforms_panel_field(
                'select',
                'settings',
                $cv['name'],
                $instance->form_data,
                __('Custom Variable ' . $cv['name'], 'marketing-automation-suite'),
                array(
                    'field_map' => array('text', 'select', 'radio', 'hidden', 'date-time', 'number', 'checkbox'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

        }

        if (!$GLOBALS['company_field_hide']) {

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('fiscal_code'),
                $instance->form_data,
                __('Fiscal Code', 'marketing-automation-suite'),
                array(
                    'field_map' => array('text'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );
            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('state'),
                $instance->form_data,
                __('Nation/Country ISO CODE', 'marketing-automation-suite'),
                array(
                    'field_map' => array('hidden'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('city'),
                $instance->form_data,
                __('City', 'marketing-automation-suite'),
                array(
                    'field_map' => array('address', 'text'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );


            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('postal_code'),
                $instance->form_data,
                __('Postal Code', 'marketing-automation-suite'),
                array(
                    'field_map' => array('address', 'text'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('country'),
                $instance->form_data,
                __('Province/State', 'marketing-automation-suite'),
                array(
                    'field_map' => array('address'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('address'),
                $instance->form_data,
                __('Address', 'marketing-automation-suite'),
                array(
                    'field_map' => array('address', 'text'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );
            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('telephone_1'),
                $instance->form_data,
                __('Telephone 1', 'marketing-automation-suite'),
                array(
                    'field_map' => array('phone', 'text'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('gdpr_marketing'),
                $instance->form_data,
                __('GDPR Marketing', 'marketing-automation-suite'),
                array(
                    'field_map' => array('checkbox', 'gdpr-checkbox'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('gdpr_profiling'),
                $instance->form_data,
                __('GDPR Profiling', 'marketing-automation-suite'),
                array(
                    'field_map' => array('checkbox', 'gdpr-checkbox'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );

            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('gdpr_thirdparties'),
                $instance->form_data,
                __('GDPR Thirdparties', 'marketing-automation-suite'),
                array(
                    'field_map' => array('checkbox', 'gdpr-checkbox'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );
            wpforms_panel_field(
                'select',
                'settings',
                $mas->placeholderCompany('gdpr_outsideeu'),
                $instance->form_data,
                __('GDPR Outside EU', 'marketing-automation-suite'),
                array(
                    'field_map' => array('checkbox', 'gdpr-checkbox'),
                    'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                )
            );
            for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
                wpforms_panel_field(
                    'select',
                    'settings',
                    $mas->placeholderCompany('tag' . $i),
                    $instance->form_data,
                    __('Company Tag' . $mas->placeholderCompany($i), 'marketing-automation-suite'),
                    array(
                        'field_map' => array('text', 'select'),
                        'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                    )
                );

            }

            foreach ($opt_custom_variables as $cv) {
                wpforms_panel_field(
                    'select',
                    'settings',
                    $mas->placeholderCompany($cv['name']),
                    $instance->form_data,
                    __('Custom Variable ' . $mas->placeholderCompany($cv['name']), 'marketing-automation-suite'),
                    array(
                        'field_map' => array('text', 'select', 'radio', 'number'),
                        'placeholder' => __('-- Select Field --', 'marketing-automation-suite'),
                    )
                );
            }

        }

        echo '</div>';
    }


    /**
     * @param $fields
     * @param $entry
     * @param $form_data
     * @param $entry_id
     * @return bool
     * @throws Exception
     */

    public function mas_prepareWpformsDataApiCall($fields, $entry, $form_data, $entry_id): bool
    {
        $email = $fields[$form_data['settings']['email_1']]['value'] ?? null;
        //$contact_code = $fields[$form_data['settings']['contact_code']]['value'] ?? null;

        $timezone = new DateTimeZone(get_option('timezone_string'));
        $now = new DateTime("now", $timezone);
        if (empty($email) && empty($contact_code)) {
            mas_WriteLog('ERROR');
            return false;
        }


        /*$full_address = null;
        if (!empty($fields[$form_data['settings']['address']]['address1'])) {
            $full_address = $fields[$form_data['settings']['address']]['address1'];
        }

        if (!empty($fields[$form_data['settings']['address']]['address2'])) {
            if ($full_address != null) {
                $full_address .= " ";
            }
            $full_address .= $fields[$form_data['settings']['address']]['address2'];
        }

        if ($full_address == null && !empty($fields[$form_data['settings']['address']]['value'])) {
            $full_address = $fields[$form_data['settings']['address']]['value'];
        }*/


        $event_data = array();
        $event_data['primary_key'] = !empty($email) ? 'email' : 'contact_code';
        $event_data['email_1'] = trim(strtolower($email));
        //$event_data['contact_code'] = $fields[$form_data['settings']['contact_code']]['value'] ?? null;
        //$event_data['email_2'] = $fields[$form_data['settings']['email_2']]['value'] ?? null;
        $event_data['first_name'] = $fields[$form_data['settings']['first_name']]['value'] ?? null;
        $event_data['last_name'] = $fields[$form_data['settings']['last_name']]['value'] ?? null;
        $event_data['fiscal_code'] = $fields[$form_data['settings']['fiscal_code']]['value'] ?? null;
        $event_data['gender'] = !empty($fields[$form_data['settings']['gender']]['value']) ? $this->mas_findGender($fields[$form_data['settings']['gender']]['value']) : null;
        if (!empty($fields[$form_data['settings']['datebirth']]['value'])) {
            $event_data['datebirth'] = date("Y-m-d", $fields[$form_data['settings']['datebirth']]['unix']);
        }
        $event_data['state'] = $fields[$form_data['settings']['state']]['value'] ?? null;
        $event_data['city'] = $fields[$form_data['settings']['city']]['value'] ?? null;
        $event_data['postal_code'] = $fields[$form_data['settings']['postal_code']]['value'] ?? null;
        $event_data['country'] = $fields[$form_data['settings']['country']]['value'] ?? null;
        $event_data['address'] = $fields[$form_data['settings']['address']]['value'] ?? null;
        $event_data['telephone_1'] = $fields[$form_data['settings']['telephone_1']]['value'] ?? null;

        $event_data['mobile_1'] = $fields[$form_data['settings']['mobile_1']]['value'] ?? null;;
        $event_data['profession'] = $fields[$form_data['settings']['profession']]['value'] ?? null;
        $event_data['company_name'] = $fields[$form_data['settings']['company_name']]['value'] ?? null;
        $event_data['vat_code'] = $fields[$form_data['settings']['vat_code']]['value'] ?? null;


        $event_data['placebirth'] = $fields[$form_data['settings']['placebirth']]['value'] ?? null;
        $event_data['preferred_language'] = $fields[$form_data['settings']['preferred_language']]['value'] ?? null;
        $event_data['zone'] = $fields[$form_data['settings']['zone']]['value'] ?? null;

        for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
            if (isset($form_data['settings']['tag' . $i])) {
                if (!empty($fields[$form_data['settings']['tag' . $i]]['value'])) {
                    $event_data['tags'][]['name'] = $fields[$form_data['settings']['tag' . $i]]['value'];
                }
            }
        }


        if (isset($fields[$form_data['settings']['gdpr_marketing']])) {
            $event_data['gdpr_marketing'] = !empty($fields[$form_data['settings']['gdpr_marketing']]['value']);
            $event_data['gdpr_marketing_date'] = $now->format("Y-m-d");
        }


        if (isset($fields[$form_data['settings']['gdpr_profiling']])) {
            $event_data['gdpr_profiling'] = !empty($fields[$form_data['settings']['gdpr_profiling']]['value']);
            $event_data['gdpr_profiling_date'] = $now->format("Y-m-d");
        }


        if (isset($fields[$form_data['settings']['gdpr_thirdparties']])) {
            $event_data['gdpr_thirdparties'] = !empty($fields[$form_data['settings']['gdpr_thirdparties']]['value']);
            $event_data['gdpr_thirdparties_date'] = $now->format("Y-m-d");
        }


        if (isset($fields[$form_data['settings']['gdpr_outsideeu']])) {
            $event_data['gdpr_outsideeu'] = !empty($fields[$form_data['settings']['gdpr_outsideeu']]['value']);
            $event_data['gdpr_outsideeu_date'] = $now->format("Y-m-d");
        }

        $opt_custom_variables = get_option($GLOBALS['opt_cv_list'], array());

        foreach ($opt_custom_variables as $cv) {
            if (isset($fields[$form_data['settings'][$cv['name']]])) {
                if (!empty($fields[$form_data['settings'][$cv['name']]]['value'])) {
                    if ($fields[$form_data['settings'][$cv['name']]]['type'] == 'date-time' || $fields[$form_data['settings'][$cv['name']]]['type'] == 'date') {
                        $value = date("Y-m-d", $fields[$form_data['settings'][$cv['name']]]['unix']);
                    } else {
                        $value = $fields[$form_data['settings'][$cv['name']]]['value'];
                    }
                    if ($cv['type'] == 'multivalue') {
                        $value = $entry['fields'][$form_data['settings'][$cv['name']]];
                    }
                    $event_data['custom_variables'][] = ['name' => substr($cv['name'], strlen($GLOBALS['cv_placeholder'])), 'value' => $value];
                }
            }
        }


        $post_meta_ma_directory_form = get_post_meta($entry['id'], $GLOBALS['post_meta_ma_directory_form'], true);
        $db = json_decode($post_meta_ma_directory_form, true);

        $event_data['form_name'] = $db['sync_status'] ? $db['sync_data']['form'] ?? '' : '';


        return mas_eventSave($event_data);

        //return mas_personSave($event_data);

    }
}


?>