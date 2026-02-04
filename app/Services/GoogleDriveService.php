<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Koperasi JR Backup');
        $this->client->setScopes(Drive::DRIVE);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setRedirectUri(route('backup.callback'));

        // FIX: Disable SSL verify for local development (cURL error 60)
        $httpClient = new \GuzzleHttp\Client(['verify' => false]);
        $this->client->setHttpClient($httpClient);

        // Load Token if exists
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // Refresh Token if expired
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                // Save updated token
                file_put_contents($tokenPath, json_encode($newAccessToken));
            }
        }

        // Initialize Drive Service only if authenticated
        if ($this->client->getAccessToken()) {
            $this->service = new Drive($this->client);
        }
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);

        // Check for errors
        if (array_key_exists('error', $accessToken)) {
            throw new \Exception(join(', ', $accessToken));
        }

        // Save Token
        $tokenPath = storage_path('app/google/token.json');
        file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));

        return true;
    }

    public function isConnected()
    {
        return $this->client->getAccessToken() && !$this->client->isAccessTokenExpired();
    }

    /**
     * Create folder if not exists, else return its ID
     */
    public function createFolder($folderName, $parentId = null)
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and trashed=false";
        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        }

        $files = $this->service->files->listFiles([
            'q' => $query,
            'spaces' => 'drive',
        ]);

        if (count($files->getFiles()) > 0) {
            return $files->getFiles()[0]->getId();
        }

        $fileMetadata = new Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => $parentId ? [$parentId] : [],
        ]);

        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'id'
        ]);

        return $folder->id;
    }

    /**
     * Upload file to specific folder
     */
    public function uploadFile($filePath, $fileName, $folderId)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName,
            'parents' => [$folderId]
        ]);

        $content = file_get_contents($filePath);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($filePath),
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink'
        ]);

        return $file;
    }
}
