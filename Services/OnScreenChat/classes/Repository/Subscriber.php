<?php declare(strict_types=1);
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\OnScreenChat\Repository;

use ilDBInterface;
use ilObjUser;
use ilUserUtil;
use ilWACException;
use ilWACSignedPath;

/**
 * Class Subscriber
 * @package ILIAS\OnScreenChat\Repository
 */
class Subscriber
{
    protected ilDBInterface $db;
    protected ilObjUser $user;

    public function __construct(ilDBInterface $db, ilObjUser $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    /**
     * @return int[]
     */
    public function getInitialUserProfileData() : array
    {
        $conversationIds = [];

        $res = $this->db->queryF(
            'SELECT DISTINCT(conversation_id) FROM osc_activity WHERE user_id = %s',
            ['integer'],
            [$this->user->getId()]
        );
        while ($row = $this->db->fetchAssoc($res)) {
            $conversationIds[$row['conversation_id']] = $row['conversation_id'];
        }

        $res = $this->db->queryF(
            'SELECT DISTINCT(conversation_id) FROM osc_messages WHERE user_id = %s',
            ['integer'],
            [$this->user->getId()]
        );
        while ($row = $this->db->fetchAssoc($res)) {
            $conversationIds[$row['conversation_id']] = $row['conversation_id'];
        }

        if (0 === count($conversationIds)) {
            return [];
        }

        $usrIds = [];

        $in = $this->db->in('id', $conversationIds, false, 'text');
        $res = $this->db->query('SELECT DISTINCT(participants) FROM osc_conversation WHERE ' . $in);
        while ($row = $this->db->fetchAssoc($res)) {
            $participants = json_decode($row['participants'], true, 512, JSON_THROW_ON_ERROR);

            if (is_array($participants)) {
                $usrIds = array_unique(array_merge($usrIds, array_filter(array_map(static function ($user) : int {
                    if (is_array($user) && isset($user['id'])) {
                        return (int) $user['id'];
                    }

                    return 0;
                }, $participants))));
            }
        }

        return $this->getDataByUserIds($usrIds);
    }

    /**
     * @param int[] $usrIds
     * @return array<int, array{public_name: string, profile_image: string}>
     */
    public function getDataByUserIds(array $usrIds) : array
    {
        $usrIds = array_filter(array_map('intval', array_map('trim', $usrIds)));

        $oldWacTokenValue = ilWACSignedPath::getTokenMaxLifetimeInSeconds();
        ilWACSignedPath::setTokenMaxLifetimeInSeconds(60);

        $publicData = ilUserUtil::getNamePresentation($usrIds, true, false, '', false, true, false, true);

        $data = [];

        foreach ($usrIds as $usr_id) {
            $publicImage = $publicData[$usr_id]['img'] ?? '';
            $publicName = '';

            if (isset($publicData[$usr_id])) {
                $login = '';
                if (isset($publicData[$usr_id]['login'])) {
                    $publicName = $login = $publicData[$usr_id]['login'];
                }

                if (isset($publicData[$usr_id]['public_profile']) && $publicData[$usr_id]['public_profile']) {
                    $publicName = implode(' ', array_filter(array_map('trim', [
                        (string) ($publicData[$usr_id]['firstname'] ?? ''),
                        (string) ($publicData[$usr_id]['lastname'] ?? ''),
                    ])));

                    if ($publicName === '') {
                        $publicName = $login;
                    }
                }
            }

            $data[$usr_id] = [
                'public_name' => $publicName,
                'profile_image' => $publicImage
            ];
        }

        ilWACSignedPath::setTokenMaxLifetimeInSeconds($oldWacTokenValue);

        return $data;
    }
}
