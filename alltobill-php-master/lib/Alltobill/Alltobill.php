<?php
/**
 * The Alltobill client API basic class file
 * @author    Markus Kling <markus.kling@alltobill.com>
 * @copyright 2017 Alltobill GmbH
 * @since     v1.0
 */
namespace Alltobill;

/**
 * All interactions with the API can be done with an instance of this class.
 * @package Alltobill
 */
class Alltobill
{
    /**
     * @var Communicator The object for the communication wrapper.
     */
    protected $communicator;

    /**
     * Generates an API object to use for the whole interaction with Alltobill.
     *
     * @param string $instance             The name of the Alltobill instance
     * @param string $apiSecret            The API secret which can be found in the Alltobill administration
     * @param string $communicationHandler The preferred communication handler.
     *                                     If nothing is defined the Alltobill API will use the cURL communicator.
     */
    public function __construct($instance, $apiSecret, $communicationHandler = null)
    {
        if ($communicationHandler) {
            $this->communicator = new \Alltobill\Communicator($instance, $apiSecret, $communicationHandler);
        } else {
            $this->communicator = new \Alltobill\Communicator($instance, $apiSecret);
        }
    }

    /**
     * This method returns the version of the API communicator which is the API version used for this
     * application.
     *
     * @return string The version of the API communicator
     */
    public function getVersion()
    {
        return $this->communicator->getVersion();
    }

    /**
     * This magic method is used to call any method available in communication object.
     *
     * @param string $method The name of the method called.
     * @param array  $args   The arguments passed to the method call. There can only be one argument which is the model.
     *
     * @return \Alltobill\Models\Response\Base[]|\Alltobill\Models\Response\Base
     * @throws \Alltobill\AlltobillException The model argument is missing or the method is not implemented
     */
    public function __call($method, $args)
    {
        if (!$this->communicator->methodAvailable($method)) {
            throw new \Alltobill\AlltobillException('Method ' . $method . ' not implemented');
        }
        if (empty($args)) {
            throw new \Alltobill\AlltobillException('Argument model is missing');
        }
        $model = current($args);
        return $this->communicator->performApiRequest($method, $model);
    }
}
