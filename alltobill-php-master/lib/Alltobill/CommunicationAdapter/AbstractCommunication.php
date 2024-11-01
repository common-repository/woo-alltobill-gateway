<?php
/**
 * This class is a template for all communication handler classes.
 * @author    Markus Kling <markus.kling@alltobill.com>
 * @copyright 2017 Alltobill GmbH
 * @since     v1.0
 */
namespace Alltobill\CommunicationAdapter;

/**
 * Class AbstractCommunication
 * @package Alltobill\CommunicationAdapter
 */
abstract class AbstractCommunication
{
    /**
     * Perform an API request
     *
     * @param string $apiUrl
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     */
    abstract public function requestApi($apiUrl, $params = array(), $method = 'POST');
}
