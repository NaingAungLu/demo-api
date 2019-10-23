<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductRepresentation extends JsonResource
{
    public function toArray($request) {

        return [
			'id' => $this->when($this->id, $this->id),
			'name' => $this->when($this->name, $this->name),
			'remark' => $this->when($this->remark, $this->remark),
			'is_discount' => $this->when($this->is_discount, $this->is_discount),
			'quantity' => $this->when($this->quantity, $this->quantity),
			'price' => $this->when($this->price, $this->price),
			'date' => $this->when($this->date, Carbon::parse($this->date)->format('d/m/Y')),
			'status' => $this->when($this->status, $this->status),
			'created_by' => $this->when($this->created_by, $this->created_by),
			'last_updated_by' => $this->when($this->last_updated_by, $this->last_updated_by),
        ];
    }
}