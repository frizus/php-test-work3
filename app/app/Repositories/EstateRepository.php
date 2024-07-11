<?php

namespace App\Repositories;

class EstateRepository extends BaseRepository
{
    public const array FILTER_BY = [
        'agency_id',
        'contact_id',
        'manager_id'
    ];
}