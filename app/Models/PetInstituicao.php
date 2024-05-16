<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetInstituicao extends Model
{
    use HasFactory;
    protected $table = 'pets_instituicao';

    protected $fillable = [
        'nome',
        'porte',
        'pelagem',
        'raca',
        'foto',
        'encontrado',
        'descricao',
        'instituicao_id',
        'status',
        'especie',
        'outra_especie',
    ];

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }
}
