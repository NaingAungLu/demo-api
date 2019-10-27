<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class OrderRepresentation extends JsonResource
{
    public function toArray($request) {

        return [
			'id' => $this->when($this->id, $this->id),
			'package_id' => $this->when($this->package_id, $this->package_id),
			'order_date' => $this->when($this->order_date, $this->order_date),
			'grand_total' => $this->when($this->grand_total, $this->grand_total)
		];
    }
}