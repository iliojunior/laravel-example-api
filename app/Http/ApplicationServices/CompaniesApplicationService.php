<?php

namespace App\Http\ApplicationServices;

use App\Http\Services\ThirdPartyService;
use App\Http\Services\CompanyService;
use App\Exceptions\CnpjInvalid;

class CompaniesApplicationService extends ApplicationService
{

    private ThirdPartyService $thirdPartyService;

    private CompanyService $companyService;

    public function __construct(ThirdPartyService $thirdPartyService, CompanyService $companyService)
    {
        $this->thirdPartyService = $thirdPartyService;
        $this->companyService = $companyService;
    }

    public function getAndSaveCompany(string $cnpj)
    {
        $numbersCnpj = $this->validateCnpjAndGet($cnpj);

        $company = $this->companyService->findByCnpj($numbersCnpj);

        if ($company) {
            return $company;
        }
        
        $wsCompanyData = $this->thirdPartyService->getCompanyData($numbersCnpj);
        $wsGeolocationData = $this->thirdPartyService->getGeolocationData($wsCompanyData->get('uf'), $wsCompanyData->get('municipio'));
    
        $entity = $this->companyService->saveNew($numbersCnpj, $wsCompanyData, $wsGeolocationData);
        
        return $entity;
    }

    public function updateCompany(string $cnpj)
    {
        $numbersCnpj = $this->validateCnpjAndGet($cnpj);

        $company = $this->companyService->findByCnpjOrFail($numbersCnpj);
        $wsCompanyData = $this->thirdPartyService->getCompanyData($numbersCnpj);
        $wsGeolocationData = $this->thirdPartyService->getGeolocationData($wsCompanyData->get('uf'), $wsCompanyData->get('municipio'));
        $entity = $this->companyService->update($numbersCnpj, $wsCompanyData, $wsGeolocationData, $company);
        
        return $entity;
    }

    public function delete(string $cnpj)
    {
        $numbersCnpj = $this->validateCnpjAndGet($cnpj);
        $this->companyService->delete($numbersCnpj);
    }

    private function validateCnpjAndGet(string $cnpj)
    {
        $numbersCnpj = preg_replace('/[^\d+]/', '', $cnpj);

        if (strlen($numbersCnpj) != 14) {
            throw new CnpjInvalid();
        }

        return $numbersCnpj;
    }
}