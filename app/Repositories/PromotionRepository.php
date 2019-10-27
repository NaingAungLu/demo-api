<?php

namespace App\Repositories;

use App\Models\Promotion;

class PromotionRepository implements PromotionRepositoryInterface
{
	public function all()
    {
    	return Promotion::all();
    }

    public function total()
    {
   		return Promotion::count();
    }

    public function show($id)
    {
   		return Promotion::find($id);
    }
}