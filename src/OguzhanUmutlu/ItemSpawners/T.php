<?php

namespace OguzhanUmutlu\ItemSpawners;

class T { // Translator
    public static function T(string $key, array $arguments = []) {
        return str_replace(["{line}", "&", "\\n"], ["\n", "§", "\n"], str_replace(array_map(function($n){return "%".(int)$n;}, array_keys($arguments)), array_values($arguments), ItemSpawners::$instance->langConfig->getNested($key))
        );
    }
}