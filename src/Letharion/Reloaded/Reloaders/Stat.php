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
    // Force PHP to reload stat data.
    clearstatcache(FALSE, $this->path);

    // On a busy system, it is likely that stating happens to coincide with a file write.
    // Silence the error and skip the update this time; it will likely succeed next time.
    if (!$file_stat = @stat($this->path)) {
      return FALSE;
    }

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
