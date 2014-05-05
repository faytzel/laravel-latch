<?php namespace Faytzel\LaravelLatch;

use Faytzel\LaravelLatch\Api\Latch;

class LaravelLatch {

    protected $api;
    protected $config;

    protected $lang;
    protected $crypt;

    protected $errorString;
    protected $errorCode;
    protected $response;
    protected $responseData;
    protected $responseError;

    public function __construct(Latch $api, $config)
    {
        $this->api    = $api;
        $this->config = $config;

        $app          = app();
        $this->lang   = $app['translator'];
        $this->crypt  = $app['encrypter'];
    }

    /**
     * Check it if Latch account is locked
     * @param  string|null $accountId
     * @param  boolean $encrypt Default true. Define if accountId is encrypted
     * @return boolean
     */
    public function locked($accountId, $encrypt = true)
    {
        $this->reset();
        
        // if not define accountId, return false
        if (is_null($accountId)) return false;

        $accountIdFinal = $this->getAccountId($accountId, $encrypt);

        $this->setResponse($this->api->status($accountIdFinal));

        if ( ! $this->throwError())
        {
            // If is "on", Latch gives us access (without latch)
            $latchAppId = $this->config['app_id'];
            if ($this->responseData->operations->$latchAppId->status == 'on')
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check it if Latch account is unlocked
     * @param  string|null $accountId
     * @param  boolean $encrypt Default true. Define if accountId is encrypted
     * @return boolean
     */
    public function unlocked($accountId, $encrypt = true)
    {
        return ! $this->locked($accountId, $encrypt);
    }

    /**
     * Linked with Latch
     * @param  string  $token
     * @param  boolean $encrypt Default true. Define if accountId is encrypted
     * @return string|boolean
     */
    public function pair($token, $encrypt = true)
    {
        $this->reset();

        $this->setResponse($this->api->pair($token));

        if ( ! $this->throwError())
        {
            $accountId = $this->responseData->accountId;

            if ($encrypt)
            {
                return $this->crypt->encrypt($accountId);
            }

            return $accountId;
        }

        return false;
    }

    /**
     * Unlinked Latch
     * @param  string $accountId
     * @param  boolean $encrypt Default true. Define if accountId is encrypted
     * @return boolean
     */
    public function unpair($accountId, $encrypt = true)
    {
        $this->reset();

        $accountIdFinal = $this->getAccountId($accountId, $encrypt);

        $this->setResponse($this->api->unpair($accountIdFinal));

        if ( ! $this->throwError())
        {
            return true;
        }

        return false;
    }

    /**
     * Get error (string message)
     * @return string
     */
    public function error()
    {
        return $this->errorString;
    }

    /**
     * Get error (error code)
     * @param  int $errorCode
     * @return int
     */
    public function errorCode($errorCode)
    {
        return $this->errorCode;
    }

    protected function setResponse($response)
    {
        $this->response      = $response;
        $this->responseData  = $response->getData();
        $this->responseError = $response->getError();
    }

    protected function throwError()
    {
        if (count($this->responseError) == 0)
        {
            return false;
        }

        $this->errorCode = $this->responseError->getCode();

        // Get error message
        $this->errorString = $this->getErrorString($this->errorCode);

        return true;
    }

    protected function reset()
    {
        $this->response      = null;
        $this->responseData  = null;
        $this->responseError = null;
        $this->errorString   = null;
        $this->errorCode     = null;
    }

    protected function getErrorString($errorCode)
    {
        if ($this->lang->has('laravel-latch::latch.errorCode_' . $errorCode))
        {
            return $this->lang->get('laravel-latch::latch.errorCode_' . $errorCode);
        }
        else
        {
            return $this->lang->has('laravel-latch::latch.errorCode_generic');
        }
    }

    protected function getAccountId($accountId, $encrypt)
    {
        if ($encrypt)
        {
            return $this->crypt->decrypt($accountId);
        }
        else
        {
            return $accountId;
        }
    }

}