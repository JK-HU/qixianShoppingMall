<?php
/**
 * 钱包
 */
namespace app\user\controller;

class Wallet extends Common
{
    public function index()
    {
        return $this->fetch();
    }

    public function recharge()
    {
        return $this->fetch();
    }
}