#!/usr/bin/env php
<?php

$duration = 3;

file_put_contents('php://stderr', "plop1 plop1 plop1 plop1 plop1 plop1 plop2 plop2 plop2 plop2 plop2 plop2 plop2 plop2 \n");

while ($duration--)
{
  echo "Duration:Duration:Duration:Duration:Duration:Duration:Duration:Duration:Duration:$duration;\n";
  sleep(1);
}