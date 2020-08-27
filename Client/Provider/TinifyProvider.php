<?php
namespace TIG\TinifyCDN\Client\Provider;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Exception\IDPException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

// @codingStandarsIgnoreFile
class TinifyProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private $urlAuthorize;

    private $urlAccessToken;

    private $accessTokenMethod;

    private $accessTokenResourceOwnerId;

    private $scopes = null;

    private $scopeSeparator;

    private $responseError = 'error';

    private $responseCode;

    private $responseResourceOwnerId = 'id';

    protected $codeChallenge;

    protected $codeVerifier;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->assertRequiredOptions($options);

        $possible   = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
          $this->$key = $value;
        }

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        parent::__construct($options, $collaborators);
    }

    protected function getConfigurableOptions()
    {
        return array_merge($this->getRequiredOptions(), [
            'accessTokenMethod',
            'accessTokenResourceOwnerId',
            'scopeSeparator',
            'responseError',
            'responseCode',
            'responseResourceOwnerId',
        ]);
    }

    protected function getRequiredOptions()
    {
        return [
            'urlAuthorize',
            'urlAccessToken',
            'scopes',
            'codeChallenge',
            'codeVerifier',
        ];
    }

    private function assertRequiredOptions(array $options)
    {
        $missing = array_diff_key(array_flip($this->getRequiredOptions()), $options);

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                'Required options not defined: ' . implode(', ', array_keys($missing))
            );
        }
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->urlAuthorize;
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccessToken;
    }

    public function getDefaultScopes()
    {
        return $this->scopes;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return "This is not implemented yet.";
    }

    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);
        $params = [
            'client_id'     => $this->clientId,
            'code_verifier' => $this->codeVerifier,
            'redirect_uri'  => $this->redirectUri,
        ];

        $params   = $grant->prepareRequestParameters($params, $options);
        $request  = $this->getAccessTokenRequest($params);
        $response = $this->getParsedResponse($request);
        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }
        $prepared = $this->prepareAccessTokenResponse($response);
        $token    = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    protected function getAuthorizationParameters(array $options)
    {
        if (empty($options['state'])) {
            $options['state'] = $this->getRandomState();
        }

        if (empty($options['scope'])) {
            $options['scope'] = $this->getDefaultScopes();
        }

        $options += [
            'response_type'   => 'code',
            'approval_prompt' => 'auto',
            'code_challenge_method' => 'S256',
        ];

        if (is_array($options['scope'])) {
            $separator = $this->getScopeSeparator();
            $options['scope'] = implode($separator, $options['scope']);
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        // Business code layer might set a different redirect_uri parameter
        // depending on the context, leave it as-is
        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirectUri;
        }

        $options['client_id'] = $this->clientId;
        $options['code_challenge'] = $this->codeChallenge;

        return $options;
    }

    protected function getAccessTokenMethod()
    {
        return $this->accessTokenMethod ?: parent::getAccessTokenMethod();
    }

    protected function getAccessTokenResourceOwnerId()
    {
        return $this->accessTokenResourceOwnerId ?: parent::getAccessTokenResourceOwnerId();
    }

    protected function getScopeSeparator()
    {
        return $this->scopeSeparator ?: parent::getScopeSeparator();
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            if (!is_string($error)) {
                $error = var_export($error, true);
            }
            $code  = $this->responseCode && !empty($data[$this->responseCode])? $data[$this->responseCode] : 0;
            if (!is_int($code)) {
                $code = intval($code);
            }
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GenericResourceOwner($response, $this->responseResourceOwnerId);
    }
}
