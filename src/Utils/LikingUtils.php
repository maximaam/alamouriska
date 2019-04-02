<?php

namespace App\Utils;

use App\Entity\Liking;
/**
 * Class Liking
 * @package App\Utils
 */
class LikingUtils
{
    /**
     * @param array $likings
     * @return array
     */
    public static function getLikingsUsersIds(array $likings)
    {
        $data = [];

        /** @var Liking $liking */
        foreach ($likings as $liking) {
            $ownerId = $liking->getOwnerId();

            if (!isset($data[$ownerId])) {
                $data[$ownerId] = [];
            }

            \array_push($data[$ownerId], $liking->getUser()->getId());
        }

        return $data;

    }

}