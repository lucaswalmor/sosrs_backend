<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    use HasFactory;

    protected $table = 'instituicoes';
    protected $fillable = [
        'tipo_insituicao',
        'nome_instituicao',
        'responsavel',
        'instagram',
        'whatsapp',
        'email',
        'cep',
        'estado',
        'cidade',
        'bairro',
        'rua',
        'numero',
        'complemento',
        'path_logo',
    ];

    public function pets()
    {
        return $this->hasMany(PetInstituicao::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
