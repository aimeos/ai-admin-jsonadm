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
		'src',
	],
	'i18n' => [
		'admin/jsonadm' => 'i18n',
	],
	'template' => [
		'admin/jsonadm/templates' => [
			'templates/admin/jsonadm',
		]
	]
];
