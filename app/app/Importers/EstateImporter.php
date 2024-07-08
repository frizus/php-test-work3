<?php

namespace App\Importers;

use App\Importers\SourceReaders\ISourceReader;

class EstateImporter extends AbstractImporter
{
    protected const array MAP = [
        'id' => 'external_id',
        'Агенство Недвижимости' => 'agency_name',
        'Менеджер' => 'manager_name',
        'Продавец' => 'contacts_name',
        'Телефоны продавца' => 'contacts_phones',
        'Цена' => 'price',
        'Описание' => 'description',
        'Адрес' => 'address',
        'Этаж' => 'floor',
        'Этажей' => 'house_floors',
        'Комнат' => 'rooms',
    ];

    public function __construct(
        protected ISourceReader $sourceReader
    )
    {

    }

    public function run()
    {
        while (($row = $this->sourceReader->nextRow()) !== null) {
            //$this->
        }
    }
}