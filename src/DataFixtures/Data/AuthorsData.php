<?php

namespace App\DataFixtures\Data;

class AuthorsData
{
    public function getAuthors(): array
    {
        $datas = [];
        $datas = [
            ['firstName' => 'Régis', 'lastName' => 'Goddyn', 'active' => true],
            ['firstName' => 'Naomie', 'lastName' => 'Klein', 'active' => true],
            ['firstName' => 'Olivier', 'lastName' => 'Nerek', 'active' => true],
            ['firstName' => 'Alain', 'lastName' => 'Damasio', 'active' => true]
        ];
        return $datas;
    }


}
