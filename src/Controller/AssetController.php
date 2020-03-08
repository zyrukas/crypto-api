<?php

namespace App\Controller;

use App\Adapter\AssetPricesAdapter;
use App\Entity\Asset;
use App\Exception\JsonResponseException;
use App\Manager\AssetManager;
use App\Model\Response\CreatedResponse;
use App\Model\Response\UpdatedResponse;
use App\Repository\AssetRepository;
use App\Wrapper\ListResponseWrapper;
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
     * @param Request             $request
     * @param AssetManager        $assetManager
     * @param AssetPricesAdapter  $assetPricesAdapter
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function create(
        Request $request,
        AssetManager $assetManager,
        AssetPricesAdapter $assetPricesAdapter,
        NormalizerInterface $normalizer
    ): JsonResponse {
        $asset = $assetManager->create($this->getUser(), $this->getDecodedJsonRequest($request));

        $this->validateAsset($assetManager, $asset);
        $assetManager->save($asset);

        $response = (new CreatedResponse())->setAsset($assetPricesAdapter->adapt($asset));

        return $this->json($normalizer->normalize($response), Response::HTTP_CREATED);
    }

    /**
     * @param ListResponseWrapper $listResponseWrapper
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(
        ListResponseWrapper $listResponseWrapper,
        NormalizerInterface $normalizer
    ): JsonResponse {
        $listResponse = $listResponseWrapper->wrap($this->getUser(), $this->getParameter('default_currency'));

        return $this->json($normalizer->normalize($listResponse));
    }

    /**
     * @param string              $uid
     * @param AssetRepository     $assetRepository
     * @param NormalizerInterface $normalizer
     * @param AssetPricesAdapter  $assetPricesAdapter
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOne(
        string $uid,
        AssetRepository $assetRepository,
        NormalizerInterface $normalizer,
        AssetPricesAdapter $assetPricesAdapter
    ): JsonResponse {
        return $this->json(
            $normalizer->normalize(
                $assetPricesAdapter->adapt(
                    $this->getAsset($assetRepository, $uid)
                )
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
     * @param string              $uid
     * @param Request             $request
     * @param AssetRepository     $assetRepository
     * @param AssetManager        $assetManager
     * @param AssetPricesAdapter  $assetPricesAdapter
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function update(
        string $uid,
        Request $request,
        AssetRepository $assetRepository,
        AssetManager $assetManager,
        AssetPricesAdapter $assetPricesAdapter,
        NormalizerInterface $normalizer
    ): JsonResponse {
        $asset = $this->getAsset($assetRepository, $uid);
        $asset = $assetManager->update($asset, $this->getDecodedJsonRequest($request));

        $this->validateAsset($assetManager, $asset);
        $assetManager->save($asset);

        $response = (new UpdatedResponse())->setAsset($assetPricesAdapter->adapt($asset));

        return $this->json($normalizer->normalize($response));
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
