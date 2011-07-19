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

class AdminSession
{
    private $request;
    private $session;
    private $parameter;
    private $hash;

    public function __construct(Request $request, Session $session, $parameter)
    {
        $this->request = $request;
        $this->session = $session;
        $this->parameter = $parameter;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function getHash()
    {
        if (null === $this->hash) {
            if (!$hash = $this->request->query->get($this->parameter)) {
                do {
                    $hash = substr(sha1(microtime().mt_rand(11111, 99999)), 0, 7);
                } while ($this->session->has($hash));

                $this->request->query->set($this->parameter, $hash);
            }
            $this->hash = $hash;
        }

        return $this->hash;
    }

    public function set($name, $value)
    {
        $data = $this->getData();
        $data[$name] = $value;
        $this->saveData($data);
    }

    public function get($name, $default = null)
    {
        $data = $this->getData();

        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

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
