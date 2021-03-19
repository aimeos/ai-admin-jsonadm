<?php

return [
	'attribute' => [
		/** admin/jsonadm/resource/attribute/groups
		 * List of user groups that are allowed to manage attribute items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/attribute/lists/groups
			 * List of user groups that are allowed to manage attribute lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/attribute/lists/type/groups
				 * List of user groups that are allowed to manage attribute lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'property' => [
			/** admin/jsonadm/resource/attribute/property/groups
			 * List of user groups that are allowed to manage attribute property items
			 *
			 * @param array List of user group names
			 * @since 2018.07
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/attribute/property/type/groups
				 * List of user groups that are allowed to manage attribute property type items
				 *
				 * @param array List of user group names
				 * @since 2018.07
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/attribute/type/groups
			 * List of user groups that are allowed to manage attribute type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'catalog' => [
		/** admin/jsonadm/resource/catalog/groups
		 * List of user groups that are allowed to manage catalog items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/catalog/lists/groups
			 * List of user groups that are allowed to manage catalog lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/catalog/lists/type/groups
				 * List of user groups that are allowed to manage catalog lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
	],
	'coupon' => [
		/** admin/jsonadm/resource/coupon/groups
		 * List of user groups that are allowed to manage coupon items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'code' => [
			/** admin/jsonadm/resource/coupon/code/groups
			 * List of user groups that are allowed to manage coupon code items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'config' => [
			/** admin/jsonadm/resource/coupon/config/groups
			 * List of user groups that are allowed to fetch available coupon configuration
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'customer' => [
		/** admin/jsonadm/resource/customer/groups
		 * List of user groups that are allowed to manage customer items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'address' => [
			/** admin/jsonadm/resource/customer/address/groups
			 * List of user groups that are allowed to manage customer address items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'group' => [
			/** admin/jsonadm/resource/customer/group/groups
			 * List of user groups that are allowed to manage customer group items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'lists' => [
			/** admin/jsonadm/resource/customer/lists/groups
			 * List of user groups that are allowed to manage customer lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/customer/lists/type/groups
				 * List of user groups that are allowed to manage customer lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'property' => [
			/** admin/jsonadm/resource/customer/property/groups
			 * List of user groups that are allowed to manage customer property items
			 *
			 * @param array List of user group names
			 * @since 2018.07
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/customer/property/type/groups
				 * List of user groups that are allowed to manage customer property type items
				 *
				 * @param array List of user group names
				 * @since 2018.07
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
	],
	'index' => [
		/** admin/jsonadm/resource/index/groups
		 * List of user groups that are allowed to manage index items
		 *
		 * @param array List of user group names
		 * @since 2020.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'attribute' => [
			/** admin/jsonadm/resource/index/attribute/groups
			 * List of user groups that are allowed to manage index attribute items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'attribute' => [
			/** admin/jsonadm/resource/index/attribute/groups
			 * List of user groups that are allowed to manage index attribute items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'catalog' => [
			/** admin/jsonadm/resource/index/catalog/groups
			 * List of user groups that are allowed to manage index catalog items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'price' => [
			/** admin/jsonadm/resource/index/price/groups
			 * List of user groups that are allowed to manage index price items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'supplier' => [
			/** admin/jsonadm/resource/index/supplier/groups
			 * List of user groups that are allowed to manage index supplier items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'text' => [
			/** admin/jsonadm/resource/index/text/groups
			 * List of user groups that are allowed to manage index text items
			 *
			 * @param array List of user group names
			 * @since 2020.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'locale' => [
		/** admin/jsonadm/resource/locale/groups
		 * List of user groups that are allowed to manage locale items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'super'],
		'site' => [
			/** admin/jsonadm/resource/locale/site/groups
			 * List of user groups that are allowed to manage locale site items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
		'language' => [
			/** admin/jsonadm/resource/locale/language/groups
			 * List of user groups that are allowed to manage locale language items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
		'currency' => [
			/** admin/jsonadm/resource/locale/currency/groups
			 * List of user groups that are allowed to manage locale currency items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
	],
	'media' => [
		/** admin/jsonadm/resource/media/groups
		 * List of user groups that are allowed to manage media items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/media/lists/groups
			 * List of user groups that are allowed to manage media lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/media/lists/type/groups
				 * List of user groups that are allowed to manage media lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/media/type/groups
			 * List of user groups that are allowed to manage media type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'property' => [
			/** admin/jsonadm/resource/media/property/groups
			 * List of user groups that are allowed to manage media property items
			 *
			 * @param array List of user group names
			 * @since 2018.07
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/media/property/type/groups
				 * List of user groups that are allowed to manage media property type items
				 *
				 * @param array List of user group names
				 * @since 2018.07
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
	],
	'order' => [
		/** admin/jsonadm/resource/order/groups
		 * List of user groups that are allowed to manage order items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'base' => [
			/** admin/jsonadm/resource/order/base/groups
			 * List of user groups that are allowed to manage order base items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'address' => [
				/** admin/jsonadm/resource/order/base/address/groups
				 * List of user groups that are allowed to manage order address items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
			'coupon' => [
				/** admin/jsonadm/resource/order/base/coupon/groups
				 * List of user groups that are allowed to manage order coupon items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
			'product' => [
				/** admin/jsonadm/resource/order/base/product/groups
				 * List of user groups that are allowed to manage order product items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
				'attribute' => [
					/** admin/jsonadm/resource/order/base/product/attribute/groups
					 * List of user groups that are allowed to manage order product attribute items
					 *
					 * @param array List of user group names
					 * @since 2017.10
					 */
					'groups' => ['admin', 'editor', 'super'],
				],
			],
			'service' => [
				/** admin/jsonadm/resource/order/base/service/groups
				 * List of user groups that are allowed to manage order service items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
				'attribute' => [
					/** admin/jsonadm/resource/order/base/service/attribute/groups
					 * List of user groups that are allowed to manage order service attribute items
					 *
					 * @param array List of user group names
					 * @since 2017.10
					 */
					'groups' => ['admin', 'editor', 'super'],
				],
			],
		],
		'status' => [
			/** admin/jsonadm/resource/order/status/groups
			 * List of user groups that are allowed to manage order status items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'plugin' => [
		/** admin/jsonadm/resource/plugin/groups
		 * List of user groups that are allowed to manage plugin items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'super'],
		'config' => [
			/** admin/jsonadm/resource/plugin/config/groups
			 * List of user groups that are allowed to fetch available plugin configuration
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
		'type' => [
			/** admin/jsonadm/resource/plugin/type/groups
			 * List of user groups that are allowed to manage plugin type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
	],
	'price' => [
		/** admin/jsonadm/resource/price/groups
		 * List of user groups that are allowed to manage price items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/price/lists/groups
			 * List of user groups that are allowed to manage price lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/price/lists/type/groups
				 * List of user groups that are allowed to manage price lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'property' => [
			/** admin/jsonadm/resource/price/property/groups
			 * List of user groups that are allowed to manage price property items
			 *
			 * @param array List of user group names
			 * @since 2019.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/price/property/type/groups
				 * List of user groups that are allowed to manage price property type items
				 *
				 * @param array List of user group names
				 * @since 2019.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/price/type/groups
			 * List of user groups that are allowed to manage price type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'product' => [
		/** admin/jsonadm/resource/product/groups
		 * List of user groups that are allowed to manage product items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/product/lists/groups
			 * List of user groups that are allowed to manage product lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/product/lists/type/groups
				 * List of user groups that are allowed to manage product lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'property' => [
			/** admin/jsonadm/resource/product/property/groups
			 * List of user groups that are allowed to manage product property items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/product/property/type/groups
				 * List of user groups that are allowed to manage product property type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/product/type/groups
			 * List of user groups that are allowed to manage product type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'rule' => [
		/** admin/jsonadm/resource/rule/groups
		 * List of user groups that are allowed to manage rule items
		 *
		 * @param array List of user group names
		 * @since 2021.04
		 */
		'groups' => ['admin', 'editor', 'super'],
		'config' => [
			/** admin/jsonadm/resource/rule/config/groups
			 * List of user groups that are allowed to fetch available rule configuration
			 *
			 * @param array List of user group names
			 * @since 2021.04
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'type' => [
			/** admin/jsonadm/resource/rule/type/groups
			 * List of user groups that are allowed to manage rule type items
			 *
			 * @param array List of user group names
			 * @since 2021.04
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'service' => [
		/** admin/jsonadm/resource/service/groups
		 * List of user groups that are allowed to manage service items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'super'],
		'config' => [
			/** admin/jsonadm/resource/service/config/groups
			 * List of user groups that are allowed to fetch available service configuration
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
		'lists' => [
			/** admin/jsonadm/resource/service/lists/groups
			 * List of user groups that are allowed to manage service lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
			'type' => [
				/** admin/jsonadm/resource/service/lists/type/groups
				 * List of user groups that are allowed to manage service lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/service/type/groups
			 * List of user groups that are allowed to manage service type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'super'],
		],
	],
	'stock' => [
		/** admin/jsonadm/resource/stock/groups
		 * List of user groups that are allowed to manage stock items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'type' => [
			/** admin/jsonadm/resource/stock/type/groups
			 * List of user groups that are allowed to manage stock type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'supplier' => [
		/** admin/jsonadm/resource/supplier/groups
		 * List of user groups that are allowed to manage supplier items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'address' => [
			/** admin/jsonadm/resource/supplier/address/groups
			 * List of user groups that are allowed to manage supplier address items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
		'lists' => [
			/** admin/jsonadm/resource/supplier/lists/groups
			 * List of user groups that are allowed to manage supplier lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/supplier/lists/type/groups
				 * List of user groups that are allowed to manage supplier lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/supplier/type/groups
			 * List of user groups that are allowed to manage supplier type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'tag' => [
		/** admin/jsonadm/resource/tag/groups
		 * List of user groups that are allowed to manage tag items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'type' => [
			/** admin/jsonadm/resource/tag/type/groups
			 * List of user groups that are allowed to manage tag type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
	'text' => [
		/** admin/jsonadm/resource/text/groups
		 * List of user groups that are allowed to manage text items
		 *
		 * @param array List of user group names
		 * @since 2017.10
		 */
		'groups' => ['admin', 'editor', 'super'],
		'lists' => [
			/** admin/jsonadm/resource/text/lists/groups
			 * List of user groups that are allowed to manage text lists items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
			'type' => [
				/** admin/jsonadm/resource/text/lists/type/groups
				 * List of user groups that are allowed to manage text lists type items
				 *
				 * @param array List of user group names
				 * @since 2017.10
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		],
		'type' => [
			/** admin/jsonadm/resource/text/type/groups
			 * List of user groups that are allowed to manage text type items
			 *
			 * @param array List of user group names
			 * @since 2017.10
			 */
			'groups' => ['admin', 'editor', 'super'],
		],
	],
];
