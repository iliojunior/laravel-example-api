<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Support\Collection;

class CompanyService extends Service
{
    public function saveNew(string $cnpj, Collection $wsCompanyData, Collection $wsGeolocationData)
    {
        \Log::info("Saving new company $cnpj");
        $atividadePrincipal = $wsCompanyData->get('atividade_principal');

        $company = new Company();

        $company->cnpj = $cnpj;
        $company->razao_social = $wsCompanyData->get('nome');
        $company->nome_fantasia = $wsCompanyData->get('fantasia');
        $company->atividade_principal = $this->formatAtividadePrincipal(array_shift($atividadePrincipal));
        $company->data_abertura = Carbon::createFromFormat('d/m/Y', $wsCompanyData->get('abertura'));
        $company->natureza_juridica = $wsCompanyData->get('natureza_juridica');
        $company->cep = $wsCompanyData->get('cep');
        $company->logradouro = $wsCompanyData->get('logradouro');
        $company->cidade = $wsCompanyData->get('municipio');
        $company->estado = $wsCompanyData->get('uf');
        $company->codigo_ibge = $wsGeolocationData->get('id');

        //TODO: That was not possible to find country by anything else.
        $company->pais = 'any';
        
        $company->save();

        \Log::info("Saved new company $cnpj");

        return $company;
    }

    public function update(string $cnpj, Collection $wsCompanyData, Collection $wsGeolocationData, Company $company)
    {
        \Log::info("Saving new company $cnpj");
        $atividadePrincipal = $wsCompanyData->get('atividade_principal');

        $company->cnpj = $cnpj;
        $company->razao_social = $wsCompanyData->get('nome');
        $company->nome_fantasia = $wsCompanyData->get('fantasia');
        $company->atividade_principal = $this->formatAtividadePrincipal(array_shift($atividadePrincipal));
        $company->data_abertura = Carbon::createFromFormat('d/m/Y', $wsCompanyData->get('abertura'));
        $company->natureza_juridica = $wsCompanyData->get('natureza_juridica');
        $company->cep = $wsCompanyData->get('cep');
        $company->logradouro = $wsCompanyData->get('logradouro');
        $company->cidade = $wsCompanyData->get('municipio');
        $company->estado = $wsCompanyData->get('uf');
        $company->codigo_ibge = $wsGeolocationData->get('id');

        //TODO: That was not possible to find country by anything else.
        $company->pais = 'any';
        
        $company->save();

        \Log::info("Saved new company $cnpj");

        return $company;
    }

    public function findByCnpj(string $cnpj)
    {
        return Company::where('cnpj', $cnpj)->first();
    }

    public function findByCnpjOrFail(string $cnpj)
    {
        return Company::where('cnpj', $cnpj)->firstOrFail();
    }

    public function delete(string $cnpj)
    {
        \Log::info("Deleting company $cnpj");

        try
        {
            $company = $this->findByCnpjOrFail($cnpj);
            $company->delete();

            \Log::info("Deleted company $cnpj");
        } catch (\Exception $ex) {
            \Log::error("Company $cnpj not found");
            throw $ex;
        }
    }

    private function formatAtividadePrincipal(array $atividadePrincipal)
    {
        return $atividadePrincipal['code'] . ' - ' . $atividadePrincipal['text'];
    }
}