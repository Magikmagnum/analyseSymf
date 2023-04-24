<?php

namespace App\Controller;


use App\Repository\ProduitRepository;
use App\Controller\Helpers\Analyser\AnalyserCroquette;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AnalyseController extends AbstractController
{
    #[Route('/analyse', name: 'croquette_analyse', methods: "POST"),]
    public function analyseAll(Request $request, ProduitRepository $produitRepository): JsonResponse
    {
        $data = json_decode($request->getContent());

        $analyseur = new AnalyserCroquette($data, $produitRepository->findAll());
        $dataFilter = $analyseur->getAnalyse();
        $response = $this->statusCode(Response::HTTP_OK, $dataFilter);
        return $this->json($response, $response["status"]);
    }



    #[Route('/analyse/{id}', name: 'croquette_analyseOne', methods: "POST"),]
    public function analyseOne($id, Request $request, ProduitRepository $produitRepository): JsonResponse
    {
        if ($produit = $produitRepository->findOneBy(['id' => $id])) {

            $data = json_decode($request->getContent());

            $data->animal = "chat";
            $analyseur = new AnalyserCroquette($data, $produit);

            $dataFilter = $analyseur->getAnalyseOne();
            $response = $this->statusCode(Response::HTTP_OK, $dataFilter);
            return $this->json($response, $response["status"]);
        }
        $response = $this->statusCode(Response::HTTP_NOT_FOUND);
        return $this->json($response, $response["status"]);
    }
}
