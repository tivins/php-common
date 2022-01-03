#!/usr/bin/env php
<?php

$duration = $argv[1] ?? 3;

while ($duration--)
{
  echo ".";
  sleep(1);
}