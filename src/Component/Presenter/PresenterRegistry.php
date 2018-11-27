<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Presenter;

use Illuminate\Contracts\Foundation\Application;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PresenterRegistry implements PresenterRegistryInterface
{
    /**
     * @var string
     */
    private $presenters;

    /**
     * @var Application
     */
    private $app;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param array|null  $presenters
     */
    public function __construct(Application $app, array $presenters = null)
    {
        $this->app = $app;

        foreach (array_values(($presenters ?? [])) as $presenter) {
            $this->add($presenter);
        }
    }

    /**
     * Register a presenter class.
     *
     * @param string $presenterClass
     */
    public function add(string $presenterClass): void
    {
        $implements = class_implements($presenterClass);

        if (!in_array(PresenterInterface::class, $implements, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Presenter class "%s" should implement "%s"',
                    $presenterClass,
                    PresenterInterface::class
                )
            );
        }

        $name = $presenterClass;

        if (in_array(NameablePresenterInterface::class, $implements, true)) {
            $name = $presenterClass::{'name'}();
        }

        $this->presenters[$name] = $presenterClass;
    }

    /**
     * Check the given presenter is exist.
     *
     * @param string $presenter
     *
     * @return bool
     */
    public function has(string $presenter): bool
    {
        return array_key_exists($presenter, $this->presenters);
    }

    /**
     * Get a presenter.
     *
     * @param string $presenter
     *
     * @return PresenterInterface
     */
    public function get(string $presenter): PresenterInterface
    {
        if (!$this->has($presenter)) {
            throw new RuntimeException(sprintf('Presenter "%s" not found.', $presenter));
        }

        $presenterClass = $this->presenters[$presenter];

        return $this->app->make($presenterClass);
    }
}
