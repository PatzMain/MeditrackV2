<?php
return [
    $GLOBALS['tableConfig'] = [
        'medical_medicines' => [
            'table_name' => 'medical_medicines',
            'primary_key' => 'medicine_id',
            'display_name' => 'Medical Medicines',
            'icon' => 'fa-pills',
            'columns' => [
                'medicine_id' => [
                    'value' => $stats['medicine_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'medicine_name' => [
                    'value' => $stats['medicine_name'] ?? '',
                    'label' => 'Medicine Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '20%',
                    'class' => 'medicine-name'
                ],
                'medicine_brand_name' => [
                    'value' => $stats['medicine_brand_name'] ?? '',
                    'label' => 'Brand Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'medicine_generic_name' => [
                    'value' => $stats['medicine_generic_name'] ?? '',
                    'label' => 'Generic Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'medicine_classification' => [
                    'value' => $stats['medicine_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'options' => [
                        'Antibiotic' => 'Antibiotic',
                        'Analgesic' => 'Analgesic',
                        'Antipyretic' => 'Antipyretic',
                        'Antihistamine' => 'Antihistamine',
                        'Antiseptic' => 'Antiseptic',
                        'Antifungal' => 'Antifungal',
                        'Antiviral' => 'Antiviral',
                        'Vaccine' => 'Vaccine',
                        'Supplement' => 'Supplement',
                        'Cough Suppressant' => 'Cough Suppressant',
                        'Decongestant' => 'Decongestant',
                        'Anti-inflammatory' => 'Anti-inflammatory',
                        'Antacid' => 'Antacid',
                        'Laxative' => 'Laxative',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Antibiotic' => 'danger',
                        'Analgesic' => 'primary',
                        'Antipyretic' => 'warning',
                        'Antihistamine' => 'info',
                        'Antiseptic' => 'success',
                        'Antifungal' => 'secondary',
                        'Antiviral' => 'purple',
                        'Vaccine' => 'pink',
                        'Supplement' => 'green',
                        'Other' => 'dark'
                    ]
                ],
                'medicine_dosage' => [
                    'value' => $stats['medicine_dosage'] ?? '',
                    'label' => 'Dosage',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'medicine_unit' => [
                    'value' => $stats['medicine_unit'] ?? '',
                    'label' => 'Unit',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'medicine_stock' => [
                    'value' => $stats['medicine_stock'] ?? '',
                    'label' => 'Stock',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'class' => 'text-center',
                    'format' => 'stock_status',
                    'inline_edit' => true,
                    'low_stock_threshold' => 20
                ],
                'medicine_expiry_date' => [
                    'value' => $stats['medicine_expiry_date'] ?? '',
                    'label' => 'Expiry Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'format' => 'expiry_status'
                ],
                'medicine_description' => [
                    'value' => $stats['medicine_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this medical medicine?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'medicine_classification'
                ],
                'stock_status' => [
                    'label' => 'Stock Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock'
                    ]
                ],
                'expiry_status' => [
                    'label' => 'Expiry Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'valid' => 'Valid',
                        'expiring_soon' => 'Expiring Soon (30 days)',
                        'expired' => 'Expired'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'medicine_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'dental_medicines' => [
            'table_name' => 'dental_medicines',
            'primary_key' => 'medicine_id',
            'display_name' => 'Dental Medicines',
            'icon' => 'fa-tooth',
            'columns' => [
                'medicine_id' => [
                    'value' => $stats['medicine_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'medicine_name' => [
                    'value' => $stats['medicine_name'] ?? '',
                    'label' => 'Medicine Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '20%',
                    'class' => 'medicine-name'
                ],
                'medicine_brand_name' => [
                    'value' => $stats['medicine_brand_name'] ?? '',
                    'label' => 'Brand Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'medicine_generic_name' => [
                    'value' => $stats['medicine_generic_name'] ?? '',
                    'label' => 'Generic Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'medicine_classification' => [
                    'value' => $stats['medicine_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'options' => [
                        'Fluoride Treatment' => 'Fluoride Treatment',
                        'Local Anesthetic' => 'Local Anesthetic',
                        'Desensitizing Agent' => 'Desensitizing Agent',
                        'Dental Antibiotic' => 'Dental Antibiotic',
                        'Mouth Rinse' => 'Mouth Rinse',
                        'Topical Analgesic' => 'Topical Analgesic',
                        'Oral Antifungal' => 'Oral Antifungal',
                        'Gingivitis Treatment' => 'Gingivitis Treatment',
                        'Periodontitis Treatment' => 'Periodontitis Treatment',
                        'Dry Mouth Treatment' => 'Dry Mouth Treatment',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Fluoride Treatment' => 'info',
                        'Local Anesthetic' => 'warning',
                        'Desensitizing Agent' => 'secondary',
                        'Dental Antibiotic' => 'danger',
                        'Mouth Rinse' => 'primary',
                        'Topical Analgesic' => 'success',
                        'Oral Antifungal' => 'purple',
                        'Gingivitis Treatment' => 'pink',
                        'Periodontitis Treatment' => 'orange',
                        'Dry Mouth Treatment' => 'cyan',
                        'Other' => 'dark'
                    ]
                ],
                'medicine_dosage' => [
                    'value' => $stats['medicine_dosage'] ?? '',
                    'label' => 'Dosage',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'medicine_unit' => [
                    'value' => $stats['medicine_unit'] ?? '',
                    'label' => 'Unit',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'medicine_stock' => [
                    'value' => $stats['medicine_stock'] ?? '',
                    'label' => 'Stock',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'class' => 'text-center',
                    'format' => 'stock_status',
                    'inline_edit' => true,
                    'low_stock_threshold' => 15
                ],
                'medicine_expiry_date' => [
                    'value' => $stats['medicine_expiry_date'] ?? '',
                    'label' => 'Expiry Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'format' => 'expiry_status'
                ],
                'medicine_description' => [
                    'value' => $stats['medicine_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this dental medicine?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'medicine_classification'
                ],
                'stock_status' => [
                    'label' => 'Stock Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock'
                    ]
                ],
                'expiry_status' => [
                    'label' => 'Expiry Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'valid' => 'Valid',
                        'expiring_soon' => 'Expiring Soon (30 days)',
                        'expired' => 'Expired'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'medicine_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'medical_supplies' => [
            'table_name' => 'medical_supplies',
            'primary_key' => 'supply_id',
            'display_name' => 'Medical Supplies',
            'icon' => 'fa-box-medical',
            'columns' => [
                'supply_id' => [
                    'value' => $stats['supply_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'supply_name' => [
                    'value' => $stats['supply_name'] ?? '',
                    'label' => 'Supply Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '25%',
                    'class' => 'supply-name'
                ],
                'supply_brand_name' => [
                    'value' => $stats['supply_brand_name'] ?? '',
                    'label' => 'Brand Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'supply_classification' => [
                    'value' => $stats['supply_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%',
                    'options' => [
                        'Syringe' => 'Syringe',
                        'Gloves' => 'Gloves',
                        'Bandage' => 'Bandage',
                        'Cotton' => 'Cotton',
                        'Alcohol Swab' => 'Alcohol Swab',
                        'Face Mask' => 'Face Mask',
                        'IV Set' => 'IV Set',
                        'Thermometer Cover' => 'Thermometer Cover',
                        'Disinfectant' => 'Disinfectant',
                        'Protective Gown' => 'Protective Gown',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Syringe' => 'primary',
                        'Gloves' => 'success',
                        'Bandage' => 'info',
                        'Cotton' => 'secondary',
                        'Alcohol Swab' => 'warning',
                        'Face Mask' => 'danger',
                        'IV Set' => 'purple',
                        'Thermometer Cover' => 'pink',
                        'Disinfectant' => 'orange',
                        'Protective Gown' => 'cyan',
                        'Other' => 'dark'
                    ]
                ],
                'supply_quantity' => [
                    'value' => $stats['supply_quantity'] ?? '',
                    'label' => 'Quantity',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'class' => 'text-center',
                    'format' => 'stock_status',
                    'inline_edit' => true,
                    'low_stock_threshold' => 50
                ],
                'supply_unit' => [
                    'value' => $stats['supply_unit'] ?? '',
                    'label' => 'Unit',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'supply_expiry_date' => [
                    'value' => $stats['supply_expiry_date'] ?? '',
                    'label' => 'Expiry Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'format' => 'expiry_status'
                ],
                'supply_description' => [
                    'value' => $stats['supply_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this medical supply?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'supply_classification'
                ],
                'stock_status' => [
                    'label' => 'Stock Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock'
                    ]
                ],
                'expiry_status' => [
                    'label' => 'Expiry Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'valid' => 'Valid',
                        'expiring_soon' => 'Expiring Soon (30 days)',
                        'expired' => 'Expired'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'supply_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'dental_supplies' => [
            'table_name' => 'dental_supplies',
            'primary_key' => 'supply_id',
            'display_name' => 'Dental Supplies',
            'icon' => 'fa-tooth',
            'columns' => [
                'supply_id' => [
                    'value' => $stats['supply_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'supply_name' => [
                    'value' => $stats['supply_name'] ?? '',
                    'label' => 'Supply Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '25%',
                    'class' => 'supply-name'
                ],
                'supply_brand_name' => [
                    'value' => $stats['supply_brand_name'] ?? '',
                    'label' => 'Brand Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'supply_classification' => [
                    'value' => $stats['supply_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%',
                    'options' => [
                        'Dental Bib' => 'Dental Bib',
                        'Cotton Roll' => 'Cotton Roll',
                        'Dental Floss' => 'Dental Floss',
                        'Dental Impression Material' => 'Dental Impression Material',
                        'Saliva Ejector' => 'Saliva Ejector',
                        'Dental Tray Cover' => 'Dental Tray Cover',
                        'Disinfectant' => 'Disinfectant',
                        'Protective Gown' => 'Protective Gown',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Dental Bib' => 'primary',
                        'Cotton Roll' => 'secondary',
                        'Dental Floss' => 'success',
                        'Dental Impression Material' => 'info',
                        'Saliva Ejector' => 'warning',
                        'Dental Tray Cover' => 'purple',
                        'Disinfectant' => 'orange',
                        'Protective Gown' => 'cyan',
                        'Other' => 'dark'
                    ]
                ],
                'supply_quantity' => [
                    'value' => $stats['supply_quantity'] ?? '',
                    'label' => 'Quantity',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'class' => 'text-center',
                    'format' => 'stock_status',
                    'inline_edit' => true,
                    'low_stock_threshold' => 30
                ],
                'supply_unit' => [
                    'value' => $stats['supply_unit'] ?? '',
                    'label' => 'Unit',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'supply_expiry_date' => [
                    'value' => $stats['supply_expiry_date'] ?? '',
                    'label' => 'Expiry Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%',
                    'format' => 'expiry_status'
                ],
                'supply_description' => [
                    'value' => $stats['supply_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this dental supply?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'supply_classification'
                ],
                'stock_status' => [
                    'label' => 'Stock Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock'
                    ]
                ],
                'expiry_status' => [
                    'label' => 'Expiry Status',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        'valid' => 'Valid',
                        'expiring_soon' => 'Expiring Soon (30 days)',
                        'expired' => 'Expired'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'supply_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'medical_equipment' => [
            'table_name' => 'medical_equipment',
            'primary_key' => 'equipment_id',
            'display_name' => 'Medical Equipment',
            'icon' => 'fa-stethoscope',
            'columns' => [
                'equipment_id' => [
                    'value' => $stats['equipment_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'equipment_name' => [
                    'value' => $stats['equipment_name'] ?? '',
                    'label' => 'Equipment Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '20%',
                    'class' => 'equipment-name'
                ],
                'serial_number' => [
                    'value' => $stats['serial_number'] ?? '',
                    'label' => 'Serial Number',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'equipment_classification' => [
                    'value' => $stats['equipment_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%',
                    'options' => [
                        'Stethoscope' => 'Stethoscope',
                        'Blood Pressure Monitor' => 'Blood Pressure Monitor',
                        'Otoscope' => 'Otoscope',
                        'Weighing Scale' => 'Weighing Scale',
                        'Nebulizer' => 'Nebulizer',
                        'Sterilizer/Autoclave' => 'Sterilizer/Autoclave',
                        'Examination Light' => 'Examination Light',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Stethoscope' => 'primary',
                        'Blood Pressure Monitor' => 'danger',
                        'Otoscope' => 'info',
                        'Weighing Scale' => 'success',
                        'Nebulizer' => 'warning',
                        'Sterilizer/Autoclave' => 'purple',
                        'Examination Light' => 'orange',
                        'Other' => 'dark'
                    ]
                ],
                'equipment_condition' => [
                    'value' => $stats['equipment_condition'] ?? '',
                    'label' => 'Condition',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'equipment_location' => [
                    'value' => $stats['equipment_location'] ?? '',
                    'label' => 'Location',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%'
                ],
                'remarks' => [
                    'value' => $stats['remarks'] ?? '',
                    'label' => 'Remarks',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'equipment_description' => [
                    'value' => $stats['equipment_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this medical equipment?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'equipment_classification'
                ],
                'condition' => [
                    'label' => 'Condition',
                    'type' => 'text'
                ],
                'location' => [
                    'label' => 'Location',
                    'type' => 'text'
                ]
            ],
            'default_sort' => [
                'column' => 'equipment_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'dental_equipment' => [
            'table_name' => 'dental_equipment',
            'primary_key' => 'equipment_id',
            'display_name' => 'Dental Equipment',
            'icon' => 'fa-tooth',
            'columns' => [
                'equipment_id' => [
                    'value' => $stats['equipment_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'equipment_name' => [
                    'value' => $stats['equipment_name'] ?? '',
                    'label' => 'Equipment Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '20%',
                    'class' => 'equipment-name'
                ],
                'serial_number' => [
                    'value' => $stats['serial_number'] ?? '',
                    'label' => 'Serial Number',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%'
                ],
                'equipment_classification' => [
                    'value' => $stats['equipment_classification'] ?? '',
                    'label' => 'Classification',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '15%',
                    'options' => [
                        'Dental Chair' => 'Dental Chair',
                        'Dental Drill' => 'Dental Drill',
                        'Ultrasonic Scaler' => 'Ultrasonic Scaler',
                        'Curing Light' => 'Curing Light',
                        'X-ray Machine' => 'X-ray Machine',
                        'Sterilizer/Autoclave' => 'Sterilizer/Autoclave',
                        'Examination Light' => 'Examination Light',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'Dental Chair' => 'primary',
                        'Dental Drill' => 'warning',
                        'Ultrasonic Scaler' => 'info',
                        'Curing Light' => 'success',
                        'X-ray Machine' => 'danger',
                        'Sterilizer/Autoclave' => 'purple',
                        'Examination Light' => 'orange',
                        'Other' => 'dark'
                    ]
                ],
                'equipment_condition' => [
                    'value' => $stats['equipment_condition'] ?? '',
                    'label' => 'Condition',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'equipment_location' => [
                    'value' => $stats['equipment_location'] ?? '',
                    'label' => 'Location',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '12%'
                ],
                'remarks' => [
                    'value' => $stats['remarks'] ?? '',
                    'label' => 'Remarks',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'equipment_description' => [
                    'value' => $stats['equipment_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this dental equipment?'
                ],
            ],
            'filters' => [
                'classification' => [
                    'label' => 'Classification',
                    'type' => 'select',
                    'options' => 'equipment_classification'
                ],
                'condition' => [
                    'label' => 'Condition',
                    'type' => 'text'
                ],
                'location' => [
                    'label' => 'Location',
                    'type' => 'text'
                ]
            ],
            'default_sort' => [
                'column' => 'equipment_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'patients' => [
            'table_name' => 'patients',
            'primary_key' => 'patient_id',
            'display_name' => 'Patients',
            'icon' => 'fa-user-injured',
            'columns' => [
                'patient_id' => [
                    'value' => $stats['patient_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'patient_number' => [
                    'value' => $stats['patient_number'] ?? '',
                    'label' => 'Patient Number',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '12%',
                    'class' => 'patient-number'
                ],
                'first_name' => [
                    'value' => $stats['first_name'] ?? '',
                    'label' => 'First Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '15%'
                ],
                'last_name' => [
                    'value' => $stats['last_name'] ?? '',
                    'label' => 'Last Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '15%'
                ],
                'date_of_birth' => [
                    'value' => $stats['date_of_birth'] ?? '',
                    'label' => 'Date of Birth',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%'
                ],
                'gender' => [
                    'value' => $stats['gender'] ?? '',
                    'label' => 'Gender',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '8%',
                    'options' => [
                        'M' => 'Male',
                        'F' => 'Female',
                        'Other' => 'Other'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'M' => 'primary',
                        'F' => 'danger',
                        'Other' => 'secondary'
                    ]
                ],
                'blood_group' => [
                    'value' => $stats['blood_group'] ?? '',
                    'label' => 'Blood Group',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'phone' => [
                    'value' => $stats['phone'] ?? '',
                    'label' => 'Phone',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'email' => [
                    'value' => $stats['email'] ?? '',
                    'label' => 'Email',
                    'type' => 'email',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'student_type' => [
                    'value' => $stats['student_type'] ?? '',
                    'label' => 'Student Type',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%',
                    'options' => [
                        'College' => 'College',
                        'LSHS' => 'LSHS'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'College' => 'success',
                        'LSHS' => 'info'
                    ]
                ],
                'course' => [
                    'value' => $stats['course'] ?? '',
                    'label' => 'Course',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'year_level' => [
                    'value' => $stats['year_level'] ?? '',
                    'label' => 'Year Level',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'grade_level' => [
                    'value' => $stats['grade_level'] ?? '',
                    'label' => 'Grade Level',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'strand' => [
                    'value' => $stats['strand'] ?? '',
                    'label' => 'Strand',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'patient_address' => [
                    'value' => $stats['patient_address'] ?? '',
                    'label' => 'Address',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'emergency_contact_name' => [
                    'value' => $stats['emergency_contact_name'] ?? '',
                    'label' => 'Emergency Contact',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'emergency_contact_phone' => [
                    'value' => $stats['emergency_contact_phone'] ?? '',
                    'label' => 'Emergency Phone',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'allergies' => [
                    'value' => $stats['allergies'] ?? '',
                    'label' => 'Allergies',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'medical_conditions' => [
                    'value' => $stats['medical_conditions'] ?? '',
                    'label' => 'Medical Conditions',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'admission_date' => [
                    'value' => $stats['admission_date'] ?? '',
                    'label' => 'Admission Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'discharge_date' => [
                    'value' => $stats['discharge_date'] ?? '',
                    'label' => 'Discharge Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'patient_status' => [
                    'value' => $stats['patient_status'] ?? '',
                    'label' => 'Status',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%',
                    'options' => [
                        'admitted' => 'Admitted',
                        'discharged' => 'Discharged',
                        'deceased' => 'Deceased',
                        'transferred' => 'Transferred'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'admitted' => 'success',
                        'discharged' => 'primary',
                        'deceased' => 'dark',
                        'transferred' => 'warning'
                    ]
                ],
                'room_id' => [
                    'value' => $stats['room_id'] ?? '',
                    'label' => 'Room ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'bed_id' => [
                    'value' => $stats['bed_id'] ?? '',
                    'label' => 'Bed ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ],
                'updated_at' => [
                    'value' => $stats['updated_at'] ?? '',
                    'label' => 'Updated',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false,
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'vitals' => [
                    'label' => 'Vitals',
                    'icon' => 'fa-heartbeat',
                    'class' => 'btn-success btn-sm',
                    'action' => 'redirect',
                    'url' => 'vitals.php?patient_id='
                ],
                'assessment' => [
                    'label' => 'Assessment',
                    'icon' => 'fa-clipboard-check',
                    'class' => 'btn-primary btn-sm',
                    'action' => 'redirect',
                    'url' => 'assessments.php?patient_id='
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this patient record?'
                ],
            ],
            'filters' => [
                'student_type' => [
                    'label' => 'Student Type',
                    'type' => 'select',
                    'options' => 'student_type'
                ],
                'gender' => [
                    'label' => 'Gender',
                    'type' => 'select',
                    'options' => 'gender'
                ],
                'patient_status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'options' => 'patient_status'
                ],
                'blood_group' => [
                    'label' => 'Blood Group',
                    'type' => 'text'
                ]
            ],
            'default_sort' => [
                'column' => 'last_name',
                'direction' => 'ASC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => true,
            'enable_pagination' => true
        ],

        'vital_signs' => [
            'table_name' => 'vital_signs',
            'primary_key' => 'vital_id',
            'display_name' => 'Vital Signs',
            'icon' => 'fa-heartbeat',
            'columns' => [
                'vital_id' => [
                    'value' => $stats['vital_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'patient_id' => [
                    'value' => $stats['patient_id'] ?? '',
                    'label' => 'Patient ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%'
                ],
                'systolic_bp' => [
                    'value' => $stats['systolic_bp'] ?? '',
                    'label' => 'Systolic BP',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'diastolic_bp' => [
                    'value' => $stats['diastolic_bp'] ?? '',
                    'label' => 'Diastolic BP',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'heart_rate' => [
                    'value' => $stats['heart_rate'] ?? '',
                    'label' => 'Heart Rate',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'respiratory_rate' => [
                    'value' => $stats['respiratory_rate'] ?? '',
                    'label' => 'Respiratory Rate',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'temperature' => [
                    'value' => $stats['temperature'] ?? '',
                    'label' => 'Temperature',
                    'type' => 'decimal',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'temperature_unit' => [
                    'value' => $stats['temperature_unit'] ?? 'C',
                    'label' => 'Temp Unit',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'options' => [
                        'C' => 'Celsius',
                        'F' => 'Fahrenheit'
                    ]
                ],
                'oxygen_saturation' => [
                    'value' => $stats['oxygen_saturation'] ?? '',
                    'label' => 'Oxygen Sat',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'blood_glucose' => [
                    'value' => $stats['blood_glucose'] ?? '',
                    'label' => 'Blood Glucose',
                    'type' => 'decimal',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'v_weight' => [
                    'value' => $stats['v_weight'] ?? '',
                    'label' => 'Weight',
                    'type' => 'decimal',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'v_height' => [
                    'value' => $stats['v_height'] ?? '',
                    'label' => 'Height',
                    'type' => 'decimal',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%'
                ],
                'pain_scale' => [
                    'value' => $stats['pain_scale'] ?? '',
                    'label' => 'Pain Scale',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'min' => 0,
                    'max' => 10
                ],
                'consciousness_level' => [
                    'value' => $stats['consciousness_level'] ?? 'alert',
                    'label' => 'Consciousness',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%',
                    'options' => [
                        'alert' => 'Alert',
                        'drowsy' => 'Drowsy',
                        'unconscious' => 'Unconscious',
                        'confused' => 'Confused'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'alert' => 'success',
                        'drowsy' => 'warning',
                        'unconscious' => 'danger',
                        'confused' => 'secondary'
                    ]
                ],
                'notes' => [
                    'value' => $stats['notes'] ?? '',
                    'label' => 'Notes',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'recorded_at' => [
                    'value' => $stats['recorded_at'] ?? '',
                    'label' => 'Recorded At',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => false,
                    'width' => '12%',
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this vital signs record?'
                ],
            ],
            'filters' => [
                'patient_id' => [
                    'label' => 'Patient ID',
                    'type' => 'text'
                ],
                'consciousness_level' => [
                    'label' => 'Consciousness',
                    'type' => 'select',
                    'options' => 'consciousness_level'
                ],
                'temperature_unit' => [
                    'label' => 'Temperature Unit',
                    'type' => 'select',
                    'options' => 'temperature_unit'
                ]
            ],
            'default_sort' => [
                'column' => 'recorded_at',
                'direction' => 'DESC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => false,
            'enable_pagination' => true
        ],

        'medical_assessments' => [
            'table_name' => 'medical_assessments',
            'primary_key' => 'assessment_id',
            'display_name' => 'Medical Assessments',
            'icon' => 'fa-clipboard-check',
            'columns' => [
                'assessment_id' => [
                    'value' => $stats['assessment_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'patient_id' => [
                    'value' => $stats['patient_id'] ?? '',
                    'label' => 'Patient ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%'
                ],
                'assessment_type' => [
                    'value' => $stats['assessment_type'] ?? '',
                    'label' => 'Type',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '12%',
                    'options' => [
                        'initial' => 'Initial',
                        'daily_round' => 'Daily Round',
                        'consultation' => 'Consultation',
                        'discharge' => 'Discharge',
                        'emergency' => 'Emergency'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'initial' => 'primary',
                        'daily_round' => 'info',
                        'consultation' => 'success',
                        'discharge' => 'secondary',
                        'emergency' => 'danger'
                    ]
                ],
                'chief_complaint' => [
                    'value' => $stats['chief_complaint'] ?? '',
                    'label' => 'Chief Complaint',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '20%'
                ],
                'present_illness' => [
                    'value' => $stats['present_illness'] ?? '',
                    'label' => 'Present Illness',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'physical_examination' => [
                    'value' => $stats['physical_examination'] ?? '',
                    'label' => 'Physical Exam',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'diagnosis' => [
                    'value' => $stats['diagnosis'] ?? '',
                    'label' => 'Diagnosis',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '20%'
                ],
                'treatment_plan' => [
                    'value' => $stats['treatment_plan'] ?? '',
                    'label' => 'Treatment Plan',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'prognosis' => [
                    'value' => $stats['prognosis'] ?? '',
                    'label' => 'Prognosis',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'follow_up_required' => [
                    'value' => $stats['follow_up_required'] ?? false,
                    'label' => 'Follow Up Required',
                    'type' => 'boolean',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'follow_up_date' => [
                    'value' => $stats['follow_up_date'] ?? '',
                    'label' => 'Follow Up Date',
                    'type' => 'date',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => true
                ],
                'priority_level' => [
                    'value' => $stats['priority_level'] ?? 'medium',
                    'label' => 'Priority',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%',
                    'options' => [
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'low' => 'success',
                        'medium' => 'info',
                        'high' => 'warning',
                        'critical' => 'danger'
                    ]
                ],
                'assessment_date' => [
                    'value' => $stats['assessment_date'] ?? '',
                    'label' => 'Assessment Date',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => false,
                    'width' => '12%',
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this assessment?'
                ],
            ],
            'filters' => [
                'patient_id' => [
                    'label' => 'Patient ID',
                    'type' => 'text'
                ],
                'assessment_type' => [
                    'label' => 'Assessment Type',
                    'type' => 'select',
                    'options' => 'assessment_type'
                ],
                'priority_level' => [
                    'label' => 'Priority Level',
                    'type' => 'select',
                    'options' => 'priority_level'
                ],
                'follow_up_required' => [
                    'label' => 'Follow Up Required',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        '1' => 'Yes',
                        '0' => 'No'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'assessment_date',
                'direction' => 'DESC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => false,
            'enable_pagination' => true
        ],

        'nursing_notes' => [
            'table_name' => 'nursing_notes',
            'primary_key' => 'note_id',
            'display_name' => 'Nursing Notes',
            'icon' => 'fa-notes-medical',
            'columns' => [
                'note_id' => [
                    'value' => $stats['note_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'patient_id' => [
                    'value' => $stats['patient_id'] ?? '',
                    'label' => 'Patient ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%'
                ],
                'nursing_shift' => [
                    'value' => $stats['nursing_shift'] ?? '',
                    'label' => 'Shift',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '10%',
                    'options' => [
                        'morning' => 'Morning',
                        'afternoon' => 'Afternoon',
                        'night' => 'Night'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'morning' => 'warning',
                        'afternoon' => 'info',
                        'night' => 'dark'
                    ]
                ],
                'nursing_note_type' => [
                    'value' => $stats['nursing_note_type'] ?? '',
                    'label' => 'Note Type',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '12%',
                    'options' => [
                        'assessment' => 'Assessment',
                        'care_plan' => 'Care Plan',
                        'medication' => 'Medication',
                        'incident' => 'Incident',
                        'discharge_prep' => 'Discharge Prep',
                        'general' => 'General'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'assessment' => 'primary',
                        'care_plan' => 'success',
                        'medication' => 'warning',
                        'incident' => 'danger',
                        'discharge_prep' => 'info',
                        'general' => 'secondary'
                    ]
                ],
                'nursing_note_content' => [
                    'value' => $stats['nursing_note_content'] ?? '',
                    'label' => 'Note Content',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'required' => true,
                    'width' => '30%'
                ],
                'nursing_action_taken' => [
                    'value' => $stats['nursing_action_taken'] ?? '',
                    'label' => 'Action Taken',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => true
                ],
                'nursing_follow_up_needed' => [
                    'value' => $stats['nursing_follow_up_needed'] ?? false,
                    'label' => 'Follow Up Needed',
                    'type' => 'boolean',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => true,
                    'width' => '10%'
                ],
                'nursing_priority' => [
                    'value' => $stats['nursing_priority'] ?? 'medium',
                    'label' => 'Priority',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => true,
                    'width' => '8%',
                    'options' => [
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger'
                    ]
                ],
                'created_at' => [
                    'value' => $stats['created_at'] ?? '',
                    'label' => 'Created',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => false,
                    'width' => '12%',
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ],
                'edit' => [
                    'label' => 'Edit',
                    'icon' => 'fa-edit',
                    'class' => 'btn-warning btn-sm',
                    'modal' => true
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'fa-archive',
                    'class' => 'btn-secondary btn-sm',
                    'confirm' => true,
                    'confirm_message' => 'Are you sure you want to archive this nursing note?'
                ],
            ],
            'filters' => [
                'patient_id' => [
                    'label' => 'Patient ID',
                    'type' => 'text'
                ],
                'nursing_shift' => [
                    'label' => 'Shift',
                    'type' => 'select',
                    'options' => 'nursing_shift'
                ],
                'nursing_note_type' => [
                    'label' => 'Note Type',
                    'type' => 'select',
                    'options' => 'nursing_note_type'
                ],
                'nursing_priority' => [
                    'label' => 'Priority',
                    'type' => 'select',
                    'options' => 'nursing_priority'
                ],
                'nursing_follow_up_needed' => [
                    'label' => 'Follow Up Needed',
                    'type' => 'select',
                    'options' => [
                        'all' => 'All',
                        '1' => 'Yes',
                        '0' => 'No'
                    ]
                ]
            ],
            'default_sort' => [
                'column' => 'created_at',
                'direction' => 'DESC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => false,
            'enable_pagination' => true
        ],

        'activity_logs' => [
            'table_name' => 'activity_logs',
            'primary_key' => 'log_id',
            'display_name' => 'Activity Logs',
            'icon' => 'fa-history',
            'columns' => [
                'log_id' => [
                    'value' => $stats['log_id'] ?? '',
                    'label' => 'ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'user_id' => [
                    'value' => $stats['user_id'] ?? '',
                    'label' => 'User ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '8%'
                ],
                'patient_id' => [
                    'value' => $stats['patient_id'] ?? '',
                    'label' => 'Patient ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '8%'
                ],
                'logs_item_type' => [
                    'value' => $stats['logs_item_type'] ?? '',
                    'label' => 'Item Type',
                    'type' => 'enum',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '12%',
                    'options' => [
                        'medical_medicine' => 'Medical Medicine',
                        'dental_medicine' => 'Dental Medicine',
                        'medical_supply' => 'Medical Supply',
                        'dental_supply' => 'Dental Supply',
                        'medical_equipment' => 'Medical Equipment',
                        'dental_equipment' => 'Dental Equipment',
                        'authentication' => 'Authentication',
                        'system' => 'System',
                        'patient' => 'Patient',
                        'vital_signs' => 'Vital Signs',
                        'medication_admin' => 'Medication Admin',
                        'lab_test' => 'Lab Test',
                        'assessment' => 'Assessment'
                    ],
                    'badge' => true,
                    'badge_colors' => [
                        'medical_medicine' => 'primary',
                        'dental_medicine' => 'info',
                        'medical_supply' => 'success',
                        'dental_supply' => 'warning',
                        'medical_equipment' => 'danger',
                        'dental_equipment' => 'purple',
                        'authentication' => 'dark',
                        'system' => 'secondary',
                        'patient' => 'pink',
                        'vital_signs' => 'orange',
                        'medication_admin' => 'cyan',
                        'lab_test' => 'indigo',
                        'assessment' => 'teal'
                    ]
                ],
                'logs_item_id' => [
                    'value' => $stats['logs_item_id'] ?? '',
                    'label' => 'Item ID',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '8%'
                ],
                'logs_item_name' => [
                    'value' => $stats['logs_item_name'] ?? '',
                    'label' => 'Item Name',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '15%'
                ],
                'logs_description' => [
                    'value' => $stats['logs_description'] ?? '',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'sortable' => false,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '20%'
                ],
                'logs_reason' => [
                    'value' => $stats['logs_reason'] ?? '',
                    'label' => 'Reason',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => false,
                    'editable' => false
                ],
                'logs_quantity' => [
                    'value' => $stats['logs_quantity'] ?? '',
                    'label' => 'Quantity',
                    'type' => 'integer',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => false,
                    'editable' => false
                ],
                'logs_status' => [
                    'value' => $stats['logs_status'] ?? '',
                    'label' => 'Status',
                    'type' => 'text',
                    'sortable' => true,
                    'searchable' => true,
                    'visible' => true,
                    'editable' => false,
                    'width' => '10%'
                ],
                'logs_timestamp' => [
                    'value' => $stats['logs_timestamp'] ?? '',
                    'label' => 'Timestamp',
                    'type' => 'datetime',
                    'sortable' => true,
                    'searchable' => false,
                    'visible' => true,
                    'editable' => false,
                    'width' => '12%',
                    'format' => 'datetime'
                ]
            ],
            'actions' => [
                'view' => [
                    'label' => 'View',
                    'icon' => 'fa-eye',
                    'class' => 'btn-info btn-sm',
                    'modal' => true
                ]
            ],
            'filters' => [
                'user_id' => [
                    'label' => 'User ID',
                    'type' => 'text'
                ],
                'patient_id' => [
                    'label' => 'Patient ID',
                    'type' => 'text'
                ],
                'logs_item_type' => [
                    'label' => 'Item Type',
                    'type' => 'select',
                    'options' => 'logs_item_type'
                ],
                'logs_status' => [
                    'label' => 'Status',
                    'type' => 'text'
                ]
            ],
            'default_sort' => [
                'column' => 'logs_timestamp',
                'direction' => 'DESC'
            ],
            'items_per_page' => [10, 25, 50, 100],
            'default_items_per_page' => 25,
            'enable_search' => true,
            'enable_filters' => true,
            'enable_export' => true,
            'enable_import' => false,
            'enable_pagination' => true
        ]
    ]
];
?>