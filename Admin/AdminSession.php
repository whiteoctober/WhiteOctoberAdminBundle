<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;

/**
 * Admin.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class AdminSession
{
    private $request;
    private $session;
    private $parameter;
    private $hash;

    /**
     * Constructor.
     *
     * @param Request $request       The request.
     * @param Session $session       The session.
     * @param string  $parameterName The parameter name.
     */
    public function __construct(Request $request, Session $session, $parameterName)
    {
        $this->request = $request;
        $this->session = $session;
        $this->parameterName = $parameterName;
    }

    /**
     * Returns the request.
     *
     * @return Request The request.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the session.
     *
     * @return Session The session.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Returns the parameter name.
     *
     * @return string The parameter name.
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Returns the hash.
     *
     * @return string The hash.
     */
    public function getHash()
    {
        if (null === $this->hash) {
            if (!$hash = $this->request->query->get($this->parameterName)) {
                do {
                    $hash = substr(sha1(microtime().mt_rand(11111, 99999)), 0, 7);
                } while ($this->session->has($hash));

                $this->request->query->set($this->parameterName, $hash);
            }
            $this->hash = $hash;
        }

        return $this->hash;
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     */
    public function set($name, $value)
    {
        $data = $this->getData();
        $data[$name] = $value;
        $this->saveData($data);
    }

    /**
     * Returns a parameter.
     *
     * @param string $name    The name.
     * @param mixed  $default The default value (optional, null by default).
     */
    public function get($name, $default = null)
    {
        $data = $this->getData();

        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    /**
     * Removes one or several parameters.
     *
     * @param string|array $names The parameter names.
     */
    public function remove($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        $data = $this->getData();
        foreach ($names as $name) {
            unset($data[$name]);
        }
        $this->saveData($data);
    }

    /**
     * Clears the session.
     */
    public function clear()
    {
        $this->session->remove($this->getHash());
    }

    private function getData()
    {
        return $this->session->get($this->getHash(), array());
    }

    private function saveData(array $data)
    {
        $this->session->set($this->getHash(), $data);
    }
}
