<?php

return [
    'shopboss' => [
        [
            'header' => 'Navigation',
        ],
        [
            'title' => 'Home',
            'icon'  => 'bi bi-house fs-3',
            'url'   => '/'
        ],
        [
            'title' => 'POS',
            'icon'  => 'bi bi-upc-scan fs-3',
            'url'   => '/app/pos'
        ],
        [
            'title' => 'Stock Status',
            'icon'  => 'bi bi-bar-chart fs-3',
            'url'   => '/stock'
        ],
        [
            'title' => 'Sale',
            'icon'  => 'bi bi-receipt fs-3',
            'url'   => '/sales'
        ],
        [
            'title' => 'Sale Return',
            'icon'  => 'bi bi-arrow-return-left fs-3',
            'url'   => '/sale-returns'
        ],
        [
            'title' => 'Purchase',
            'icon'  => 'bi bi-bag fs-3',
            'url'   => '/purchases'
        ],
        [
            'title' => 'Purchase Return',
            'icon'  => 'bi bi-arrow-return-right fs-3',
            'url'   => '/purchase-returns'
        ],
        [
            'title' => 'Adjustment',
            'icon'  => 'bi bi-clipboard-check fs-3',
            'url'   => '/'
        ],
        [
            'title' => 'Quotations',
            'icon'  => 'bi bi-cart-check fs-3',
            'url'   => '/'
        ],
        [
            'title'    => 'Reports',
            'icon'     => 'bi bi-pie-chart fs-3',
            'children' => [
                [
                    'title' => 'Sales Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/sales-report'
                ],
                [
                    'title' => 'Purchase Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/purchases-report'
                ],
            ]
        ],
        [
            'title'    => 'Settings',
            'icon'     => 'bi bi-gear fs-3',
            'children' => [
                [
                    'title' => 'Category',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/product-categories'
                ],
                [
                    'title' => 'Product',
                    'icon'  => 'bi bi-upc fs-3',
                    'url'   => '/products'
                ]
            ]
        ],
    ]
];
