<?php

return array(
	'name' => 'ai-admin-jsonadm',
	'depends' => array(
		'aimeos-core',
	),
	'config' => array(
		'admin/jsonadm/config',
	),
	'include' => array(
		'admin/jsonadm/src',
	),
	'i18n' => array(
		'admin/jsonadm' => 'admin/jsonadm/i18n',
	),
	'custom' => array(
		'admin/jsonadm/templates' => array(
			'admin/jsonadm/templates',
		),
	),
);
