<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:27 PM
 */
if(@=InvoiceLoaded == false)
{
    $masterInpDocId = "9874698365ba4c7cebf10b3073588512"; //set to the UID of the master process' Input Document
    $subInpDocId    = "7185418275bb25726d05f22033581485"; //set to the UID of the subprocess' Input Document
    require_once 'classes/model/AppDocument.php';
    $oDoc = new AppDocument();

//Find the master process case UID and get the files from its Input Document:
    $subcaseId = @@APPLICATION;
    $sql = "SELECT SUB.APP_PARENT, AD.APP_DOC_UID, AD.DOC_VERSION
   FROM APP_DOCUMENT AS AD, SUB_APPLICATION AS SUB
   WHERE SUB.APP_UID='$subcaseId' AND SUB.APP_PARENT=AD.APP_UID AND
   AD.DOC_UID='$masterInpDocId' AND AD.APP_DOC_STATUS='ACTIVE'
   ORDER BY AD.APP_DOC_INDEX";

    $aFiles = executeQuery($sql);
    if (is_array($aFiles) and count($aFiles) > 0) {
        $masterCaseId = $aFiles[1]['APP_PARENT'];
        @=myMultipleFile = array();
        foreach ($aFiles as $aFile) {
            $aFileInfo = $oDoc->Load($aFile['APP_DOC_UID'], $aFile['DOC_VERSION']);
            $extension = pathinfo($aFileInfo['APP_DOC_FILENAME'], PATHINFO_EXTENSION);
            @@extension = $extension;
            $path = PATH_DOCUMENT . G::getPathFromUID($masterCaseId) .PATH_SEP.
                $aFile['APP_DOC_UID'] .'_'. $aFile['DOC_VERSION'] .'.'. $extension;
            //works in version 3.0.1.18 and later:
            @@fileId = PMFAddInputDocument($subInpDocId, '', 1, 'INPUT', $aFileInfo['APP_DOC_COMMENT'], '',
                @@APPLICATION, @%INDEX, @@TASK, $aFileInfo['USR_UID'], 'file', $path);
            //rename the filename of the copied file from its ID to the original filename:
            if (!empty(@@fileId)) {
                $aProps = array(
                    'APP_DOC_UID'  => @@fileId,
                    'DOC_VERSION' => 1,
                    'APP_DOC_FILENAME' => $aFileInfo['APP_DOC_FILENAME']
                );
                $oDoc->update($aProps);
                @=Invoice[] = array(
                    'appDocUid' => @@fileId,
                    'version' => 1,
                    'name' => $aFileInfo['APP_DOC_FILENAME']
                );
            }
        }
    }
    @=InvoiceLoaded = true;
}
