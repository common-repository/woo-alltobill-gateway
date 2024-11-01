<?php
/**
 * The Page response model
 * @author    Markus Kling <markus.kling@alltobill.com>
 * @copyright 2017 Alltobill GmbH
 * @since     v1.0
 */
namespace Alltobill\Models\Response;

/**
 * Class Page
 * @package Alltobill\Models\Response
 */
class Page extends \Alltobill\Models\Request\Page
{
    protected $createdAt = 0;

    /**
     * @return int
     */
    public function getCreatedDate()
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     */
    public function setCreatedDate($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
}
