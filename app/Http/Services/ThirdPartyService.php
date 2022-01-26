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
        $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        return strtoupper(strtr($municipio, $unwanted_array));
    }

}