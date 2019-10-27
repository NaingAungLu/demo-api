<?php

namespace App\Repositories;

interface PromotionRepositoryInterface
{
    public function all();

    public function total();

    public function show($id);
}