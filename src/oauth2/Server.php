<?php
	namespace sys\oauth2;

	/* for debug */
	class Server extends \OAuth2\Server{
    public function setConfig($name, $value)
    {
		//echo "set config {$name} = {$value}"; exit();
        $this->config[$name] = $value;
    }

    protected function createDefaultResourceController()
    {
		//echo 'use_jwt_access_tokens = '.$this->config['use_jwt_access_tokens']; exit();
        if ($this->config['use_jwt_access_tokens']) {
            // overwrites access token storage with crypto token storage if "use_jwt_access_tokens" is set
            if (!isset($this->storages['access_token']) || !$this->storages['access_token'] instanceof JwtAccessTokenInterface) {
                $this->storages['access_token'] = $this->createDefaultJwtAccessTokenStorage();
            }
        } elseif (!isset($this->storages['access_token'])) {
            throw new \LogicException("You must supply a storage object implementing OAuth2\Storage\AccessTokenInterface or use JwtAccessTokens to use the resource server");
        }

        if (!$this->tokenType) {
            $this->tokenType = $this->getDefaultTokenType();
        }

        $config = array_intersect_key($this->config, array('www_realm' => ''));

        return new ResourceController($this->tokenType, $this->storages['access_token'], $config, $this->getScopeUtil());
    }

	}
?>
