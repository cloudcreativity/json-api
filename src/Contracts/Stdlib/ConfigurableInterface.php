<?php

namespace CloudCreativity\JsonApi\Contracts\Stdlib;

interface ConfigurableInterface
{

  /**
   * @param array $config
   * @return $this
   */
  public function configure(array $config);
}