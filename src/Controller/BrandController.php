<?php

namespace App\Controller;


use App\Repository\BrandRepository;
use App\Repository\ProduitRepository;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BrandController extends AbstractController
{

    #[Route('/brand', name: 'marque_list', methods: "GET"),]
    public function index(BrandRepository $brandRepository): JsonResponse
    {
        $response = $this->statusCode(Response::HTTP_OK, $brandRepository->findDistinc());
        return $this->json($response, $response["status"], [], ["groups" => "brand:list"]);
    }
}
