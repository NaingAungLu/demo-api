<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PromotionRepresentationCollection extends ResourceCollection
{
    public $collects = 'App\Representations\PromotionRepresentation';
}