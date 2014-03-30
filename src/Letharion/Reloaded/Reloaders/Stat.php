<?php

namespace Letharion\Reloaded\Reloaders;

class Stat {
  protected $file_stat;
  protected $validator;
  protected $path;
  protected $function;

  public function __construct($path, $validator) {
    $this->path = $path;
    $this->validator = $validator;
    $this->file_stat = stat($this->path);
    $this->load();
  }

  public function reload() {
    $file_stat = stat($this->path);

    $ret = $file_stat['mtime'] > $this->file_stat['mtime'];

    $this->file_stat = $file_stat;

    return $ret;
  }

  public function getFunction() {
    if ($this->reload()) {
      $this->load();
    }

    return $this->function;
  }

  protected function load() {
    $function = include $this->path;

    if ($this->validator->validate($function)) {
      $this->function = $function;
    }
  }
}
