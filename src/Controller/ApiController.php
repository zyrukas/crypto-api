<?php

namespace App\Controller;

use App\Exception\JsonResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getDecodedJsonRequest(Request $request): array
    {
        $data = \json_decode($request->getContent(), true);

        if (\json_last_error() !== 0) {
            throw new JsonResponseException(400, 'Invalid json.');
        }

        return $data;
    }
}
