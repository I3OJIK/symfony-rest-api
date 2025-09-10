<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private UserRepository $repo,
        private UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function createUser(array $data): array
    {
        $user = new User();
        // хэширование пароля
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($hashedPassword);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return ['status' => 'error', 'messages' => $messages, 'code' => Response::HTTP_BAD_REQUEST];
        }

        $this->em->persist($user);
        $this->em->flush();

        return ['status' => 'success', 'id' => $user->getId(), 'code' => Response::HTTP_CREATED];
    }

    public function updateUser(User $user, array $data): array
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($hashedPassword);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return ['status' => 'error', 'messages' => $messages, 'code' => Response::HTTP_BAD_REQUEST];
        }

        $this->em->flush();

        return ['status' => 'success', 'code' => Response::HTTP_OK];
    }

    public function deleteUser(User $user): array
    {
        $this->em->remove($user);
        $this->em->flush();

        return ['status' => 'success', 'code' => Response::HTTP_OK];
    }

    public function loginUser(string $username, string $password): array
    {
        $user = $this->repo->findOneBy(['username' => $username]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return ['status' => 'error', 'message' => 'Неверный логин или пароль', 'code' => Response::HTTP_UNAUTHORIZED];
        }

        return [
            'status' => 'success',
            'data' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ],
            'code' => Response::HTTP_OK
        ];
    }

    public function getUserById(int $id): ?User
    {
        return $this->repo->find($id);
    }
}
