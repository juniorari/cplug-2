<?php

use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\MoedaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/moedas/atualiza-banco', [MoedaController::class, 'index'])->name('atualiza-banco');
Route::get('/moedas/listar', [MoedaController::class, 'listar'])->name('listar');

//Route::get('/listar-grupo', [MoedaController::class, 'listarGrupo'])->name('listarGrupo');

//GET	/sharks	index	sharks.index
//GET	/sharks/create	create	sharks.create
//POST	/sharks	store	sharks.store
//GET	/sharks/{id}	show	sharks.show
//GET	/sharks/{id}/edit	edit	sharks.edit
//PUT/PATCH	/sharks/{id}	update	sharks.update
//DELETE	/sharks/{id}	destroy	sharks.destroy
//Route::get('grupos', [GrupoController::class, 'index']);
//Route::get('grupos/{slug}', [GrupoController::class, 'index']);
//Route::post('grupos', [GrupoController::class, 'store']);
//Route::put('grupos/{slug}', [GrupoController::class, 'update']);
//Route::delete('grupos/{slug}', [GrupoController::class, 'destroy']);
Route::resource('grupos', GrupoController::class);
