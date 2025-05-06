<?php

namespace Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\DriveManagement;
use Illuminate\Http\Request;

class FileNotificationsController extends Controller
{
    use DriveManagement;
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function collect(Request $request)
    {
        // Récupérer les données envoyées par Google Drive API
        $json = $request->getContent();
        $data = json_decode($json, true);

        // Vérifier si les données contiennent des modifications
        if (isset($data['changeType']) && $data['changeType'] === 'file') {
            // Récupérer l'ID du fichier modifié
            $fileId = $data['fileId'];

            // Récupérer les informations du fichier
            $fileMetadata = $this->getMetaData($fileId);
            // $fileMetadata = getFileMetadata($fileId);

            // Écrire les informations du fichier dans un fichier texte
            $text = "Nom : " . $fileMetadata['name'] . "\n";
            $text .= "ID : " . $fileMetadata['id'] . "\n";
            $text .= "Taille : " . $fileMetadata['size'] . " octets\n";
            $text .= "Date de modification : " . $fileMetadata['modifiedTime'] . "\n";

            $pathToFile = storage_path('app/public/file-info.txt');
            $file = fopen($pathToFile, 'a');
            fwrite($file, $text);
            fclose($file);
        }

        return response()->json(['success' => true]);
    }
    public function test(Request $request)
    {
        $this->getActivities();
    }
}
