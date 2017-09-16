<?php

return [
	'jsonadm' => [
		'access' => [
			'attribute' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'catalog' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
			],
			'coupon' => [
				'groups' => ['admin', 'editor', 'super'],
				'code' => [
					'groups' => ['admin', 'editor', 'super'],
				],
			],
			'customer' => [
				'groups' => ['admin', 'editor', 'super'],
				'address' => [
					'groups' => ['admin', 'editor', 'super'],
				],
				'group' => [
					'groups' => ['admin', 'super'],
				],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
			],
			'locale' => [
				'groups' => ['admin', 'super'],
				'site' => [
					'groups' => ['admin', 'super'],
				],
				'language' => [
					'groups' => ['admin', 'super'],
				],
				'currency' => [
					'groups' => ['admin', 'super'],
				],
			],
			'media' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'order' => [
				'groups' => ['admin', 'editor', 'super'],
				'base' => [
					'groups' => ['admin', 'editor', 'super'],
					'address' => [
						'groups' => ['admin', 'editor', 'super'],
					],
					'coupon' => [
						'groups' => ['admin', 'editor', 'super'],
					],
					'product' => [
						'groups' => ['admin', 'editor', 'super'],
						'attribute' => [
							'groups' => ['admin', 'editor', 'super'],
						],
					],
					'service' => [
						'groups' => ['admin', 'editor', 'super'],
						'attribute' => [
							'groups' => ['admin', 'editor', 'super'],
						],
					],
				],
				'status' => [
					'groups' => ['admin', 'editor', 'super'],
				],
			],
			'plugin' => [
				'groups' => ['admin', 'super'],
			],
			'price' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'product' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'property' => [
					'groups' => ['admin', 'editor', 'super'],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'service' => [
				'groups' => ['admin', 'super'],
				'lists' => [
					'groups' => ['admin', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'stock' => [
				'groups' => ['admin', 'editor', 'super'],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'supplier' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'tag' => [
				'groups' => ['admin', 'editor', 'super'],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
			'text' => [
				'groups' => ['admin', 'editor', 'super'],
				'lists' => [
					'groups' => ['admin', 'editor', 'super'],
					'type' => [
						'groups' => ['admin', 'super'],
					],
				],
				'type' => [
					'groups' => ['admin', 'super'],
				],
			],
		],
		'partials' => [
			'catalog' => [
				'template-data' => 'partials/catalog/data-standard.php',
			],
			'locale' => [
				'site' => [
					'template-data' => 'partials/locale/site/data-standard.php',
				],
			],
			'order' => [
				'template-data' => 'partials/order/data-standard.php',
				'base' => [
					'template-data' => 'partials/order/base/data-standard.php',
				],
			],
		],
	],
];