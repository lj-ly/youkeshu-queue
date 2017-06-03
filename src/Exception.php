<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace queue;

/**
 * Class Exception
 * @package xutl\queue
 */
class Exception extends \Exception
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return null;
    }
}