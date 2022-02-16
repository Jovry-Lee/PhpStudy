<?php

namespace TemplateMethodPattern\CaffeineBeverageWithHook;

class CoffeeWithHook extends CaffeineBeverageWithHook
{
    public function brew()
    {
        echo "Dripping Coffee through filter!\n";
    }

    public function addCondiments()
    {
        echo "Adding Sugar and Milk!!\n";
    }

    public function customerWantsConditions()
    {
        $answer = $this->getUserInput();
        if (strtolower(substr($answer, 0, 1)) == 'y') {
            return true;
        } else {
            return false;
        }
    }

    public function getUserInput()
    {
        fwrite(STDOUT, "Would you like milk and sugar woth your coffee (y/n)");
        try {
            $answer = trim(fgets(STDIN));
        } catch (\Exception $e){
            throw $e;
        }

        if (empty($answer)) {
            $answer = 'no';
        }

        return $answer;
    }
}