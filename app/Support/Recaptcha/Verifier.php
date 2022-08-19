<?php

namespace App\Support\Recaptcha;

use GuzzleHttp\Client;

class Verifier
{
    private $secret;
    private $siteKey;
    private Client $http;

    /**
     * The cached verified responses.
     *
     * @var array
     */
    protected $verifiedResponses = [];

    public function __construct($secret, $siteKey, $options = [])
    {
        $this->secret = $secret;
        $this->siteKey = $siteKey;
        $this->http = new Client($options);
    }

    public function verify($response, $clientIp): bool
    {
        if (empty($response)) {
            return false;
        }

        // Return true if response already verified before.
        if (in_array($response, $this->verifiedResponses)) {
            return true;
        }

        $verifyResponse = $this->sendRequestVerify([
            'secret' => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp,
        ]);
        if (isset($verifyResponse['success']) && $verifyResponse['success'] === true) {
            // A response can only be verified once from Google, so we need to
            // cache it to make it work in case we want to verify it multiple times.
            $this->verifiedResponses[] = $response;

            return true;
        }

        return false;
    }

    /**
     * Send verify request.
     *
     * @param  array  $query
     * @return array
     */
    protected function sendRequestVerify(array $query = [])
    {
        $response = $this->http->request('POST', config('captcha.recaptcha.verify_url'), [
            'form_params' => $query,
        ]);

        return json_decode($response->getBody(), true);
    }
}
