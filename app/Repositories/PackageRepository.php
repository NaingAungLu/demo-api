<?php

namespace App\Repositories;

use App\Models\Package;

class PackageRepository implements PackageRepositoryInterface
{
	public function all()
    {
    	return Package::all();
    }

    public function total()
    {
   		return Package::count();
    }

    public function show($id)
    {
   		return Package::find($id);
    }
}