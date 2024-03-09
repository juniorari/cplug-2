<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoedasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
//            'id' => $this->id,
            'nome' => $this->name,
            'simbolo' => $this->symbol,
            'ranking' => $this->ranking,
            'market_cap' => $this->market_cap,
            'preco_US' => 'U$ ' . number_format($this->price, 8),
            'volume_24h' => $this->volume_24h,
            'variacao_24h' => $this->variacao_24h,
        ];
    }
}
