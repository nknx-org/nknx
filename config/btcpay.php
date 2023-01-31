<?php

return [

    'api-key' => env('BTCPAY_API_KEY', null),

    'host' => env('BTCPAY_HOST', null),
    'store-id' => env('BTCPAY_STORE_ID', null),
    'webhook-secret' => env('BTCPAY_WEBHOOK_SECRET', null),
    'currency' => env('BTCPAY_CURRENCY', 'USD'),

    'plans' => [
        [
            'name' => 'Standard',
            'short_description' => 'For miners that want to manage a few more nodes and support the project.',
            'monthly_id' => '10001',
            'yearly_id' => '10002',
            'monthly_price' => '999',
            'yearly_price' => '9990',
            'features' => [
                'Monitor up to 100 nodes',
                'Monitor up to 10 wallets',
                '150 FastDeployments® per month',
                'eMail and in-App Notifications',
                'History Data for node performance',
                '10% rebate on our node ID generation service',
                'Premium Support incl. node troubleshooting'
            ],
            'options' => [
                'wallets_limit'                     => 10,
                'nodes_limit'                       => 100,
                'monthly_fast_deployments_limit'    => 150,
                'generation_id_discount'            => 1,
                'central_monitoring'                => true,
                'api_deployment'                    => true
            ]
        ],
        [
            'name' => 'Standard +',
            'short_description' => 'Not a beginner but also not a super-miner. This is your plan!',
            'monthly_id' => '20001',
            'yearly_id' => '20002',
            'monthly_price' => '1999',
            'yearly_price' => '19990',
            'features' => [
                'Monitor up to 250 nodes',
                'Monitor up to 20 wallets',
                '375 FastDeployments® per month',
                'eMail and in-App Notifications',
                'History Data for node performance',
                '20% rebate on our node ID generation service',
                'Premium Support incl. node troubleshooting'
            ],
            'options' => [
                'wallets_limit'                     => 20,
                'nodes_limit'                       => 250,
                'monthly_fast_deployments_limit'    => 375,
                'generation_id_discount'            => 2,
                'central_monitoring'                => true,
                'api_deployment'                    => true
            ]
        ],
        [
            'name' => 'Pro',
            'short_description' => 'For large miners to want to monitor a whole lot of nodes and wallets.',
            'monthly_id' => '30001',
            'yearly_id' => '30002',
            'monthly_price' => '2999',
            'yearly_price' => '29990',
            'features' => [
                'Monitor up to 500 nodes',
                'Monitor up to 25 wallets',
                '1000 FastDeployments® per month',
                'eMail and in-App Notifications',
                'History Data for node performance',
                '30% rebate on our node ID generation service',
                'Premium Support incl. node troubleshooting'
            ],
            'options' => [
                'wallets_limit'                     => 25,
                'nodes_limit'                       => 500,
                'monthly_fast_deployments_limit'    => 1000,
                'generation_id_discount'            => 3,
                'central_monitoring'                => true,
                'api_deployment'                    => true
            ]
        ],
        [
            'name' => 'Pro+',
            'short_description' => 'Having problems counting your nodes? We\'re sure we can help with that!',
            'monthly_id' => '40001',
            'yearly_id' => '40002',
            'monthly_price' => '5999',
            'yearly_price' => '59990',
            'features' => [
                'Monitor up to 2000 nodes',
                'Monitor up to 100 wallets',
                '2500 FastDeployments® per month',
                'eMail and in-App Notifications',
                'History Data for node performance',
                '40% rebate on our node ID generation service',
            ],
            'options' => [
                'wallets_limit'                     => 100,
                'nodes_limit'                       => 2000,
                'monthly_fast_deployments_limit'    => 2500,
                'generation_id_discount'            => 4,
                'central_monitoring'                => true,
                'api_deployment'                    => true
            ]
        ],
        [
            'name' => 'Ultimate',
            'short_description' => 'No compromises! Your whole fleet always monitored paired with maxed out rebates.',
            'monthly_id' => '50001',
            'yearly_id' => '50002',
            'monthly_price' => '9999',
            'yearly_price' => '99990',
            'features' => [
                'Monitor up to 5000 nodes',
                'Monitor up to 250 wallets',
                '7500 FastDeployments® per month',
                'eMail and in-App Notifications',
                'History Data for node performance',
                '40% rebate on our node ID generation service',
            ],
            'options' => [
                'wallets_limit'                     => 250,
                'nodes_limit'                       => 5000,
                'monthly_fast_deployments_limit'    => 7500,
                'generation_id_discount'            => 5,
                'central_monitoring'                => true,
                'api_deployment'                    => true
            ]
        ],
    ],

];
