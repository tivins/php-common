<?php

namespace Tivins\Core\Proc;

abstract class ProcHooks
{
    protected Proc $proc;
    protected int  $callbackFrequency = 200000;

    public function setProc(Proc $proc): void { $this->proc = $proc; }
    public function getProc(): Proc { return $this->proc; }
    public function getCallbackFrequency(): int { return $this->callbackFrequency; }
    public function setCallbackFrequency(int $value): void { $this->callbackFrequency = $value; }
    abstract public function onStart();
    abstract public function onUpdate();
    abstract public function onFinished();
}