<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserRepresentationCollection extends ResourceCollection
{
    public $collects = 'App\Representations\UserRepresentation';
}