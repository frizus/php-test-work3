<?php

namespace App\Repositories;

class ContactRepository extends BaseRepository
{
    public const array FILTER_BY = [
        'agency_id',
    ];

    public const string TABLE_NAME = 'contacts';
}