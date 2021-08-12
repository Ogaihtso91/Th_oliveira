<?php
/**
 * Created by PhpStorm.
 * User: thiag
 * Date: 11/08/2018
 * Time: 14:48
 */

namespace App\Http\Controllers\ServiceProvider;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\TypeProviderRepository;

class ServiceProvidersController extends ServiceProviderBaseController
{
    private $repository;
    private $typeProviderRepository;

    public function __construct(
        ServiceProviderRepository $serviceProvider,
        TypeProviderRepository $typeProviderRepository) 
    {
        parent::__construct();
        $this->repository = $serviceProvider;
        $this->typeProviderRepository = $typeProviderRepository;
    }

    public function edit(Request $r)
    {
        $serviceProvider = $this->serviceProvider;
        if($r->isMethod('post')){
            try {
                $this->repository->update($serviceProvider, $r->all());
                return redirect()->route('serviceProvider.index')->with('success', 'Atualizado com Sucesso');

            } catch(\Exception $e) {
                return redirect()->back()->withInput($r->all())->with('error', 'Ocorreu um erro ao atualizar');
            }
        }
        $typeProviders = $this->typeProviderRepository->getList();
        return view('serviceProvider.serviceProvider.edit', compact('typeProviders'));
    }


}