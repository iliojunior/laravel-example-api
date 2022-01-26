<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\ApplicationServices\CompaniesApplicationService;
use App\Http\Requests\CompanyRequest;

class CompaniesController extends Controller
{

    private CompaniesApplicationService $companiesApplicationService;

    public function __construct(CompaniesApplicationService $companiesApplicationService)
    {
        $this->companiesApplicationService = $companiesApplicationService;
    }

    public function index(string $cnpj)
    {
        \Log::info("Received new request from cnpj: $cnpj");
        $body = $this->companiesApplicationService->getAndSaveCompany($cnpj)->toArray();
        return response()->json($body);
    }

    public function update(string $cnpj)
    {
        \Log::info("Received a delete request from cnpj: $cnpj");
        $body = $this->companiesApplicationService->updateCompany($cnpj)->toArray();
        
        return response()->json($body);
    }

    public function delete(string $cnpj)
    {
        \Log::info("Received a delete request from cnpj: $cnpj");
        $this->companiesApplicationService->delete($cnpj);

        return response()->noContent();
    }

}