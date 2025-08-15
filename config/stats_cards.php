<?php
return [
    'medicines' => [
        [
            'key' => 'total',
            'label' => 'Total Medicines',
            'value' => $stats['total'] ?? 0,
            'bgColor' => '#f0f4ff',
            'svgColor' => '#1e40af',
            'svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="#1e40af">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14
                             c0 1.1.9 2 2 2h14c1.1 0 
                             2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 
                             14H7v-2h7v2zm3-4H7v-2h10v2zm0-4
                             H7V7h10v2z"/>
                 </svg>'
        ],
        [
            'key' => 'low_stock',
            'label' => 'Low Stock',
            'value' => $stats['low_stock'] ?? 0,
            'bgColor' => '#fff7ed',
            'svgColor' => '#f97316',
            'svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="#f97316">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M10 17l-5-5 1.41-1.41L10 14.17
                             l7.59-7.59L19 8l-9 9z" fill="#fff"/>
                 </svg>'
        ],
        [
            'key' => 'expired',
            'label' => 'Expired',
            'value' => $stats['expired'] ?? 0,
            'bgColor' => '#fef2f2',
            'svgColor' => '#dc2626',
            'svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="#dc2626">
                    <path d="M12 2C6.48 2 2 6.48 2 12
                             s4.48 10 10 10 10-4.48 10-10S17.52 
                             2 12 2zm-1 15h2v2h-2v-2zm0-8h2v6
                             h-2V9z"/>
                 </svg>'
        ],
        [
            'key' => 'expiring_soon',
            'label' => 'Expiring Soon',
            'value' => $stats['expiring_soon'] ?? 0,
            'bgColor' => '#fffbeb',
            'svgColor' => '#ca8a04',
            'svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="#ca8a04">
                    <polygon points="12,2 15.09,8.26 22,9.27 
                                     17,14.14 18.18,21.02 
                                     12,17.77 5.82,21.02 
                                     7,14.14 2,9.27 8.91,8.26"/>
                 </svg>'
        ]
    ]
];

?>