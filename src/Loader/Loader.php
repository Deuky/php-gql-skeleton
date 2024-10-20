<?php

namespace Vertuoza\Loader;

use Exception;

class Loader
{
    /**
     * @var bool
     */
    protected bool $isLoad = false;

    /**
     * @var bool
     */
    protected bool $isError = false;

    /**
     * @var mixed|callable
     */
    protected mixed $load;

    /**
     * @var mixed
     */
    protected mixed $result;

    public function __construct(callable $load)
    {
        $this->load = $load;
    }

    /**
     * @return static
     */
    public function load(): static
    {
        try {
            $this->result = ($this->load)();
            $this->isError = false;
        } catch (Exception $e) {
            $this->isError = true;
            $this->result = $e;
        } finally {
            $this->isLoad = true;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoad(): bool
    {
        return $this->isLoad;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function get()
    {
        if ($this->isLoad) {
            return $this->result;
        }

        $this->load();

        if (!$this->isLoad) {
            throw new Exception();
        }

        return $this->get();
    }

    /**
     * @return $this
     */
    public function rewind(): static
    {
        unset($this->result);
        $this->isLoad = false;
        $this->isError = false;

        return $this;
    }
}
