<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PromotionRepresentation extends JsonResource
{
    public function toArray($request) {

        return [
			'id' => $this->when($this->id, $this->id),
			'promo_code' => $this->when($this->promo_code, $this->promo_code),
			'amount' => $this->when($this->order_date, $this->order_date),
		];
    }
}