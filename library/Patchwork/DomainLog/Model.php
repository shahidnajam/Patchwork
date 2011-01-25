<?php

interface Patchwork_DomainLog_Model
{
    function __construct(Patchwork_Storage_Service $service);
    function createFromEvent(array $event);
}