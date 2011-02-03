<?php

interface Patchwork_Storage_Query extends Countable
{
    public function limit($limit);

    public function offset($offset);

    public function __toString();
}