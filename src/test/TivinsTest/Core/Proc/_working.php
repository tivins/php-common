#!/usr/bin/env php
<?php

$duration = 3;//$argv[1] ?? 3;

if (rand(0,10)<5) {
    file_put_contents('php://stderr', "plop plop plop plop plop plop plop plop plop plop plop plop plop plop \n");
}

while ($duration--)
{
  echo "Duration:Duration:Duration:Duration:Duration:Duration:Duration:Duration:Duration:$duration;\n";
  sleep(1);
}