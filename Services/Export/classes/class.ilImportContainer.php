<?php declare(strict_types=1);

/* Copyright (c) 1998-2021 ILIAS open source, GPLv3, see LICENSE */

/**
 * Import class
 * @author Stefan Meyer <meyer@leifos.com>
 */
class ilImportContainer extends ilImport
{
    public function __construct(int $a_target_id)
    {
        parent::__construct($a_target_id);
    }

    protected function doImportObject(
        string $dir,
        string $a_type,
        string $a_component = "",
        string $a_tmpdir = ""
    ) : array {
        $manifest_file = $dir . "/manifest.xml";
        if (!file_exists($manifest_file)) {
            return [];
        }
        $parser = new ilManifestParser($manifest_file);

        // Handling single containers without subitems
        // @todo: check if this is required
        // all container have container export sets
        $all_importers = array();
        if (!$parser->getExportSets()) {
            $this->createDummy($a_type);
            $import_info = parent::doImportObject($dir, $a_type);
            $all_importers = array_merge($all_importers, $import_info['importers']);
            return $import_info;
        }

        // Handling containers with subitems
        $first = true;
        $ret = [];
        foreach ($parser->getExportSets() as $set) {
            $import_info = parent::doImportObject($dir . DIRECTORY_SEPARATOR . $set['path'], $set['type']);
            $all_importers = array_merge($all_importers, $import_info['importers']);
            if ($first) {
                $ret = $import_info;
                $first = false;
            }
        }
        // after container import is finished, call all importers to perform a final processing
        foreach ($all_importers as $importer) {
            $importer->afterContainerImportProcessing($this->getMapping());
        }
        return $ret;
    }

    protected function createDummy(string $a_type) : ilObject
    {
        $class_name = "ilObj" . $this->objDefinition->getClassName($a_type);

        $new = new $class_name();
        $new->setTitle('Import');
        $new->create(true);
        $new->createReference();
        $new->putInTree($this->getMapping()->getTargetId());
        $new->setPermissions($this->getMapping()->getTargetId());

        $this->getMapping()->addMapping('Services/Container', 'objs', '0', (string) $new->getId());
        $this->getMapping()->addMapping('Services/Container', 'refs', '0', (string) $new->getRefId());

        return $new;
    }
}
