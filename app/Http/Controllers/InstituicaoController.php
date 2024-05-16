<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use App\Models\PetInstituicao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstituicaoController extends Controller
{
    public function instituicoes(Request $request)
    {
        $nome = $request->input('nome');
        $cidade = $request->input('cidade');

        $query = Instituicao::query();

        if ($nome) {
            $query->where('nome_instituicao', 'LIKE', '%' . $nome . '%');
        }

        if ($cidade) {
            $query->where('cidade', 'LIKE', '%' . $cidade . '%');
        }

        $query->orderBy('nome_instituicao', 'asc');

        $instituicoes = $query->get();
        // $instituicoes = $query->paginate(10);

        return $instituicoes->map(function ($instituicao) {
            return [
                'nome' => $instituicao->nome_instituicao,
                'path_logo' => $instituicao->path_logo,
            ];
        });
    }

    public function instituicao($nome)
    {
        $instituicao = Instituicao::with('pets')->where('nome_instituicao', $nome)->first();

        return [
            'nome' => $instituicao->nome_instituicao,
            'path_logo' => $instituicao->path_logo,
        ];
    }

    public function pets(Request $request, $nomeInstituicao)
    {
        $query = Instituicao::with(['pets' => function ($query) use ($request) {
            // Filtros para os pets
            $nome = $request->input('nome');
            $especie = $request->input('especie');
            $outra_especie = $request->input('outra_especie');
            $porte = $request->input('porte');
            $pelagem = $request->input('pelagem');
            $raca = $request->input('raca');
            $status = $request->input('status');

            if ($nome) {
                $query->where('nome', 'LIKE', '%' . $nome . '%');
            }

            if ($especie) {
                $query->where('especie', $especie);
            }

            if ($outra_especie && $outra_especie == 'Outro') {
                $query->where('outra_especie', $outra_especie);
            }

            if ($porte) {
                $query->where('porte', $porte);
            }

            if ($pelagem) {
                $query->where('pelagem', $pelagem);
            }

            if ($raca) {
                $query->where('raca', $raca);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $query->orderBy('nome', 'asc');
        }])->where('nome_instituicao', $nomeInstituicao);

        // Adicione a ordenação aqui
        $query->orderBy('nome_instituicao', 'asc');

        $instituicao = $query->first();

        $dadosInstituicao = [
            'nome_instituicao' => $instituicao->nome_instituicao,
            'instagram' => $instituicao->instagram,
            'whatsapp' => $instituicao->whatsapp,
            'email' => $instituicao->email,
            'cep' => $instituicao->cep,
            'estado' => $instituicao->estado,
            'cidade' => $instituicao->cidade,
            'bairro' => $instituicao->bairro,
            'rua' => $instituicao->rua,
            'numero' => $instituicao->numero,
            'complemento' => $instituicao->complemento,
            'path_logo' => $instituicao->path_logo,
        ];

        $pets = $instituicao->pets->map(function ($pet) {
            return [
                'id' => $pet->id,
                'nome' => $pet->nome,
                'porte' => $pet->porte,
                'pelagem' => $pet->pelagem,
                'raca' => $pet->raca,
                'foto' => $pet->foto,
                'encontrado' => $pet->encontrado,
                'descricao' => $pet->descricao,
                'status' => $pet->status,
                'especie' => $pet->especie,
                'outra_especie' => $pet->outra_especie,
            ];
        });

        return response()->json(['pets' => $pets, 'instituicao' => $dadosInstituicao]);
    }

    public function pets2($nomeInstituicao)
    {
        //teste
        $instituicao = Instituicao::with('pets')->where('nome_instituicao', $nomeInstituicao)->first();

        $dadosInstituicao = [
            'nome_instituicao' => $instituicao->nome_instituicao,
            'instagram' => $instituicao->instagram,
            'whatsapp' => $instituicao->whatsapp,
            'email' => $instituicao->email,
            'cep' => $instituicao->cep,
            'estado' => $instituicao->estado,
            'cidade' => $instituicao->cidade,
            'bairro' => $instituicao->bairro,
            'rua' => $instituicao->rua,
            'numero' => $instituicao->numero,
            'complemento' => $instituicao->complemento,
            'path_logo' => $instituicao->path_logo,
        ];

        $pets = $instituicao->pets->map(function($pet) {
            return [
                'nome' => $pet->nome,
                'porte' => $pet->porte,
                'pelagem' => $pet->pelagem,
                'raca' => $pet->raca,
                'foto' => $pet->foto,
                'encontrado' => $pet->encontrado,
                'descricao' => $pet->descricao,
                'status' => $pet->status,
                'especie' => $pet->especie,
                'outra_especie' => $pet->outra_especie,
            ];
        });

        return response()->json(['pets' => $pets, 'instituicao' => $dadosInstituicao]);
    }

    public function cadastroPet(Request $request, $nomeInstituicao)
    {
        $instituicao = Instituicao::where('nome_instituicao', $nomeInstituicao)->first();

        $validator = $this->validarPet($request);

        if ($validator) {
            return response()->json(['error' => $validator], 400);
        }

        if (empty($request->imagem)) {
            return response()->json(['error' => 'Selecione a imagem do PET'], 400);
        }

        $fotoPet = $this->uploadImagem($request);

        $pet = PetInstituicao::create([
            'nome' => $request->nome,
            'porte' => $request->porte,
            'pelagem' => $request->pelagem,
            'raca' => $request->raca,
            'foto' => $fotoPet,
            'encontrado' => $request->encontrado,
            'descricao' => $request->descricao,
            'instituicao_id' => $instituicao->id,
            'status' => !empty($request->status) ? $request->status : 'Aguardando',
            'especie' => $request->especie,
            'outra_especie' => $request->outra_especie,
        ]);

        if ($pet) {
            return response()->json(['success' => 'PET cadastrado com sucesso']);
        }

        return response()->json(['error' => 'Falha ao cadastrar o PET']);
    }

    public function cadastro(Request $request)
    {
        $validator = $this->validarInstituicao($request);

        if ($validator) {
            return response()->json(['error' => $validator], 400);
        }

        $pathLogo = $this->uploadImagem($request);

        $instituicao = Instituicao::create([
            'tipo_insituicao' => $request->tipo_insituicao,
            'nome_instituicao' => $request->nome_instituicao,
            'responsavel' => $request->responsavel,
            'instagram' => $request->instagram,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'cep' => $request->cep,
            'estado' => $request->estado,
            'cidade' => $request->cidade,
            'bairro' => $request->bairro,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'path_logo' => $pathLogo,
        ]);

        $user = User::create([
            'nome' => $request->usuario['nome'],
            'email' => $request->usuario['email'],
            'telefone' => $request->usuario['telefone'],
            'password' => Hash::make($request->usuario['password']),
            'instituicao_id' => $instituicao->id
        ]);

        if ($user && $instituicao) {
            return response()->json(['success' => 'Instituição cadastrada com sucesso']);
        }

        return response()->json(['error' => 'Falha ao cadastrar instituição']);
    }

    private function uploadImagem(Request $request)
    {
        // Verifique se há uma imagem no pedido
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {

            $imagem = $request->file('imagem');

            // Converta a imagem para base64
            $imageData = file_get_contents($imagem->getRealPath());
            $base64Image = base64_encode($imageData);

            // Chave da API do imgbb
            $api_key = "dfc5891d3771645699ae35eb0cdeaadd";
            $url = "https://api.imgbb.com/1/upload?key=" . $api_key;

            // Inicialize o cURL
            $ch = curl_init($url);

            // Defina as opções do cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'image' => $base64Image
            ]);

            // Execute a requisição cURL
            $response = curl_exec($ch);

            // Verifique erros na requisição cURL
            if (curl_errno($ch)) {
                echo 'Erro ao enviar requisição cURL: ' . curl_error($ch);
            } else {
                // Decodifique a resposta JSON
                $imagemResponse = json_decode($response);

                // Verifique se a resposta contém a URL da imagem
                if (isset($imagemResponse->data->url)) {
                    return $imagemResponse->data->url;

                } else {
                    return response()->json(['error' => 'Erro ao enviar imagem']);
                }
            }

            // Feche a sessão cURL
            curl_close($ch);
        } else {
            return response()->json(['error' => 'Nenhuma imagem válida fornecida']);
        }
    }

    private function validarInstituicao($request)
    {
        $rules = [
            'tipo_insituicao' => 'required',
            'nome_instituicao' => 'required',
            'responsavel' => 'required',
            'instagram' => 'required|unique:instituicoes,instagram',
            'whatsapp' => 'required|unique:instituicoes,whatsapp',
            'email' => 'required|email|max:255|unique:instituicoes,email',
            'cep' => 'required',
            'cidade' => 'required|string|max:255',
            'estado' => 'required',
            'rua' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'numero' => 'required',
            'usuario.nome' => 'required',
            'usuario.email' => 'required|email|max:255|unique:users,email',
            'usuario.telefone' => 'required|unique:users,telefone',
            'usuario.password' => 'required',
            'imagens.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'tipo_insituicao.required' => 'O campo tipo de instituição é obrigatório.',
            'nome_instituicao.required' => 'O campo nome da instituição é obrigatório.',
            'responsavel.required' => 'O campo responsável é obrigatório.',
            'instagram.required' => 'O campo instagram é obrigatório.',
            'instagram.unique' => 'O campo instagram já está sendo utilizado.',
            'whatsapp.required' => 'O campo whatsapp é obrigatório.',
            'whatsapp.unique' => 'O campo whatsapp já está sendo utilizado.',
            'email.required' => 'O campo email da instituição é obrigatório.',
            'email.email' => 'O campo email da instituição deve ser um email válido.',
            'email.unique' => 'O campo email de instituição já está em uso.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'rua.required' => 'O campo rua é obrigatório.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'estado.required' => 'O campo estado é obrigatório.',
            'numero.required' => 'O campo número do local é obrigatório.',
            'usuario.nome.required' => 'O campo nome de usuário é obrigatório.',
            'usuario.email.required' => 'O campo email do usuário é obrigatório.',
            'usuario.email.unique' => 'O campo email de usuário já está em uso.',
            'usuario.telefone.required' => 'O campo telefone do usuário é obrigatório.',
            'usuario.telefone.unique' => 'O campo telefone de usuário já está em uso.',
            'usuario.password.required' => 'O campo senha é obrigatório.',
            'imagens.*.required' => 'Por favor, selecione uma imagem.',
            'imagens.*.image' => 'O arquivo deve ser uma imagem.',
            'imagens.*.mimes' => 'A imagem deve estar em um dos formatos: jpeg, png, jpg ou gif.',
            'imagens.*.max' => 'A imagem não deve ser maior que 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
    }

    private function validarPet($request)
    {
        $rules = [
            'nome' => 'required',
            'porte' => 'required',
            'especie' => 'required',
            'pelagem' => 'required',
            'imagens.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'porte.required' => 'O campo porte é obrigatório.',
            'especie.required' => 'O campo espécie é obrigatório.',
            'pelagem.required' => 'O campo pelagem é obrigatório.',
            'imagens.*.required' => 'Por favor, selecione uma imagem.',
            'imagens.*.image' => 'O arquivo deve ser uma imagem.',
            'imagens.*.mimes' => 'A imagem deve estar em um dos formatos: jpeg, png, jpg ou gif.',
            'imagens.*.max' => 'A imagem não deve ser maior que 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
    }
}
