<?php

namespace TemplateMethodPattern\CaffeineBeverageWithHook;

class TeaWithHook extends CaffeineBeverageWithHook
{
    public function brew()
    {
        echo "Steepign the tea!\n";
    }

    public function addCondiments()
    {
        echo "Adding Lemon!\n";
    }
}