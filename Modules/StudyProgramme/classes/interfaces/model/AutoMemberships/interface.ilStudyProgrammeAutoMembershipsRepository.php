<?php declare(strict_types=1);

/**
 * Persistence of "monitored" sources for automatic membership
 */
interface ilStudyProgrammeAutoMembershipsRepository
{
    /**
     * Read auto-membership sources of programme.
     *
     * @return ilStudyProgrammeAutoMembershipSource[]
     */
    public function getFor(int $prg_obj_id) : array;

    /**
     * Build an auto-membership source.
     */
    public function create(
        int $prg_obj_id,
        string $source_type,
        int $source_id,
        bool $enabled,
        int $last_edited_usr_id = null,
        DateTimeImmutable $last_edited = null
    ) : ilStudyProgrammeAutoMembershipSource;

    /**
     * Update an auto-membership source.
     */
    public function update(ilStudyProgrammeAutoMembershipSource $ams) : void;

    /**
     * Delete a single source-setting.
     */
    public function delete(int $prg_obj_id, string $source_type, int $source_id) : void;

    /**
     * Delete all auto-membership sources of a programme.
     */
    public function deleteFor(int $prg_obj_id) : void;

    /**
     * Get all programmes' obj_ids monitoring the given source.
     *
     * @return int[]
     */
    public static function getProgrammesFor(string $source_type, int $source_id) : array;
}
