<?php

namespace App\Repositories;

use App\Models\InformationRecord;

class InformationRecordRepository implements RepositoryInterface
{

    private $information;

    public function __construct(InformationRecord $information)
    {
        $this->information = $information;
    }

    public function store(array $data)
    {
        foreach ($data as $atribute) {
            $this->information->create([
                'name' => $atribute['name'],
                'amount' => $atribute['amount']
            ]);
        }
        return $data;
    }

}
