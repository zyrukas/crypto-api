<?php

namespace App\Controller;

use App\Manager\AssetManager;
use App\Repository\AssetRepository;
use App\Serializer\AssetNormalizer;
use App\Service\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AssetController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @param Request           $request
     * @param UserAuthenticator $userAuthenticator
     * @param AssetManager      $assetManager
     *
     * @return JsonResponse
     */
    public function create(
        Request $request,
        UserAuthenticator $userAuthenticator,
        AssetManager $assetManager
    ): JsonResponse {
        if (!$user = $userAuthenticator->authenticate($request->query->get('token'))) {
            return $this->json(['message' => 'Invalid token.']);
        }

        if (!$data = \json_decode($request->getContent(), true)) {
            return $this->json(['message' => 'Invalid json.']);
        }

        try {
            $asset = $assetManager->create($user, $data);
        } catch (\Throwable $throwable) {
            return $this->json(['message' => 'Invalid json. Probably the types or missing fields.']);
        }

        $errors = $assetManager->validate($asset);
        if (!empty($errors)) {
            return $this->json(['message' => $errors]);
        }

        $assetManager->save($asset);

        return $this->json([
            'message' => 'Successfully created. UID: ' . $asset->getUid(),
        ]);
    }

    /**
     * @param Request           $request
     * @param UserAuthenticator $userAuthenticator
     * @param AssetRepository   $assetRepository
     * @param AssetNormalizer   $assetNormalizer
     *
     * @return JsonResponse
     */
    public function list(
        Request $request,
        UserAuthenticator $userAuthenticator,
        AssetRepository $assetRepository,
        AssetNormalizer $assetNormalizer
    ): JsonResponse {
        if (!$user = $userAuthenticator->authenticate($request->query->get('token'))) {
            return $this->json(['message' => 'Invalid token.']);
        }
        $assets = $assetNormalizer->normalize($assetRepository->findBy(['user' => $user]));

        return $this->json([
            'totalMoneyInUSD' => \array_sum(\array_column($assets, 'valueInUSD')),
            'assets' => $assets,
        ]);
    }

    /**
     * @param string            $uid
     * @param Request           $request
     * @param UserAuthenticator $userAuthenticator
     * @param AssetRepository   $assetRepository
     * @param AssetNormalizer   $assetNormalizer
     *
     * @return JsonResponse
     */
    public function getOne(
        string $uid,
        Request $request,
        UserAuthenticator $userAuthenticator,
        AssetRepository $assetRepository,
        AssetNormalizer $assetNormalizer
    ): JsonResponse {
        if (!$user = $userAuthenticator->authenticate($request->query->get('token'))) {
            return $this->json(['message' => 'Invalid token.']);
        }

        if (!$asset = $assetRepository->findOneBy(['user' => $user, 'uid' => $uid])) {
            return $this->json(['message' => 'Asset not found.']);
        }

        return $this->json($assetNormalizer->normalizeAsset($asset));
    }

    /**
     * @param string            $uid
     * @param Request           $request
     * @param UserAuthenticator $userAuthenticator
     * @param AssetRepository   $assetRepository
     * @param AssetManager      $assetManager
     *
     * @return JsonResponse
     */
    public function delete(
        string $uid,
        Request $request,
        UserAuthenticator $userAuthenticator,
        AssetRepository $assetRepository,
        AssetManager $assetManager
    ): JsonResponse {
        if (!$user = $userAuthenticator->authenticate($request->query->get('token'))) {
            return $this->json(['message' => 'Invalid token.']);
        }

        if (!$asset = $assetRepository->findOneBy(['user' => $user, 'uid' => $uid])) {
            return $this->json(['message' => 'Asset not found.']);
        }
        $assetManager->delete($asset);

        return $this->json(['message' => 'Successfully deleted.']);
    }

    /**
     * @param string            $uid
     * @param Request           $request
     * @param UserAuthenticator $userAuthenticator
     * @param AssetRepository   $assetRepository
     * @param AssetManager      $assetManager
     *
     * @return JsonResponse
     */
    public function update(
        string $uid,
        Request $request,
        UserAuthenticator $userAuthenticator,
        AssetRepository $assetRepository,
        AssetManager $assetManager
    ): JsonResponse {
        if (!$user = $userAuthenticator->authenticate($request->query->get('token'))) {
            return $this->json(['message' => 'Invalid token.']);
        }

        if (!$data = \json_decode($request->getContent(), true)) {
            return $this->json(['message' => 'Invalid json.']);
        }

        if (!$asset = $assetRepository->findOneBy(['user' => $user, 'uid' => $uid])) {
            return $this->json(['message' => 'Asset not found.']);
        }

        $asset = $assetManager->update($asset, $data);

        $errors = $assetManager->validate($asset);
        if (!empty($errors)) {
            return $this->json(['message' => $errors]);
        }
        $assetManager->save($asset);

        return $this->json(['message' => 'Successfully updated.']);
    }
}
