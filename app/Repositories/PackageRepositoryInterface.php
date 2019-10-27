<?php

namespace App\Repositories;

interface PackageRepositoryInterface
{
    public function all();

    public function total();

    public function show($id);
}