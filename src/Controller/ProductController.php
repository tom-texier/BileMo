<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

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
     * @Route("/{id}", name="api_products_getProduct", methods={"GET"})
     */
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }
}
