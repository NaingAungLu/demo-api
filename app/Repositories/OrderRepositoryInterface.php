<?php

namespace App\Repositories;

interface OrderRepositoryInterface
{
    public function all();

    public function total();

    public function save($data);

    public function show($id);
}