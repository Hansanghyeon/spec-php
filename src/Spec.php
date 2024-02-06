<?php

namespace Hansanghyeon/Spec;

interface ISpecification {
  public function isSatisfiedBy($candidate): bool;
  public function and($other): ISpecification;
  public function andNot($other): ISpecification;
  public function or($other): ISpecification;
  public function orNot($other): ISpecification;
  public function not(): ISpecification;
}

abstract class CompositeSpecification implements ISpecification {
  abstract public function isSatisfiedBy($candidate): bool;

  public function and($other): ISpecification {
    return new AndSpecification($this, $other);
  }

  public function andNot($other): ISpecification {
    return new AndNotSpecification($this, $other);
  }

  public function or($other): ISpecification {
    return new OrSpecification($this, $other);
  }

  public function orNot($other): ISpecification {
    return new OrNotSpecification($this, $other);
  }

  public function not(): ISpecification {
    return new NotSpecification($this);
  }
}

class AndSpecification extends CompositeSpecification {
  private $leftCondition;
  private $rightCondition;

  public function __construct($leftCondition, $rightCondition) {
    $this->leftCondition = $leftCondition;
    $this->rightCondition = $rightCondition;
  }

  public function isSatisfiedBy($candidate): bool {
    return $this->leftCondition->isSatisfiedBy($candidate) &&
      $this->rightCondition->isSatisfiedBy($candidate);
  }
}

class AndNotSpecification extends CompositeSpecification {
  private $leftCondition;
  private $rightCondition;

  public function __construct($leftCondition, $rightCondition) {
    $this->leftCondition = $leftCondition;
    $this->rightCondition = $rightCondition;
  }

  public function isSatisfiedBy($candidate): bool {
    return $this->leftCondition->isSatisfiedBy($candidate) &&
      !$this->rightCondition->isSatisfiedBy($candidate);
  }
}

class OrSpecification extends CompositeSpecification {
  private $leftCondition;
  private $rightCondition;

  public function __construct($leftCondition, $rightCondition) {
    $this->leftCondition = $leftCondition;
    $this->rightCondition = $rightCondition;
  }

  public function isSatisfiedBy($candidate): bool {
    return $this->leftCondition->isSatisfiedBy($candidate) ||
      $this->rightCondition->isSatisfiedBy($candidate);
  }
}

class OrNotSpecification extends CompositeSpecification {
  private $leftCondition;
  private $rightCondition;

  public function __construct($leftCondition, $rightCondition) {
    $this->leftCondition = $leftCondition;
    $this->rightCondition = $rightCondition;
  }

  public function isSatisfiedBy($candidate): bool {
    return $this->leftCondition->isSatisfiedBy($candidate) ||
      !$this->rightCondition->isSatisfiedBy($candidate);
  }
}

class NotSpecification extends CompositeSpecification {
  private $wrapped;

  public function __construct($wrapped) {
    $this->wrapped = $wrapped;
  }

  public function isSatisfiedBy($candidate): bool {
    return !$this->wrapped->isSatisfiedBy($candidate);
  }
}

class Spec extends CompositeSpecification {
  private $expression;

  public function __construct($expression) {
    $this->expression = $expression;
  }

  public function isSatisfiedBy($candidate): bool {
    return call_user_func($this->expression, $candidate);
  }
}

