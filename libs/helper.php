<?php

function helpMenu(): void
{
    echo "\e[32mThis is a small tool to get to know which tables in MySQL are affected by actions via DB inserts and deletes.\n" .
        "First get the tables from the DB in MySQL run: $ php learndb.php get-tables\n" .
        "1) Before you make a change (ex. fill out and submit a form) in MySQL run: $ php learndb.php before\n" .
        "2) After you make a change in MySQL run: $ php learndb.php after\n" .
        "3) To see what was changed in the DB tables, run: $ php learndb.php compare \e[0m \n";
}

function convert($size): string
{
    $unit = ['b','kb','mb','gb','tb','pb'];
    return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),2).'('.$unit[$i].')';
}
