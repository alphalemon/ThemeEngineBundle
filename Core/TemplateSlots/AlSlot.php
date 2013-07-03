<?php
/**
 * This file is part of the AlphaLemonThemeEngineBundle and it is distributed
 * under the MIT License. In addiction, to use this bundle, you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://alphalemon.com
 *
 * @license    MIT License
 */

namespace AlphaLemon\ThemeEngineBundle\Core\TemplateSlots;

use AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException;
/**
 * This class represents a slot on a page. The slot is the last html tag, usually a DIV tag,
 * where the displayed content lives.
 *
 * @author AlphaLemon
 */
class AlSlot
{
    private $repeated = 'page';
    private $slotName;
    private $blockType = 'Text';
    private $htmlContent = null;
    private $forceRepeatedDuringDeploying = null;

    /**
     * Constructor
     *
     * @param string    $slotName   The slot name
     * @param array     $options    An array of options, which are [repeated, blockType, htmlContent]
     */
    public function __construct($slotName, array $options = null)
    {
        if(null === $slotName)
        {
            throw new InvalidArgumentException("The slotName param could not be null");
        }

        if(!is_string($slotName))
        {
            throw new InvalidArgumentException("The slotName param must be a string");
        }

        $this->slotName = $slotName;
        if(null !== $options) $this->fromArray($options);
    }

    public function getSlotName()
    {
        return $this->slotName;
    }

    public function setRepeated($v)
    {
        $this->repeated = $v;
    }

    public function getRepeated()
    {
        return $this->repeated;
    }
    
    public function getForceRepeatedDuringDeploying()
    {
        return $this->forceRepeatedDuringDeploying;
    }

    public function setBlockType($v)
    {
        $this->blockType = ucfirst($v);
    }

    public function getBlockType()
    {
        return $this->blockType;
    }

    public function setContent($v)
    {
        $this->htmlContent = $v;
    }

    public function getContent()
    {
        return $this->htmlContent;
    }

    public function toArray()
    {
        return array(
            'slotName' => $this->slotName,
            'repeated' => $this->repeated,
            'blockType' => $this->blockType,
            'htmlContent' => $this->htmlContent,
        );
    }

    /**
     *
     * @param array $options    An array of options
     */
    protected function fromArray(array $options)
    {
        $this->repeated = 'page';
        if (array_key_exists('repeated', $options)) {
            $repeated =  explode("|", $options['repeated']);
            $this->repeated = $repeated[0];
            if (isset($repeated[1])) {
                $this->forceRepeatedDuringDeploying = $repeated[1];
            }
        }

        $blockType = (array_key_exists('blockType', $options)) ? ucfirst($options['blockType']) : 'Text';
        $this->blockType = $blockType;

        if(array_key_exists('htmlContent', $options)) $this->setContent($options['htmlContent']);
    }
}
