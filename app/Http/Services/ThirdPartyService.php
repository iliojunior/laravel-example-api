<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class ThirdPartyService extends Service
{

    private static $receitaWsUrl = 'https://www.receitaws.com.br/v1/cnpj/:parameter';
    private static $estadosWsUrl = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados';
    private static $municipiosWsUrl = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/:parameter/municipios';

    public function getCompanyData(string $cnpj)
    {
        try
        {
            \Log::info("Retrieving company data from $cnpj");

            $url = $this->getUrl(self::$receitaWsUrl, $cnpj);
            $response = $this->getHttpClient()->get($url);
            $responseBody = $response->json();

            return collect($responseBody)->only([
                'nome',
                'fantasia',
                'atividade_principal',
                'abertura',
                'natureza_juridica',
                'logradouro',
                'cep',
                'uf',
                'municipio',
                'bairro',
                'cnpj',
            ]);

        } catch (\Exception $ex) {
            \Log::error("Error when trying to get company data. " . $ex->getMessage());
            throw $ex;
        }
    }

    public function getGeolocationData(string $uf, string $municipio)
    {
        $ufData = $this->getUfData($uf);
        $municipioData = $this->getMunicipioData($ufData['id'], $municipio);

        return collect($municipioData);
    }

    private function getUfData(string $uf)
    {
        $response = $this->getHttpClient()
            ->get(self::$estadosWsUrl)
            ->json();
        
        return collect($response)
            ->filter(fn ($item) => $item['sigla'] == strtoupper($uf))
            ->first();
    }

    private function getMunicipioData(string $uf, string $municipio)
    {
        $url = $this->getUrl(self::$municipiosWsUrl, $uf);

        $response = $this->getHttpClient()
            ->get($url)
            ->json();
        
        return collect($response)
            ->filter(fn ($item) => $this->normalizeMunicipio($item['nome']) == $this->normalizeMunicipio($municipio))
            ->map(fn ($item) => [
                'id' => $item['id'], 
                'nome' => $item['nome'],
            ])
            ->first();
    }

    private function getHttpClient()
    {
        return Http::withOptions(['http_errors' => true]);
    }

    private function getUrl(string $urlBase, string $parameter)
    {
        return str_replace(':parameter', $parameter, $urlBase);
    }

    private function normalizeMunicipio(string $municipio)
    {
        $unwanted_array = array('??'=>'S', '??'=>'s', '??'=>'Z', '??'=>'z', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'C', '??'=>'E', '??'=>'E',
                            '??'=>'E', '??'=>'E', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'N', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'U',
                            '??'=>'U', '??'=>'U', '??'=>'U', '??'=>'Y', '??'=>'B', '??'=>'Ss', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'c',
                            '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'o', '??'=>'n', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o',
                            '??'=>'o', '??'=>'o', '??'=>'u', '??'=>'u', '??'=>'u', '??'=>'y', '??'=>'b', '??'=>'y' );
        return strtoupper(strtr($municipio, $unwanted_array));
    }

}