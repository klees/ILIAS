<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateObjectsForUserPreloader
{
    /** @var array<int, int[]> */
    private static array $certificates = [];
    private ilUserCertificateRepository $userCertificateRepository;

    public function __construct(ilUserCertificateRepository $userCertificateRepository)
    {
        $this->userCertificateRepository = $userCertificateRepository;
    }

    /**
     * @param int   $userId
     * @param int[] $objIds
     * @throws JsonException
     */
    public function preLoad(int $userId, array $objIds) : void
    {
        if (!array_key_exists($userId, self::$certificates)) {
            self::$certificates[$userId] = [];
        }

        $objectIdsWithUserCertificate = $this->userCertificateRepository->fetchObjectIdsWithCertificateForUser(
            $userId,
            array_diff($objIds, self::$certificates[$userId])
        );

        self::$certificates[$userId] = array_unique(array_merge(
            $objectIdsWithUserCertificate,
            self::$certificates[$userId]
        ));
    }

    public function isPreloaded(int $userId, int $objId) : bool
    {
        if (false === array_key_exists($userId, self::$certificates)) {
            return false;
        }

        if (true === in_array($objId, self::$certificates[$userId], true)) {
            return true;
        }

        return false;
    }
}
