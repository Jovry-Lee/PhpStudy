<?php

namespace DataStructure\Queue;

/**
 * Queue class that store element in queue
 * Remove element from font
 * Insert element to back
 */
class Queue
{
    private $font = null;
    private $back = null;

    /**
     * Check whether the queue is empty or not
     * @return boolean
     * public function isEmpty(){ return false; }  //stub
     */
    public function isEmpty(){
        return $this->font == null;
    }

    /**
     * Insert element at the back of queue
     * @param $value
     * public function enqueue($value){} //stub
     */
    public function enqueue($value){
        $oldBack = $this->back;
        $this->back = new Element();
        $this->back->value = $value;
        if($this->isEmpty()){
            $this->font = $this->back;
        }else{
            $oldBack->next = $this->back;
        }
    }

    /**
     * Remove element from the font of queue
     * @return $value
     * public function dequeue(){ return 0; } //stub
     */
    public function dequeue(){
        if($this->isEmpty()){
            return null;
        }
        $removedValue = $this->font->value;
        $this->font = $this->font->next;
        return $removedValue;
    }
}