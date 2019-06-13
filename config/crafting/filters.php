<?php

// Filters array used to generate both filtration anchors and filtration widgets
$filters = [
	'name' => [
		'key'	 => 'name',
		'for'    => ['item', 'recipe', 'equipment', 'quest', 'mob'],
		'type'   => 'string',
		'icon'   => 'fa-magnify',
		'title'  => 'Search',
	],
	'ilvl' => [
		'key'	 => 'ilvl',
		'for'    => ['item', 'recipe', 'equipment'],
		'type'   => 'range',
		'icon'   => 'fa-info',
		'title'  => 'Item Level',
	],
	'rarity' => [
		'key'	 => 'rarity',
		'for'    => ['item', 'recipe', 'equipment'],
		'type'   => 'multiple',
		'icon'   => 'fa-registered',
		'title'  => 'Rarity',
	],
	'rlvl' => [
		'key'	 => 'rlvl',
		'for'    => ['recipe'],
		'type'   => 'range',
		'icon'   => 'fa-award',
		'title'  => 'Recipe Level',
	],
	'rclass' => [
		'key'	 => 'rclass',
		'for'    => ['recipe'],
		'type'   => 'multiple',
		'icon'   => 'fa-chess-bishop',
		'title'  => 'Recipe Class',
	],
	'rdifficulty' => [
		'key'	 => 'sublevel',
		'for'    => ['recipe'],
		'type'   => 'multiple',
		'icon'   => 'fa-star',
		'title'  => 'Recipe Difficulty',
	],
	'elvl' => [
		'key'	 => 'elvl',
		'for'    => ['equipment'],
		'type'   => 'range',
		'icon'   => 'fa-medal',
		'title'  => 'Equipment Level',
	],
	'eclass' => [
		'key'	 => 'eclass',
		'for'    => ['equipment'],
		'type'   => 'multiple',
		'icon'   => 'fa-chess-rook',
		'title'  => 'Equipment Class',
	],
	'slot' => [
		'key'	 => 'slot',
		'for'    => ['equipment'],
		'type'   => 'multiple',
		'icon'   => 'fa-hand-paper',
		'title'  => 'Equipment Slot',
	],
	'sockets' => [
		'key'	 => 'sockets',
		'for'    => ['equipment'],
		'type'   => 'multiple',
		'icon'   => 'fa-gem',
		'title'  => 'Materia Sockets',
	],
];

return [
	'all' => $filters,

	'item' => array_intersect_key($filters, array_flip(['name', 'ilvl', 'rarity'])),

	'recipe' => array_intersect_key($filters, array_flip(['name', 'ilvl', 'rarity', 'rlvl', 'rclass', 'rdifficulty'])),

	'equipment' => array_intersect_key($filters, array_flip(['name', 'ilvl', 'rarity', 'elvl', 'eclass', 'slot', 'sockets'])),

	'sorting' => [
		'name:asc' => [
			'key'   => 'name:asc',
			'icon'  => 'fa-sort-alpha-down',
			'title' => 'Name: A-Z',
		],
		'name:desc' => [
			'key'   => 'name:desc',
			'icon'  => 'fa-sort-alpha-up',
			'title' => 'Name: Z-A',
		],
		'ilvl:asc' => [
			'key'   => 'ilvl:asc',
			'icon'  => 'fa-sort-numeric-down',
			'title' => 'iLv: Low to High',
		],
		'ilvl:desc' => [
			'key'   => 'ilvl:desc',
			'icon'  => 'fa-sort-numeric-up',
			'title' => 'iLv: High to Low',
		],
	],

	'perPage' => [
		'15' => [
			'key'   => '15',
			'icon'  => 'fa-battery-quarter',
			'title' => 'Show 15 per page',
		],
		'30' => [
			'key'   => '30',
			'icon'  => 'fa-battery-half',
			'title' => 'Show 30 per page',
		],
		'45' => [
			'key'   => '45',
			'icon'  => 'fa-battery-three-quarters',
			'title' => 'Show 45 per page',
		],
	],

];