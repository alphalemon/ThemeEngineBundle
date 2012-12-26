<?php
/**
 * This file is part of the AlphaLemonPageTreeBundle and it is distributed
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

namespace AlphaLemon\ThemeEngineBundle\Core\Rendering\Compiler\ThemeEngineListenersCollection;

/**
 * Collects the theme engine registered listeners for the alpha_lemon_theme_engine.event_listener
 * tag
 *
 * @author alphalemon <webmaster@alphalemon.com>
 */
class AlThemeEngineListenersCollection implements \Iterator, \Countable
{
    private $listeners = array();

    /**
     * Adds the listener id to the collections
     *
     * @param string $listenerId
     */
    public function addListenerId($listenerId)
    {
        if (null !== $this->getListenerId($listenerId)) {
            return;
        }
        
        $this->listeners[$listenerId] = $listenerId;
    }

    /**
     * Returns the listener id
     *
     * @param string $listenerId
     * @return null|string
     */
    public function getListenerId($listenerId)
    {
        return (array_key_exists($listenerId, $this->listeners)) ? $this->listeners[$listenerId] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return (current($this->listeners) !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->listeners);
    }
}