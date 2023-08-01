<?php

namespace App\Repositories;

use App\Models\InformationRecord;
use Illuminate\Support\Facades\Hash;

interface RepositoryInterface
{
    public function all();

    public function store(Array $data);

    public function update(Array $data);

    public function delete();
}
