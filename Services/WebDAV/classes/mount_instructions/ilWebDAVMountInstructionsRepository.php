<?php declare(strict_types = 1);

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
interface ilWebDAVMountInstructionsRepository
{
    public function createMountInstructionsDocumentEntry(ilWebDAVMountInstructionsDocument $document) : void;
    
    public function getNextMountInstructionsDocumentId() : int;
    
    public function getHighestSortingNumber() : int;
    
    public function getMountInstructionsDocumentById(int $id) : ilWebDAVMountInstructionsDocument;
    
    public function getMountInstructionsByLanguage(string $language) : ilWebDAVMountInstructionsDocument;
    
    public function getAllMountInstructions() : array;
    
    public function doMountInstructionsExistByLanguage(string $language) : int;
    
    public function updateMountInstructions(ilWebDAVMountInstructionsDocument $document);
    
    public function updateSortingValueById(int $id, int $a_new_sorting_value);
    
    public function deleteMountInstructionsById(int $id);
}
