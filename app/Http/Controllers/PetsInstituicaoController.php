<?php

namespace App\Http\Controllers;

use App\Models\PetInstituicao;
use Illuminate\Http\Request;

class PetsInstituicaoController extends Controller
{
    public function atualizarStatusPet(Request $request)
    {
        $pet = PetInstituicao::where('id', $request->id)->first();

        if (empty($pet)) {
            return response()->json(['error' => 'Nenhum pet encontrado'], 400);
        }

        $pet->status = $request->status;

        $pet->update();

        return response()->json(['success' => 'Status alterado com sucesso'], 200);
    }

    public function excluirPet($idPet)
    {
        PetInstituicao::where('id', $idPet)->delete();

        return response()->json(['success' => 'PET deletado com sucesso'], 200);
    }
}
