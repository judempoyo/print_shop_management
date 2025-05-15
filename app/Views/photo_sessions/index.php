<?php

$tableParams = [
    'title' => 'Séances Photo',
    'createUrl' => PUBLIC_URL . 'photo-session/create',
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'sortable' => true],
        ['key' => 'type', 'label' => 'Type', 'sortable' => true],
        ['key' => 'date', 'label' => 'Date', 'format'=>'date','sortable' => true],
        ['key' => 'sessions.customer.name', 'label' => 'Client'],
        ['key' => 'status', 'label' => 'Statut', 'sortable' => true],
    ],
    'data' => $sessions,
    'actions' => [
        [
            'label' => 'Voir',
            'url' => function($item) {
                return PUBLIC_URL . 'photo-session/' . $item['id'];
            },
            'icon' =>' <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>'
        ],
        [
            'label' => 'Modifier',
            'url' => function($item) {
                return PUBLIC_URL . 'photo-session/edit/' . $item['id'];
            }
        ],
        [
            'label' => 'Supprimer',
            'type' => 'delete',
            'url' => function($item) {
                return PUBLIC_URL . 'photo-session/delete/' . $item['id'];
            }
        ]
    ],
    'filters' => [
        ['placeholder' => 'Rechercher par titre...'],
    ],
    'modelName' => 'photo-session',
];

include __DIR__ . '/../partials/reusable_table.php';
?>