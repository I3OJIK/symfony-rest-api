<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для работы с пользователями через API.
 */
#[Route('/api/users', name: 'app_api_user')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService) {}

    /**
     * Получение пользователя по ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->json(
                ['error' => 'Пользователь не найден'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            'status' => 'success',
            'data'=> [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ]
        ]);
    }

    /**
     * Создание нового пользователя
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $result = $this->userService->createUser($data);

        if ($result['status'] === 'error') {
            return $this->json(
                ['errors' => $result['messages']],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json($result, Response::HTTP_CREATED);
    }

    /**
     * Обновление пользователя
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update($id, Request $request): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->json(
                ['error' => 'Пользователь не найден'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $request->toArray();

        $result = $this->userService->updateUser($user, $data);

        if ($result['status'] === 'error') {
            return $this->json(
                ['errors' => $result['messages']],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Удаление пользователя по ID
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->json(
                ['error' => 'Пользователь не найден'],
                Response::HTTP_NOT_FOUND
            );
        }

        $result = $this->userService->deleteUser($user);

        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Аутентификация пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $result = $this->userService->loginUser($username, $password);

        if ($result['status'] === 'error') {
            return $this->json(
                ['errors' => $result['message']],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json($result, Response::HTTP_OK);
    }
}
