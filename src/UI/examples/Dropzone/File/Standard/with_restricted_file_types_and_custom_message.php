<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Dropzone\File\Standard;

use Exception;

function with_restricted_file_types_and_custom_message()
{
    global $DIC;
    $uiFactory = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();
    $refinery = $DIC->refinery();
    $request_wrapper = $DIC->http()->wrapper()->query();

    if ($request_wrapper->has('example') && $request_wrapper->retrieve('example', $refinery->kindlyTo()->int()) == 3) {
        $upload = $DIC->upload();
        try {
            $upload->process();
            // $upload->moveFilesTo('/myPath/'); // Since we are in an example here, we do not move the files. But this would be the way wou move files using the FileUpload-Service

            // The File-Dropzones will expect a valid json-Status (success true or false).
            echo json_encode(['success' => true, 'message' => 'Successfully uploaded file']);
        } catch (Exception $e) {
            // See above
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    $uploadUrl = $_SERVER['REQUEST_URI'] . '&example=3';
    $dropzone = $uiFactory->dropzone()->file()->standard($uploadUrl)
        ->withMessage("Drag and drop some PDF files over here...")
        ->withAllowedFileTypes(['pdf'])
        ->withUploadButton($uiFactory->button()->standard('Upload', ''));

    return $renderer->render($dropzone);
}
