<?php

return [
	'name' => 'ai-admin-jsonadm',
	'depends' => [
		'aimeos-core',
	],
	'config' => [
		'config',
	],
	'include' => [
		'admin/jsonadm/src',
	],
	'i18n' => [
		'admin/jsonadm' => 'admin/jsonadm/i18n',
	],
	'template' => [
		'admin/jsonadm/templates' => [
			'admin/jsonadm/templates',
		]
	]
];
