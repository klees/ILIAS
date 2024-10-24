<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Michael Jansen <mjansen@databay.de>
 * @ingroup ServicesMail
 */
class ilMailLuceneSearcher
{
    protected ilLuceneQueryParser $query_parser;
    protected ilMailSearchResult $result;
    protected ilSetting $settings;

    public function __construct(ilLuceneQueryParser $query_parser, ilMailSearchResult $result)
    {
        global $DIC;
        $this->settings = $DIC->settings();
        $this->query_parser = $query_parser;
        $this->result = $result;
    }

    public function search(int $user_id, int $mail_folder_id) : void
    {
        if (!$this->query_parser->getQuery()) {
            throw new ilException('mail_search_query_missing');
        }

        try {
            $xml = ilRpcClientFactory::factory('RPCSearchHandler')->searchMail(
                CLIENT_ID . '_' . $this->settings->get('inst_id', '0'),
                $user_id,
                $this->query_parser->getQuery(),
                $mail_folder_id
            );
        } catch (Exception $e) {
            ilLoggerFactory::getLogger('mail')->critical($e->getMessage());
            throw $e;
        }

        $parser = new ilMailSearchLuceneResultParser($this->result, $xml);
        $parser->parse();
    }
}
