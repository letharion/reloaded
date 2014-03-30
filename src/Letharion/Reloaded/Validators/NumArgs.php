<?php

namespace Letharion\Reloaded\Validators;

class NumArgs {
  protected $number;

  public function __construct($number) {
    $this->number = $number;
  }

  public function validate($function) {
    $refFunc = new \ReflectionFunction($function);
    return (
      count($refFunc->getParameters()) === $this->number
      && is_callable($function)
    );
  }
}
