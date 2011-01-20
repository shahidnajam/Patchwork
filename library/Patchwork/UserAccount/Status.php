<?php

interface Patchwork_UserAccount_Status
{
    const STATUS_NEW = 10;
    const STATUS_UNLOCKED = 1000;
    const STATUS_LOCKED = 20;
    
    function getStatus();

    function setStatus();

    function isStatusUnlocked();
}
