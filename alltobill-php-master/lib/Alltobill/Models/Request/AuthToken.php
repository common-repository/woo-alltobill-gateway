<?php
/**
 * The AuthToken request model
 * @author    Markus Kling <markus.kling@alltobill.com>
 * @copyright 2017 Alltobill GmbH
 * @since     v1.0
 */
namespace Alltobill\Models\Request;

/**
 * Class AuthToken
 * @package Alltobill\Models\Request
 */
class AuthToken extends \Alltobill\Models\Base
{
    protected $userId = 0;

    /**
     * The user id of the user you want an auth token for
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the user id you would like to get an auth token for
     * 
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseModel()
    {
        return new \Alltobill\Models\Response\AuthToken();
    }
}
