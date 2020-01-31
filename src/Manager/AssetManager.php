<?php

namespace App\Manager;

use App\Entity\Asset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AssetManager
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return Asset
     */
    public function create(User $user, array $data): Asset
    {
        return (new Asset())
            ->setUser($user)
            ->setLabel($data['label'])
            ->setCurrency($data['currency'])
            ->setValue($data['value'])
            ->setUid(\md5(\uniqid()));
    }

    /**
     * @param Asset $asset
     *
     * @return void
     */
    public function save(Asset $asset): void
    {
        $this->entityManager->persist($asset);
        $this->entityManager->flush();
    }

    /**
     * @param Asset $asset
     *
     * @return void
     */
    public function delete(Asset $asset): void
    {
        $this->entityManager->remove($asset);
        $this->entityManager->flush();
    }

    /**
     * @param Asset $asset
     * @param array $data
     *
     * @return Asset
     */
    public function update(Asset $asset, array $data): Asset
    {
        if (isset($data['label'])) {
            $asset->setLabel($data['label']);
        }
        if (isset($data['currency'])) {
            $asset->setCurrency($data['currency']);
        }
        if (isset($data['value'])) {
            $asset->setValue($data['value']);
        }

        return $asset;
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function validate(Asset $asset): array
    {
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($this->validator->validate($asset) as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $messages;
    }
}
