<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductRepresentationCollection extends ResourceCollection
{
    public $collects = 'App\Representations\ProductRepresentation';
}