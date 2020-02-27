<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Exception\JsonResponseException;
use App\Manager\AssetManager;
use App\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AssetController extends ApiController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @param Request      $request
     * @param AssetManager $assetManager
     *
     * @return JsonResponse
     */
    public function create(Request $request, AssetManager $assetManager): JsonResponse
    {
        $asset = $assetManager->create($this->getUser(), $this->getDecodedJsonRequest($request));

        $this->validateAsset($assetManager, $asset);
        $assetManager->save($asset);

        return $this->json(['message' => 'Successfully created. UID: ' . $asset->getUid()], Response::HTTP_CREATED);
    }

    /**
     * @param AssetRepository     $assetRepository
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(AssetRepository $assetRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $assets = [];
        foreach ($assetRepository->findBy(['user' => $this->getUser()]) as $asset) {
            $assets[] = $normalizer->normalize($asset);
        }

        return $this->json([
            'totalMoneyInUSD' => \array_sum(\array_column($assets, 'valueInUSD')),
            'assets' => $assets,
        ]);
    }

    /**
     * @param string              $uid
     * @param AssetRepository     $assetRepository
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOne(
        string $uid,
        AssetRepository $assetRepository,
        NormalizerInterface $normalizer
    ): JsonResponse {
        return $this->json(
            $normalizer->normalize(
                $this->getAsset($assetRepository, $uid)
            )
        );
    }

    /**
     * @param string          $uid
     * @param AssetRepository $assetRepository
     * @param AssetManager    $assetManager
     *
     * @return JsonResponse
     */
    public function delete(
        string $uid,
        AssetRepository $assetRepository,
        AssetManager $assetManager
    ): JsonResponse {
        $assetManager->delete(
            $this->getAsset($assetRepository, $uid)
        );

        return $this->json(['message' => 'Successfully deleted.']);
    }

    /**
     * @param string          $uid
     * @param Request         $request
     * @param AssetRepository $assetRepository
     * @param AssetManager    $assetManager
     *
     * @return JsonResponse
     */
    public function update(
        string $uid,
        Request $request,
        AssetRepository $assetRepository,
        AssetManager $assetManager
    ): JsonResponse {
        $asset = $this->getAsset($assetRepository, $uid);
        $asset = $assetManager->update($asset, $this->getDecodedJsonRequest($request));

        $this->validateAsset($assetManager, $asset);
        $assetManager->save($asset);

        return $this->json(['message' => 'Successfully updated.']);
    }

    /**
     * @param AssetManager $assetManager
     * @param Asset        $asset
     *
     * @return void
     */
    private function validateAsset(AssetManager $assetManager, Asset $asset): void
    {
        $errors = $assetManager->validate($asset);
        if (!empty($errors)) {
            throw (new JsonResponseException(Response::HTTP_BAD_REQUEST, 'errors'))->setMessages($errors);
        }
    }

    /**
     * @param AssetRepository $assetRepository
     * @param string          $uid
     *
     * @return Asset
     */
    private function getAsset(AssetRepository $assetRepository, string $uid): Asset
    {
        $asset = $assetRepository->findOneBy(['user' => $this->getUser(), 'uid' => $uid]);

        if ($asset === null) {
            throw new JsonResponseException(404, 'Asset not found.');
        }

        return $asset;
    }
}
