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
            'icon'  => 'bi bi-speedometer2',
            'title' => 'therapy::therapy.dashboard',
            'url'   => '/dashboard',
        ],
        [
            'header' => 'Finance'
        ],
        [
            'title' => 'Finance',
            'icon'  => 'bi bi-umbrella fs-3',
            'url'   => '/finance/records'
        ],
        [
            'header' => 'MENUS'
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
        // [
        //     'title' => 'Adjustment',
        //     'icon'  => 'bi bi-clipboard-check fs-3',
        //     'url'   => '/'
        // ],
        // [
        //     'title' => 'Quotations',
        //     'icon'  => 'bi bi-cart-check fs-3',
        //     'url'   => '/'
        // ],
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
                    'title' => 'Sales Return Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/sales-return-report'
                ],
                [
                    'title' => 'Purchase Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/purchases-report'
                ],
                [
                    'title' => 'Purchase Return Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/purchases-return-report'
                ],
                [
                    'title' => 'Product Wise Sele Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/product-wise-sele-report'
                ],
                [
                    'title' => 'Product Wise Purchase Report',
                    'icon'  => 'bi bi-tag fs-3',
                    'url'   => '/product-wise-purchase-report'
                ],
            ]
        ],
        [
            'title'    => 'Master Data',
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
                ],
                [
                    'title' => 'Customer',
                    'icon'  => 'fa-solid fa-user fs-3',
                    'url'   => '/customers'
                ],
                [
                    'title' => 'Supplier',
                    'icon'  => 'fa-solid fa-industry fs-3',
                    'url'   => '/suppliers'
                ],
                [
                    'title' => 'Branch',
                    'icon'  => 'fa-solid fa-code-branch fs-3',
                    'url'   => '/shopboss-branches'
                ]
            ]
        ],
    ]
];
