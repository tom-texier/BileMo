<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    private TagAwareCacheInterface $cache;

    public function __construct(TagAwareCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Récupère tous les produits
     *
     * @OA\Response(
     *     response="200",
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Response(
     *     response="401",
     *     description="JWT token invalide",
     *     @OA\JsonContent(ref="#/components/schemas/ExpiredToken")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws InvalidArgumentException
     * @Route(name="api_products_getProductsList", methods={"GET"})
     */
    public function getProductsList(Request $request, ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $id_cache = "getProductsList[$page][$limit]";

        $jsonProductsList = $this->cache->get($id_cache, function (ItemInterface $item) use ($productRepository, $serializer, $page, $limit) {
            $item->tag("productsCache");
            return $serializer->serialize($productRepository->findAllByPage($page, $limit), 'json');
        });

        return new JsonResponse($jsonProductsList, Response::HTTP_OK, [], true);
    }

    /**
     * Récupère les détails d'un produit
     *
     * @OA\Response(
     *     response="200",
     *     description="Retourne le produit",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Response(
     *     response="401",
     *     description="JWT token invalide",
     *     @OA\JsonContent(ref="#/components/schemas/ExpiredToken")
     * )
     * @OA\Tag(name="Products")
     *
     * @param Product $product
     * @return JsonResponse
     * @Route("/{id}", name="api_products_getProduct", methods={"GET"})
     */
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }
}
