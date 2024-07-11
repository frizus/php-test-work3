<?php

namespace App\Repositories;

class ManagerRepository extends BaseRepository
{
    public const array FILTER_BY = [
        'agency_id',
    ];

    public const string TABLE_NAME = 'manager';
}