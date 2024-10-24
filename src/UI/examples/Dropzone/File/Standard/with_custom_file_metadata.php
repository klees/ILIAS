<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Dropzone\File\Standard;

function with_custom_file_metadata()
{
    global $DIC;
    $factory = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();
    $refinery = $DIC->refinery();
    $request_wrapper = $DIC->http()->wrapper()->query();

    // Handle a file upload ajax request
    if ($request_wrapper->has('example') && $request_wrapper->retrieve('example', $refinery->kindlyTo()->int()) == 2) {
        $upload = $DIC->upload();
        try {
            $upload->process();
            // $upload->moveFilesTo('/myPath/'); // Since we are in an example here, we do not move the files. But this would be the way wou move files using the FileUpload-Service

            // Access the custom file name and description via $_POST parameters:
            // $_POST['customFileName'] and $_POST['fileDescription']

            // The File-Dropzones will expect a valid json-Status (success true or false).
            echo json_encode(['success' => true, 'message' => 'Successfully uploaded file']);
        } catch (\Exception $e) {
            // See above
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    $uploadUrl = $_SERVER['REQUEST_URI'] . '&example=2';
    $dropzone = $factory->dropzone()->file()->standard($uploadUrl)
        ->withUserDefinedFileNamesEnabled(true)
        ->withUserDefinedDescriptionEnabled(true)
        ->withUploadButton($factory->button()->standard('Upload', ''));

    return $renderer->render($dropzone);
}
