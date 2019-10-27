<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
	public function all()
    {
    	return Order::all();
    }

    public function total()
    {
   		return Order::count();
    }

    public function save($data)
    {
   		return Order::create($data);
    }

    public function show($id)
    {
   		return Order::find($id);
    }
}