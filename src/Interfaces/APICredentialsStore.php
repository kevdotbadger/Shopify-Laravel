<?php

namespace Shopify\Interfaces;

interface APICredentialsStore {

    public function setSecret($secret);
    public function getSecret();

    public function setKey($key);
    public function getKey();

}
