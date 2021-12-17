<?php

namespace App\Cli;

class Console
{

    public static function out($message)
    {
        echo "$message";
    }

    public static function write($message)
    {
        static::out("$message\n");
    }

    public static function askForInput($prompt): string
    {
        static::out("$prompt: ");
        return trim(fgets(STDIN));
    }

    public static function askForSecret($prompt): string
    {
        $command = "/usr/bin/env sh -c 'read -s -p \""
            . addslashes("$prompt: ")
            . "\" password && echo \$password'";
        return rtrim(shell_exec($command));
    }

    public static function readChar(): string
    {
        $term = shell_exec("stty -g");
        system("stty -icanon");
        $char = fgetc(STDIN);
        system("stty $term");
        return $char;
    }

    public static function space(): void
    {
        static::write("\n");
    }

    public static function clean(): void
    {
        system('clear');
//        static::write(chr(27).chr(91).'H'.chr(27).chr(91).'J');
    }

    public static function exitConsole($buyMessage): void
    {
        static::space();
        die("$buyMessage\n\n");
    }

    public static function alert(string $message)
    {
        static::write("===============================================================");
        static::write($message);
        static::write("===============================================================");
    }

}