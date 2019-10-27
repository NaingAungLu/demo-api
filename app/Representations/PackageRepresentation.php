<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PackageRepresentation extends JsonResource
{
    public function toArray($request) {

        return [
			'id' => $this->when($this->id, $this->id),
			'disp_order' => $this->when($this->disp_order, $this->disp_order),
			'pack_id' => $this->when($this->pack_id, $this->pack_id),
			'pack_name' => $this->when($this->pack_name, $this->pack_name),
			'pack_description' => $this->when($this->pack_description, $this->pack_description),
			'pack_type' => $this->when($this->pack_type, $this->pack_type),
			'total_credit' => $this->when($this->total_credit, $this->total_credit),
			'tag_name' => $this->when($this->tag_name, $this->tag_name),
			'validity_month' => $this->when($this->validity_month, $this->validity_month),
			'pack_price' => $this->when($this->pack_price, $this->pack_price),
			'newbie_first_attend' => $this->newbie_first_attend ? true : false,
			'newbie_addition_credit' => $this->when($this->newbie_addition_credit, $this->newbie_addition_credit),
			'newbie_note' => $this->when($this->newbie_note, $this->newbie_note),
			'pack_alias' => $this->when($this->pack_alias, $this->pack_alias),
			'estimate_price' => $this->when($this->estimate_price, $this->estimate_price),
		];
    }
}