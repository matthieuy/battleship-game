<?php

namespace App\Controller;

use App\Weapons\WeaponRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class WeaponController
 */
class WeaponController extends AbstractController
{
    /**
     * Get the weapon list
     * @Route(
     *     name="weapons.list",
     *     path="/weapons.json",
     *     methods={"GET"},
     *     options={"expose"="true"})
     *
     * @param WeaponRegistry      $weaponRegistry
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     */
    public function listWeapon(WeaponRegistry $weaponRegistry, NormalizerInterface $normalizer): JsonResponse
    {
        $weapons = $normalizer->normalize($weaponRegistry->getAllWeapons(), null, ['groups' => 'weapon']);

        return new JsonResponse($weapons);
    }
}
