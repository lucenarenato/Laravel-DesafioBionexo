<?php

namespace App\Repositories;

use App\Models\InformationRecord;
use Illuminate\Support\Facades\Hash;

interface RepositoryInterface
{
    public function store(Array $data);
}
