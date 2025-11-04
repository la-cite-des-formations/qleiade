<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;

// Les imports Google (corrects)
use Google\Client;
use Google\Service\Drive;

// LE VRAI NAMESPACE (sans le sous-dossier en trop)
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Storage::extend('google', function ($app, $config) {
                $credentials_file = base_path($config['credentialsFile']);

                $client = new Client();
                $client->setScopes(Drive::DRIVE);
                $client->setAuthConfig($credentials_file);
                $client->useApplicationDefaultCredentials();
                $client->setAuthConfig($credentials_file);
                $client->setAccessType('offline');

                $service = new Drive($client);

                $options = [];
                $options['defaultParams'] = [
                    'files.list' =>
                    [
                        'driveId' => $config['folderId'],
                        'includeItemsFromAllDrives' => true,
                        'corpora' => 'drive',
                        'supportsAllDrives' => true
                    ]
                ];
                if (isset($config['teamDriveId'])) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                // On utilise la classe "Masbug\Flysystem\GoogleDriveAdapter"
                $adapter = new GoogleDriveAdapter($service, $config['folderId'], $options);

                // CORRECTION FLYSYSTEM V3
                return new Filesystem($adapter, $config);
            });
        } catch (\Exception $e) {
            // Gérer l'erreur si besoin
            Log::error('Erreur lors du chargement du Google Drive Service Provider: ' . $e->getMessage());
        }
    }
}
