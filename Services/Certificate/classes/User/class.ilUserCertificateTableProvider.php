<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilUserCertificateTableProvider
{
    private ilDBInterface $database;
    private ilLogger $logger;
    private ilCtrlInterface $ctrl;
    private ilCertificateObjectHelper $objectHelper;
    private string $defaultTitle;

    public function __construct(
        ilDBInterface $database,
        ilLogger $logger,
        ilCtrlInterface $ctrl,
        string $defaultTitle,
        ilCertificateObjectHelper $objectHelper = null
    ) {
        $this->database = $database;
        $this->logger = $logger;
        $this->ctrl = $ctrl;
        $this->defaultTitle = $defaultTitle;

        if (null === $objectHelper) {
            $objectHelper = new ilCertificateObjectHelper();
        }
        $this->objectHelper = $objectHelper;
    }

    /**
     * @param int                  $userId
     * @param array<string, mixed> $params
     * @param array<string, mixed> $filter
     * @return array{cnt: int, items: array<int, array>}
     * @throws JsonException
     */
    public function fetchDataSet(int $userId, array $params, array $filter) : array
    {
        $this->logger->debug(sprintf('START - Fetching all active certificates for user: "%s"', $userId));

        $sql = 'SELECT 
  il_cert_user_cert.id,
  il_cert_user_cert.obj_type,
  il_cert_user_cert.thumbnail_image_path,
  il_cert_user_cert.acquired_timestamp,
  usr_data.firstname,
  usr_data.lastname,
  il_cert_user_cert.obj_id,
  (CASE
    WHEN (trans.title IS NOT NULL AND LENGTH(trans.title) > 0) THEN trans.title
    WHEN (object_data.title IS NOT NULL AND LENGTH(object_data.title) > 0) THEN object_data.title 
    WHEN (object_data_del.title IS NOT NULL AND LENGTH(object_data_del.title) > 0) THEN object_data_del.title 
    ELSE ' . $this->database->quote($this->defaultTitle, 'text') . '
    END
  ) as title,
  (CASE
    WHEN (trans.description IS NOT NULL AND LENGTH(trans.description) > 0) THEN trans.description
    WHEN (object_data.description IS NOT NULL AND LENGTH(object_data.description) > 0) THEN object_data.description 
    WHEN (object_data_del.description IS NOT NULL AND LENGTH(object_data_del.description) > 0) THEN object_data_del.description 
    ELSE ""
    END
  ) as description
FROM il_cert_user_cert
LEFT JOIN object_data ON object_data.obj_id = il_cert_user_cert.obj_id
LEFT JOIN object_translation trans ON trans.obj_id = object_data.obj_id
AND trans.lang_code = ' . $this->database->quote($params['language'], 'text') . '
LEFT JOIN object_data_del ON object_data_del.obj_id = il_cert_user_cert.obj_id
LEFT JOIN usr_data ON usr_data.usr_id = il_cert_user_cert.user_id
WHERE user_id = ' . $this->database->quote($userId, 'integer') . ' AND currently_active = 1';

        if ([] !== $params) {
            $sql .= $this->getOrderByPart($params, $filter);
        }

        if (isset($params['limit'])) {
            if (!is_numeric($params['limit'])) {
                throw new InvalidArgumentException('Please provide a valid numerical limit.');
            }

            if (!isset($params['offset'])) {
                $params['offset'] = 0;
            } elseif (!is_numeric($params['offset'])) {
                throw new InvalidArgumentException('Please provide a valid numerical offset.');
            }

            $this->database->setLimit($params['limit'], $params['offset']);
        }

        $query = $this->database->query($sql);

        $data = [
            'items' => [],
            'cnt' => 0,
        ];

        while ($row = $this->database->fetchAssoc($query)) {
            $title = $row['title'];

            $data['items'][] = [
                'id' => $row['id'],
                'title' => $title,
                'obj_id' => $row['obj_id'],
                'obj_type' => $row['obj_type'],
                'date' => $row['acquired_timestamp'],
                'thumbnail_image_path' => $row['thumbnail_image_path'],
                'description' => $row['description'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
            ];
        }

        if (isset($params['limit'])) {
            $cnt_sql = '
				SELECT COUNT(*) cnt
				FROM il_cert_user_cert
				WHERE user_id = ' . $this->database->quote($userId, 'integer') . ' AND currently_active = 1';

            $row_cnt = $this->database->fetchAssoc($this->database->query($cnt_sql));

            $data['cnt'] = $row_cnt['cnt'];

            $this->logger->debug(sprintf(
                'All active certificates for user: "%s" total: "%s"',
                $userId,
                $data['cnt']
            ));
        } else {
            $data['cnt'] = count($data['items']);
        }

        $this->logger->debug(sprintf('END - Actual results: "%s"', json_encode($data, JSON_THROW_ON_ERROR)));

        return $data;
    }

    protected function getOrderByPart(array $params, array $filter) : string
    {
        if (isset($params['order_field'])) {
            if (!is_string($params['order_field'])) {
                throw new InvalidArgumentException('Please provide a valid order field.');
            }

            if (!in_array($params['order_field'], ['date', 'id', 'title'])) {
                throw new InvalidArgumentException('Please provide a valid order field.');
            }

            if ($params['order_field'] === 'date') {
                $params['order_field'] = 'acquired_timestamp';
            }

            if (!isset($params['order_direction'])) {
                $params['order_direction'] = 'ASC';
            } elseif (!in_array(strtolower($params['order_direction']), ['asc', 'desc'])) {
                throw new InvalidArgumentException('Please provide a valid order direction.');
            }

            return ' ORDER BY ' . $params['order_field'] . ' ' . $params['order_direction'];
        }

        return '';
    }
}
