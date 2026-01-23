<?php

namespace App\Service\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCommandService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    /**
     * Метод создаёт Администратора и используется в Command app:admin-create
     * @param string $name
     * @param string $lastName
     * @param string $password
     * @param string $mail
     * @param string $phone
     * @return string
     */
    public function makeAdmin(
        string $name,
        string $lastName,
        string $password,
        string $mail,
        string $phone,
    ): string
    {
        $user = new User();
        $user->setName($name);
        $user->setLastName($lastName);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setEmail($mail);
        $user->setPhone($phone);
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return 'Админ создан успешно';
    }

}
