<?php

namespace Drupal\password_protected_page;

use Drupal\node\Entity\Node;

class PasswordClass
{
    protected $cookie_name;
    protected $node;

    public function __construct($node_id)
    {
        $this->cookie_name = "password_protected_" . md5($_SERVER["HTTP_HOST"]);
        $this->node = Node::load($node_id);
    }

    public function getCookieName()
    {
        return $this->cookie_name;
    }

    public function getPassword()
    {
        return $this->node->get('field_page_password')->value;
    }

    public function validatePass($input_password)
    {
        return $input_password == $this->getPassword();
    }


    /**
     * クッキーの検証を行う。
     */
    public function validateCheckPass()
    {
        $cookie_value = \Drupal::request()->cookies->get($this->cookie_name);
        if (empty($cookie_value)) return false;

        return password_verify($this->getPassword(), $cookie_value) ? true : false;
    }
}
