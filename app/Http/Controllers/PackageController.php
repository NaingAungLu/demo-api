<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PackageRepositoryInterface;
use App\Representations\PackageRepresentation;
use App\Representations\PackageRepresentationCollection;

use App\Library\APIResponse;

class PackageController extends Controller
{
    protected $package;
    
    public function __construct(PackageRepositoryInterface $package)
    {
        $this->package = $package;
    }

    public function index()
    {
        return (new APIResponse(0, 'Success', [
            'total_item' => $this->package->total(),
            'total_page' => 1,
            'mem_tier' => "newbie",
            'total_expired_class' => 0,
            'pack_list' => new PackageRepresentationCollection($this->package->all())
        ]))->getJson();
    }


}
