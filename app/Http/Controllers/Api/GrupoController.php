<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GruposResource;
use App\Models\Grupo;
use App\Models\Moeda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class GrupoController extends Controller
{

    const PER_PAGE = 20;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $rows = Grupo::query();
            $rows->orderBy('nome', 'ASC');

            $res = $rows //->orderBy($sort, $dir)
                ->latest()
                ->paginate(self::PER_PAGE)
                ->appends($request->query());

            $rows = GruposResource::collection($res)
                ->response()
                ->getData(true);

            return responseJson(Response::HTTP_OK, 'Sucesso', $rows);

        } catch (\Exception $e) {
            return responseJson(Response::HTTP_INTERNAL_SERVER_ERROR, 'Houve um erro desconhecido!', ['data' => ['error' => $e->getMessage()]]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return responseJson(Response::HTTP_OK, 'N/D', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (empty($request->moedas)) {
                return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['moedas' => 'Campo Obrigatório']]);
            }

            if (empty($request->nome_grupo)) {
                return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['nome_grupo' => 'Campo Obrigatório']]);
            }

            $moedas = explode(",", $request->moedas);
            if (!is_array($moedas)) {
                $moedas = (array)$moedas;
            }

            $slug = slugify($request->nome_grupo);
            if (Grupo::where('slug', $slug)->first()) {
                return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['nome_grupo' => 'Já existe um grupo com este nome!']]);
            }
            $ids = [];

            foreach ($moedas as $moeda) {
                $data = Moeda::where('symbol', trim($moeda))->first();
                if (!$data) {
                    DB::rollBack();
                    return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['moedas' => "Moeda '{$moeda}' não existe!"]]);
                }
                if (!in_array($data->id, $ids)) {
                    Grupo::create([
                        'nome' => $request->nome_grupo,
                        'slug' => $slug,
                        'moeda' => $data->symbol,
                        'moeda_id' => $data->id,
                    ]);
                    $ids[] = $data->id;
                }
            }
            DB::commit();
            return responseJson(Response::HTTP_OK, "'{$request->nome_grupo}' cadastrado com sucesso", []);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseJson(Response::HTTP_INTERNAL_SERVER_ERROR, 'Erro', ['data' => ['msg' => 'Houve um erro desconhecido!', 'error' => $e->getMessage()]]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return Response
     */
    public function show($slug)
    {
        try {
            $grupo = Grupo::where('slug', $slug);
            if(!$grupo->count()) {
                return responseJson(Response::HTTP_NOT_FOUND, 'Grupo não encontrado', []);
            }

            $res = $grupo
                ->latest()
                ->paginate(self::PER_PAGE);

            $rows = GruposResource::collection($res)
                ->response()
                ->getData(true);

            return responseJson(Response::HTTP_OK, 'Sucesso', $rows);

        } catch (\Exception $e) {
            return responseJson(Response::HTTP_INTERNAL_SERVER_ERROR, 'Houve um erro desconhecido!', ['data' => ['error' => $e->getMessage()]]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return responseJson(Response::HTTP_OK, 'N/D', []);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $slug
     * @return Response
     */
    public function update(Request $request, $slug)
    {
        try {

            if (empty($request->moedas)) {
                return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['moedas' => 'Campo Obrigatório']]);
            }

            $grupo = Grupo::where('slug', $slug);
            if(!$grupo->count()) {
                return responseJson(Response::HTTP_NOT_FOUND, 'Grupo não encontrado', []);
            }

            DB::beginTransaction();

            $ids = [];
            $moedas = explode(",", $request->moedas);
            if (!is_array($moedas)) {
                $moedas = (array)$moedas;
            }
            $gr = clone $grupo;
            $grupo = $grupo->first();

            foreach ($moedas as $moeda) {
                $data = Moeda::where('symbol', trim($moeda))->first();
                if (!$data) {
                    DB::rollBack();
                    return responseJson(Response::HTTP_BAD_REQUEST, 'Erro', ['data' => ['moedas' => "Moeda '{$moeda}' não existe!"]]);
                }
                if (!in_array($data->id, $ids)) {
                    Grupo::updateOrCreate(
                        [
                            'slug' => $slug,
                            'moeda_id' => $data->id,
                        ],
                        [
                            'nome' => $grupo->nome,
                            'slug' => $slug,
                            'moeda' => $data->symbol,
                            'moeda_id' => $data->id,
                    ]);
                    $ids[] = $data->id;
                }
            }
            DB::commit();

            $res = $gr
                ->latest()
                ->paginate(self::PER_PAGE);

            $rows = GruposResource::collection($res)
                ->response()
                ->getData(true);

            return responseJson(Response::HTTP_OK, "'{$grupo->nome}' atualizado com sucesso", $rows);

        } catch (\Exception $e) {
            return responseJson(Response::HTTP_INTERNAL_SERVER_ERROR, 'Houve um erro desconhecido!', ['data' => ['error' => $e->getMessage()]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($slug)
    {
        try {

            $grupo = Grupo::where('slug', $slug);
            if (!$grupo->count()) {
                return responseJson(Response::HTTP_NOT_FOUND, 'Grupo não encontrado', []);
            }

            $grupo->delete();
            return responseJson(Response::HTTP_OK, 'Grupo deletado com sucesso', []);

        } catch (\Exception $e) {
            return responseJson(Response::HTTP_INTERNAL_SERVER_ERROR, 'Houve um erro na tentativa de apagar o registro!', ['data' => ['error' => $e->getMessage()]]);
        }

    }

}
