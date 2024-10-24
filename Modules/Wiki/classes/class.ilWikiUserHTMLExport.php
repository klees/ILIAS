<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

use ILIAS\Wiki\Export\WikiHtmlExport;

/**
 *  Class manages user html export
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ilWikiUserHTMLExport
{
    public const PROCESS_OTHER_USER = 0;	// another user has started a running export
    public const PROCESS_STARTED = 1;		// export has been started by current user
    public const PROCESS_UPTODATE = 2;		// no export necessary, current export is up-to-date

    public const NOT_RUNNING = 0;
    public const RUNNING = 1;

    protected ?array $data = null;
    protected ilDBInterface $db;
    protected \ilObjWiki $wiki;
    protected ilObjUser $user;
    protected ilLogger$log;
    protected bool $with_comments = false;

    public function __construct(
        ilObjWiki $a_wiki,
        ilDBInterface $a_db,
        ilObjUser $a_user,
        bool $with_comments = false
    ) {
        $this->db = $a_db;
        $this->wiki = $a_wiki;
        $this->user = $a_user;
        $this->read();
        $this->log = ilLoggerFactory::getLogger('wiki');
        $this->with_comments = $with_comments;
        $this->log->debug("comments: " . $this->with_comments);
    }

    protected function read() : void
    {
        $set = $this->db->query(
            "SELECT * FROM wiki_user_html_export " .
            " WHERE wiki_id  = " . $this->db->quote($this->wiki->getId(), "integer") .
            " AND with_comments = " . $this->db->quote($this->with_comments, "integer")
        );
        if (!$this->data = $this->db->fetchAssoc($set)) {
            $this->data = array();
        }
    }

    protected function getProcess() : int
    {
        $this->log->debug("getProcess");
        $last_change = ilPageObject::getLastChangeByParent("wpg", $this->wiki->getId());
        $file_exists = $this->doesFileExist();

        $ilAtomQuery = $this->db->buildAtomQuery();
        $ilAtomQuery->addTableLock('wiki_user_html_export');

        $ilAtomQuery->addQueryCallable(function (ilDBInterface $ilDB) use ($last_change, &$ret, $file_exists) {
            $this->log->debug("atom query start");
            
            $this->read();
            $ts = ilUtil::now();

            if ($this->data["start_ts"] != "" &&
                $this->data["start_ts"] > $last_change) {
                if ($file_exists) {
                    $ret = self::PROCESS_UPTODATE;
                    $this->log->debug("return: " . self::PROCESS_UPTODATE);
                    return;
                }
            }

            if (!isset($this->data["wiki_id"])) {
                $this->log->debug("insert, wiki id: " . $this->wiki->getId() . ", user id: " . $this->user->getId() .
                    ", ts: " . $ts . ", with_comments: " . $this->with_comments);
                $ilDB->manipulate("INSERT INTO wiki_user_html_export  " .
                    "(wiki_id, usr_id, progress, start_ts, status, with_comments) VALUES (" .
                    $ilDB->quote($this->wiki->getId(), "integer") . "," .
                    $ilDB->quote($this->user->getId(), "integer") . "," .
                    $ilDB->quote(0, "integer") . "," .
                    $ilDB->quote($ts, "timestamp") . "," .
                    $ilDB->quote(self::RUNNING, "integer") . "," .
                    $ilDB->quote($this->with_comments, "integer") .
                    ")");
            } else {
                $this->log->debug("update, wiki id: " . $this->wiki->getId() . ", user id: " . $this->user->getId() .
                    ", ts: " . $ts . ", with_comments: " . $this->with_comments);
                $ilDB->manipulate(
                    "UPDATE wiki_user_html_export SET " .
                    " start_ts = " . $ilDB->quote($ts, "timestamp") . "," .
                    " usr_id = " . $ilDB->quote($this->user->getId(), "integer") . "," .
                    " progress = " . $ilDB->quote(0, "integer") . "," .
                    " status = " . $ilDB->quote(self::RUNNING, "integer") .
                    " WHERE status = " . $ilDB->quote(self::NOT_RUNNING, "integer") .
                    " AND wiki_id = " . $ilDB->quote($this->wiki->getId(), "integer") .
                    " AND with_comments = " . $ilDB->quote($this->with_comments, "integer")
                );
                $this->read();
            }

            if ($this->data["start_ts"] == $ts && $this->data["usr_id"] == $this->user->getId()) {
                //  we started the process
                $ret = self::PROCESS_STARTED;
                $this->log->debug("return: " . self::PROCESS_STARTED);
                return;
            }

            // process was already running
            $ret = self::PROCESS_OTHER_USER;
            $this->log->debug("return: " . self::PROCESS_OTHER_USER);
        });

        $ilAtomQuery->run();

        $this->log->debug("outer return: " . $ret);

        return $ret;
    }

    public function updateStatus(
        int $a_progress,
        int $a_status
    ) : void {
        $this->db->manipulate(
            "UPDATE wiki_user_html_export SET " .
            " progress = " . $this->db->quote($a_progress, "integer") . "," .
            " status = " . $this->db->quote($a_status, "integer") .
            " WHERE wiki_id = " . $this->db->quote($this->wiki->getId(), "integer") .
            " AND usr_id = " . $this->db->quote($this->user->getId(), "integer") .
            " AND with_comments = " . $this->db->quote($this->with_comments, "integer")
        );

        $this->read();
    }

    public function getProgress() : array
    {
        $set = $this->db->query(
            "SELECT progress, status FROM wiki_user_html_export " .
            " WHERE wiki_id = " . $this->db->quote($this->wiki->getId(), "integer") .
            " AND with_comments = " . $this->db->quote($this->with_comments, "integer")
        );
        $rec = $this->db->fetchAssoc($set);

        return array("progress" => (int) $rec["progress"], "status" => (int) $rec["status"]);
    }

    public function initUserHTMLExport() : void
    {
        // get process, if not already running or export is up-to-date, return corresponding status
        echo $this->getProcess();
        exit;
    }

    public function startUserHTMLExport() : void
    {
        ignore_user_abort(true);
        // do the export
        $exp = new WikiHtmlExport($this->wiki);
        if (!$this->with_comments) {
            $exp->setMode(WikiHtmlExport::MODE_USER);
        } else {
            $exp->setMode(WikiHtmlExport::MODE_USER_COMMENTS);
        }
        $exp->buildExportFile();
        // reset user export status
        $this->updateStatus(100, self::NOT_RUNNING);
        exit;
    }

    protected function doesFileExist() : bool
    {
        $exp = new WikiHtmlExport($this->wiki);
        if ($this->with_comments) {
            $exp->setMode(WikiHtmlExport::MODE_USER_COMMENTS);
        } else {
            $exp->setMode(WikiHtmlExport::MODE_USER);
        }
        $file = $exp->getUserExportFile();
        return is_file($file);
    }

    public function deliverFile() : void
    {
        $this->log->debug("deliver");

        $exp = new WikiHtmlExport($this->wiki);
        if ($this->with_comments) {
            $exp->setMode(WikiHtmlExport::MODE_USER_COMMENTS);
        } else {
            $exp->setMode(WikiHtmlExport::MODE_USER);
        }
        $file = $exp->getUserExportFile();
        $this->log->debug("file: " . $file);
        ilFileDelivery::deliverFileLegacy($file, pathinfo($file, PATHINFO_BASENAME));
    }
}
