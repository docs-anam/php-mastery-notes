<?php

namespace Mukhoiran\Test;

class Counter {
    private int $count = 0;

    public function increment() {
        $this->count++;
    }

    public function getCount(): int {
        return $this->count;
    }

    public function decrement() {
        $this->count--;
    }
}