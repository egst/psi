<?php declare(strict_types = 1);

namespace Psi;

use \ReflectionClass;
use \StdClass;
use \Stringable;

use \Psi\Exception;
use \Psi\Std\Cast;
use \Psi\Type\ArrayKeyType;
use \Psi\Type\ArrayType;
use \Psi\Type\ClassStringType;
use \Psi\Type\MixedType;
use \Psi\Type\NamedType;
use \Psi\Type\StringType;
use \Psi\Type\TypeInterface;
use \Psi\Type\UnionType;

/**
 *  @template T
 *
 *  This is an experimental tool for passing run-time type information.
 *  The type constructs provided mimic some of the Psalm extensions to the PHP type system.
 */
class Type implements Stringable {

    /** @param T $value */
    protected function __construct (
        protected TypeInterface $type,
        protected mixed $value
    ) {}

    /** @return T */
    public function value (): mixed {
        return $this->value;
    }

    public function __toString (): string {
        return Cast::string($this->type);
    }

    /**
     *  @template A
     *  @template B
     *  @param Type<A> $a
     *  @param Type<B> $b
     *  @psalm-assert-if-true Type<B> $a
     *  @psalm-assert-if-true Type<A> $b
     */
    public static function same (Type $a, Type $b): bool {
        return $a->type->same($b->type);
    }

    /**
     *  @template U
     *  @param Type<U> $type
     *  @psalm-assert-if-true Type<U> $this
     */
    public function contains (Type $type): bool {
        return $this->type->contains($type->type);
    }

    /**
     *  @template U
     *  @param Type<U> $type
     *  @psalm-assert-if-true Type<U> $this
     */
    public function satisfies (Type $type): bool {
        return $type->type->contains($this->type);
    }

    /** @psalm-assert-if-true T $value */
    public function check (mixed $value): bool {
        return $this->type->check($value);
    }

    // TODO: Maybe add Type::cast?
    // (Type::string())->cast(123);

    /** @return Type<bool> */
    public static function bool (): Type {
        return new Type(new NamedType('bool'), (bool) null);
    }

    /** @return Type<int> */
    public static function int (): Type {
        return new Type(new NamedType('int'), (int) null);
    }

    /** @return Type<float> */
    public static function float (): Type {
        return new Type(new NamedType('float'), (float) null);
    }

    /** @return Type<string> */
    public static function string (): Type {
        return new Type(new StringType(), (string) null);
    }

    /** @return Type<object> */
    public static function object (): Type {
        /** @var object (not StdClass) */ $v = (object) null;
        return new Type(new NamedType('object'), $v);
    }

    /** @return Type<null> */
    public static function null (): Type {
        return new Type(new NamedType('null'), null);
    }

    /** @return Type<false> */
    public static function false (): Type {
        return new Type(new NamedType('false'), false);
    }

    /** @return Type<mixed> */
    public static function mixed (): Type {
        /** @var mixed */ $v = null;
        return new Type(new MixedType(), $v);
    }

    /** @return Type<array-key> */
    public static function arrayKey (): Type {
        /** @var array-key */ $v = 0;
        return new Type(new ArrayKeyType(), $v);
    }

    /**
     *  @template U
     *  @param Type<U> $type
     *  @return Type<?U>
     */
    public static function nullable (Type $type): Type {
        /** @var ?U */ $v = null;
        return new Type(new UnionType($type->type, Type::null()->type), $v);
    }

    /**
     *  @template U
     *  @param Type<U> $type
     *  @return Type<U | false>
     */
    public static function falsable (Type $type): Type {
        /** @var U | false */ $v = false;
        return new Type(new UnionType($type->type, Type::false()->type), $v);
    }

    /**
     *  @template U
     *  @template V
     *  @param Type<U> $a
     *  @param Type<V> $b
     *  @return Type<U | V>
     */
    public static function union (Type $a, Type $b): Type {
        /** @var U | V */ $v = $a->value();
        return new Type(new UnionType($a->type, $b->type), $v);
    }

    /**
     *  @template Key
     *  @template Value
     *  @param ?Type<Key> $key
     *  @param ?Type<Value> $value
     *  @return Type<array<($key is null ? array-key : Key), ($value is null ? mixed : Value)>>
     */
    public static function array (?Type $key = null, ?Type $value = null): Type {
        return new Type(new ArrayType($key->type ?? Type::mixed()->type, $value->type ?? Type::mixed()->type), []);
    }

    /**
     *  @template Value
     *  @param ?Type<Value> $value
     *  @return Type<array<array-key, ($value is null ? mixed : Value)>>
     */
    public static function arrayOf (?Type $value = null): Type {
        return new Type(new ArrayType(Type::mixed()->type, $value->type ?? Type::mixed()->type), []);
    }

    /**
     *  @template Value
     *  @param ?Type<Value> $value
     *  @return Type<list<($value is null ? mixed : Value)>>
     */
    public static function list (?Type $value = null): Type {
        return new Type(new ArrayType(Type::int()->type, $value->type ?? Type::mixed()->type, true), []);
    }

    /**
     *  @template U
     *  @param ?class-string<U> $class
     *  @return ($class is null ? Type<class-string> : Type<class-string<U>>)
     */
    public static function classString (?string $class = null): Type {
        return new Type(new NamedType('class-string'), $class ?? \StdClass::class);
    }

    /**
     *  @template U
     *  @param class-string<U> $class
     *  @return Type<U>
     *  @throws Exception\ClientError
     *  TODO: Does this work on enums?
     */
    public static function named (string $class): Type {
        if (!class_exists($class))
            throw new Exception\ClientError('Not a class.');
        $v = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
        return new Type(new NamedType($class), $v);
    }

}
