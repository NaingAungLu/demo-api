<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageRepresentationCollection extends ResourceCollection
{
    public $collects = 'App\Representations\PackageRepresentation';
}