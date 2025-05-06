<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;
use Illuminate\Support\Str;

abstract class Factory extends BaseFactory
{
    /**
     * surcharge de la fonction pour permettre de sortir le répertoire Models de App.
     *
     * @var string
     */
    public function modelName()
    {
        $resolver = static::$modelNameResolver ?: function (self $factory) {
            $namespacedFactoryBasename = Str::replaceLast(
                'Factory',
                '',
                Str::replaceFirst(static::$namespace, '', get_class($factory))
            );

            $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));

            // $appNamespace = static::appNamespace();
            $appNamespace = "";

            return class_exists($appNamespace . 'Models\\' . $namespacedFactoryBasename)
                ? $appNamespace . 'Models\\' . $namespacedFactoryBasename
                : $appNamespace . $factoryBasename;
        };

        return $this->model ?: $resolver($this);
    }
}
