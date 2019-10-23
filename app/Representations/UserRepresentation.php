<?php

namespace App\Representations;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserRepresentation extends JsonResource
{
    public function toArray($request) {

        return [
			'id' => $this->when($this->id, $this->id),
			'name' => $this->when($this->name, $this->name),
			'email' => $this->when($this->email, $this->email),
			'api_token' => $this->when($this->api_token, $this->api_token),
			'status' => $this->when($this->status, $this->status),
			'created_by' => $this->when($this->created_by, $this->created_by),
			'last_updated_by' => $this->when($this->last_updated_by, $this->last_updated_by),

			'scopes' => $request->scopes ?? false
        ];
    }
}