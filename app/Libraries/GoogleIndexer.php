<?php

namespace App\Libraries;

require_once APPPATH . "ThirdParty/google-apiclient/vendor/autoload.php";

use Google\Client;
use Google\Service\Indexing;

class GoogleIndexer
{
    private ?Client $client = null;
    private ?Indexing $service = null;
    private bool $isEnabled = true;
    private string $jsonKeyPath;

    public function __construct()
    {
        $this->jsonKeyPath = WRITEPATH . 'keys/google-service-account.json';

        if (!file_exists($this->jsonKeyPath)) {
            $this->isEnabled = false;
            log_message('error', 'Google Indexing API disabled: JSON key file not found.');
            return;
        }

        $this->initializeClient();
    }

    /**
     * Initializes the Google API client.
     */
    private function initializeClient(): void
    {
        try {
            $this->client = new Client();
            $this->client->setAuthConfig($this->jsonKeyPath);
            $this->client->addScope(Indexing::INDEXING);

            $this->service = new Indexing($this->client);
        } catch (\Exception $e) {
            $this->isEnabled = false;
            log_message('error', 'Google Indexing API Client Initialization Failed: ' . $e->getMessage());
        }
    }

    /**
     * Notifies Google that a new or updated URL should be indexed.
     *
     * @param string $url The URL to be indexed.
     * @return array API response.
     */
    public function indexUrl(string $url): array
    {
        return $this->sendRequest($url, "URL_UPDATED");
    }

    /**
     * Notifies Google that a URL should be removed from the index.
     *
     * @param string $url The URL to be removed.
     * @return array API response.
     */
    public function removeUrl(string $url): array
    {
        return $this->sendRequest($url, "URL_DELETED");
    }

    /**
     * Sends a request to the Google Indexing API.
     *
     * @param string $url The URL to be processed.
     * @param string $type Request type: "URL_UPDATED" or "URL_DELETED".
     * @return array API response.
     */
    private function sendRequest(string $url, string $type): array
    {
        if (!$this->isEnabled || !$this->service) {
            return $this->errorResponse('Google Indexing API is disabled or not properly initialized.');
        }

        try {
            $postBody = new Indexing\UrlNotification();
            $postBody->setUrl($url);
            $postBody->setType($type);

            $response = $this->service->urlNotifications->publish($postBody);

            return [
                'success' => true,
                'message' => 'Request successful',
                'response' => $response
            ];
        } catch (\Google\Service\Exception $e) {
            return $this->errorResponse('Google API Error: ' . $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->errorResponse('General Error: ' . $e->getMessage());
        }
    }

    /**
     * Returns a standardized error response.
     *
     * @param string $message Error message.
     * @param int|null $code Optional error code.
     * @return array
     */
    private function errorResponse(string $message, ?int $code = null): array
    {
        log_message('error', $message);
        return [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
    }

    /**
     * Checks if the Google Indexing API is enabled.
     *
     * @return bool
     */
    public function isApiEnabled(): bool
    {
        return $this->isEnabled;
    }
}