<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MoedasResource;
use App\Models\Moeda;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class MoedaController extends Controller
{

    const PER_PAGE = 20;
    protected $API_URL;
    protected $API_KEY;

    public function __construct()
    {
        $this->API_URL = env('API_URL');
        $this->API_KEY = env('API_KEY_COIN_MARKET');
    }

    public function index(Request $request)
    {
        $qtd = $request->qtd ?? 100;
        $qtd = ($qtd > 5000 ? 5000 : $qtd);
        $endpoint = "cryptocurrency/listings/latest";
        $resp = $this->callApi($endpoint, [
            'limit' => $qtd,
            'cryptocurrency_type' => 'all',
            'tag' => 'all'
        ]);

        if (!$resp) {
            return response()->json([
                'status' => 400,
                'message' => 'Nada retornado na API'
            ]);
        }

        if (count($resp->data) > 0) {
            foreach ($resp->data as $data) {
                $dados = [
                    'name' => $data->name,
                    'symbol' => $data->symbol,
                    'slug' => $data->slug,
                    'ranking' => $data->cmc_rank,
                    'market_cap' => $data->quote->USD->market_cap,
                    'price' => $data->quote->USD->price,
                    'volume_24h' => $data->quote->USD->volume_24h,
                    'variacao_24h' => $data->quote->USD->volume_change_24h,
                    'dados' => json_encode($data),
                ];
                Moeda::updateOrCreate(
                    [
                        'name' => $data->name,
                        'symbol' => $data->symbol,
                        'slug' => $data->slug,
                    ],
                    $dados
                );
            }
            return response()->json([
                'code' => 200,
                'message' => count($resp->data) . ' Dado(s) foram incluídos/atualizados',
                'status' => $resp->status,
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Dados não encontrados',
                'status' => $resp->status,
            ]);
        }

    }

    public function listar(Request $request)
    {
        $moedas = Moeda::query();

        $sort = getValueFromRequest($request, 'sort', 'ranking', ['name', 'symbol', 'ranking', 'price']);
        $dir = getValueFromRequest($request, 'dir', 'ASC', ['ASC', 'DESC']);

        $res = $moedas->orderBy($sort, $dir)
            ->latest()
            ->paginate(self::PER_PAGE)
            ->appends($request->query());

        $moedas = MoedasResource::collection($res)
            ->response()
            ->getData(true);

        return responseJson(Response::HTTP_OK, 'Sucesso', $moedas);

    }

    public function callApi($endpoint, $parameters = [])
    {

        try {

            $url = "{$this->API_URL}/{$endpoint}";
            if (empty($parameters)) {
                $parameters = [
                    'start' => '1',
                    'limit' => '100',
                    'convert' => 'USD'
                ];
            }

            $headers = [
                'Accepts: application/json',
                'X-CMC_PRO_API_KEY: ' . $this->API_KEY
            ];
            $qs = http_build_query($parameters); // query string encode the parameters
            $request = "{$url}?{$qs}"; // create the request URL

            $curl = curl_init(); // Get cURL resource

            curl_setopt_array($curl, array(
                CURLOPT_URL => $request,            // set the request URL
                CURLOPT_HTTPHEADER => $headers,     // set the headers
                CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
            ));

            $response = curl_exec($curl); // Send the request, save the response
            curl_close($curl); // Close request
            return json_decode($response);
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }

}
