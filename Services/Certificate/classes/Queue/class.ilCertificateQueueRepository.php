<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateQueueRepository
{
    private ilDBInterface $database;
    private ilLogger $logger;

    public function __construct(ilDBInterface $database, ilLogger $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function addToQueue(ilCertificateQueueEntry $certificateQueueEntry) : void
    {
        $this->logger->info('START - Add new entry to certificate cron job queue');

        $id = $this->database->nextId('il_cert_cron_queue');

        $row = [
            'id' => ['integer', $id],
            'obj_id' => ['integer', $certificateQueueEntry->getObjId()],
            'usr_id' => ['integer', $certificateQueueEntry->getUserId()],
            'adapter_class' => ['text', $certificateQueueEntry->getAdapterClass()],
            'state' => ['text', $certificateQueueEntry->getState()],
            'started_timestamp' => ['integer', $certificateQueueEntry->getStartedTimestamp()],
            'template_id' => ['integer', $certificateQueueEntry->getTemplateId()],
        ];

        $this->logger->debug(sprintf(
            'Save queue entry with following values: %s',
            json_encode($row, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        ));
        $this->logger->info('END - Added entry to queue');

        $this->database->insert('il_cert_cron_queue', $row);
    }

    public function removeFromQueue(int $id) : void
    {
        $this->logger->info(sprintf('START - Remove entry(id: "%s") from queue', $id));

        $sql = 'DELETE FROM il_cert_cron_queue WHERE id = ' . $this->database->quote($id, 'integer');

        $this->database->manipulate($sql);

        $this->logger->info(sprintf('END - Entry(id: "%s") deleted from queue', $id));
    }

    /**
     * @return ilCertificateQueueEntry[]
     */
    public function getAllEntriesFromQueue() : array
    {
        $this->logger->info('START - Fetch all entries from queue');

        $sql = 'SELECT * FROM il_cert_cron_queue';
        $query = $this->database->query($sql);

        $result = [];
        while ($row = $this->database->fetchAssoc($query)) {
            $this->logger->debug(sprintf(
                'Queue entry found: "%s"',
                json_encode($row, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
            ));
            
            $result[] = new ilCertificateQueueEntry(
                (int) $row['obj_id'],
                (int) $row['usr_id'],
                $row['adapter_class'],
                $row['state'],
                (int) $row['template_id'],
                (int) $row['started_timestamp'],
                (int) $row['id']
            );
        }

        $this->logger->info(sprintf('END - All queue entries fetched(Total: "%s")', count($result)));

        return $result;
    }

    public function removeFromQueueByUserId(int $user_id) : void
    {
        $this->logger->info(sprintf('START - Remove entries for user(user_id: "%s") from queue', $user_id));

        $sql = 'DELETE FROM il_cert_cron_queue WHERE usr_id = ' . $this->database->quote($user_id, 'integer');

        $this->database->manipulate($sql);

        $this->logger->info(sprintf('END - Entries for user(user_id: "%s") deleted from queue', $user_id));
    }
}
