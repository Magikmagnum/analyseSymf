<?php

namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Brand;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Characteristic;
use App\Repository\ProduitRepository;
use App\Repository\BrandRepository;
use App\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Helpers\AnalyserCroquette;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;



class CroquetteController extends AbstractController
{


    #[Route('/croquette', name: 'croquette_list', methods: "GET"),]
    public function list(ProduitRepository $produitRepository): JsonResponse
    {
        $response = $this->statusCode(Response::HTTP_OK, $produitRepository->findAll());
        return $this->json($response, $response["status"], [], ["groups" => "produit:list"]);
    }


    #[Route('/croquette/{id}', name: 'croquette_show', methods: "GET"),]
    public function showOne($id, ProduitRepository $produitRepository): JsonResponse
    {
        if ($produits = $produitRepository->findOneBy(['id' => $id])) {
            $response = $this->statusCode(Response::HTTP_OK, $produits);
            return $this->json($response, $response["status"], [], ["groups" => "produit:list"]);
        }
        $response = $this->statusCode(Response::HTTP_NOT_FOUND);
        return $this->json($response, $response["status"]);
    }



    #[Route('/croquette_by_brand/{brand}', name: 'marque_show', methods: "GET"),]
    public function showByBrand($brand, ProduitRepository $produitRepository): JsonResponse
    {
        $products = $produitRepository->findDistinc($brand);
        $response = $this->statusCode(Response::HTTP_OK, $products);
        return $this->json($response, $response["status"], [], ["groups" => "brand:list"]);
    }
}
