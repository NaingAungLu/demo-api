<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PromotionRepositoryInterface;
use App\Representations\PromotionRepresentation;
use App\Representations\PromotionRepresentationCollection;

use App\Library\APIResponse;

class PromotionController extends Controller
{
    protected $promotion;
    
    public function __construct(PromotionRepositoryInterface $promotion)
    {
        $this->promotion = $promotion;
    }

    public function index()
    {
        return (new APIResponse(0, 'Success', [
            'total_item' => $this->promotion->total(),
            'promotion_list' => new PromotionRepresentationCollection($this->promotion->all())
        ]))->getJson();
    }

    public function show(Request $request, $id)
    {
        return (new APIResponse(0, 'Success', [
            'promotion_list' => new PromotionRepresentation($this->promotion->show($id))
        ]))->getJson();
    }
}
