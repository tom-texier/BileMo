<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/users")
 */
class UserController extends AbstractController
{
    private TagAwareCacheInterface $cache;
    private EntityManagerInterface $em;

    public function __construct(TagAwareCacheInterface $cache, EntityManagerInterface $em)
    {
        $this->cache = $cache;
        $this->em = $em;
    }

    /**
     * @Route(name="api_users_getUsersList", methods={"GET"})
     */
    public function getUsersList(Request $request, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $this->getUser();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $id_cache = "getUsersList[" . $customer->getId() . "][$page][$limit]";

        $jsonUsersList = $this->cache->get($id_cache, function (ItemInterface $item) use ($userRepository, $serializer, $page, $limit, $customer) {
            $item->tag("usersCache");

            $usersList = $userRepository->findBy_ByPage([
                'customer' => $customer->getId()
            ], $page, $limit);
            $context = SerializationContext::create()->setGroups(['user:read']);

            return $serializer->serialize($usersList, 'json', $context);
        });

        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="api_users_getUserDetails", methods={"GET"})
     */
    public function getUserDetails(User $user, SerializerInterface $serializer): JsonResponse
    {
        if($user->getCustomer() !== $this->getUser()) {
            throw new NotFoundHttpException("Cet utilisateur n'existe pas ou n'est pas dans votre liste d'utilisateurs.");
        }

        $context = SerializationContext::create()->setGroups(['user:read']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    /**
     * @Route(name="api_users_createUser", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user
            ->setCustomer($this->getUser())
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $errors = $validator->validate($user);

        if ($errors->count()) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($user);
        $this->em->flush();

        $context = SerializationContext::create()->setGroups(['user:read']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        $location = $this->generateUrl('api_users_getUserDetails', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ['Location:' => $location], true);
    }

    /**
     * @Route("/{id}", name="api_users_deleteUser", methods={"DELETE"})
     */
    public function deleteUser(User $user): JsonResponse
    {
        if($user->getCustomer() !== $this->getUser()) {
            throw new NotFoundHttpException("Cet utilisateur n'existe pas ou n'est pas dans votre liste d'utilisateurs.");
        }

        $this->em->remove($user);
        $this->em->flush();
        $this->cache->invalidateTags(['usersCache']);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
