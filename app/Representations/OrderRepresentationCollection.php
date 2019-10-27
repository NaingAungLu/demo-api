<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderRepresentationCollection extends ResourceCollection
{
    public $collects = 'App\Representations\OrderRepresentation';
}