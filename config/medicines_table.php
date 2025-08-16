<?php
return [
    'medical_medicines' => [
        'table_name' => 'medical_medicines',
        'primary_key' => 'medicine_id',
        'display_name' => 'Medical Medicines',
        'icon' => 'fa-pills',
        'columns' => [
            'medicine_id' => [
                'value' => '',
                'label' => 'ID',
                'type' => 'integer',
                'sortable' => true,
                'searchable' => false,
                'visible' => false,
                'editable' => false
            ],
            'medicine_name' => [
                'value' => '',
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
                'value' => '',
                'label' => 'Brand Name',
                'type' => 'text',
                'sortable' => true,
                'searchable' => true,
                'visible' => true,
                'editable' => true,
                'width' => '15%'
            ],
            'medicine_generic_name' => [
                'value' => '',
                'label' => 'Generic Name',
                'type' => 'text',
                'sortable' => true,
                'searchable' => true,
                'visible' => true,
                'editable' => true,
                'width' => '15%'
            ],
            'medicine_classification' => [
                'value' => '',
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
                'value' => '',
                'label' => 'Dosage',
                'type' => 'text',
                'sortable' => true,
                'searchable' => false,
                'visible' => true,
                'editable' => true,
                'width' => '10%'
            ],
            'medicine_unit' => [
                'value' => '',
                'label' => 'Unit',
                'type' => 'text',
                'sortable' => true,
                'searchable' => false,
                'visible' => true,
                'editable' => true,
                'width' => '8%'
            ],
            'medicine_stock' => [
                'value' => '',
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
                'value' => '',
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
                'value' => '',
                'label' => 'Description',
                'type' => 'textarea',
                'sortable' => false,
                'searchable' => true,
                'visible' => false,
                'editable' => true
            ],
            'created_at' => [
                'value' => '',
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
            'delete' => [
                'label' => 'Delete',
                'icon' => 'fa-trash',
                'class' => 'btn-danger btn-sm',
                'confirm' => true,
                'confirm_message' => 'Are you sure you want to delete this medicine?'
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
    ]
];
?>