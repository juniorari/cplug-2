<?php

namespace App\Http\Resources;

use App\Models\Moeda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GruposResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        dd($this);
        $res = Moeda::where('id', $this->moeda_id)->get();
        $moeda = null;
        if (count($res) > 0) {
            $moeda = MoedasResource::collection($res)
                ->response()
                ->getData(true)['data'][0]; //HACK
        }
        return [
            'nome' => $this->nome,
            'slug' => $this->slug,
            'moeda' => $moeda,
        ];
    }
}
