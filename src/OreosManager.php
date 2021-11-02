<?php

namespace Takepart\Oreos;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class OreosManager
{
    protected array $config;
    protected Collection $groups;
    protected array $consents;

    public function __construct()
    {
        $this->config = $this->loadConfig();
        $this->groups = $this->loadGroups();
        $this->consents = $this->loadConsents();
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getGroupsWithInfo(): Collection
    {
        return $this->groups
            ->map(function($group, $key) {
                $explicit = $this->isGroupExplicitlySet($key);
                $consent = $this->isGroupConsent($key);
                $default = $group['default'];
                $required = $group['required'];

                return [
                    'handle' => $key,
                    'consent' => $consent,
                    'explicit' => $explicit,
                    'checked' => (($explicit && $consent) || (!$explicit && $default)),
                    'default' => $default,
                    'required' => $required,
                    'title' => Str::title($key),
                    // 'title' => statamic content,
                    // 'description' => statamic content,
                ];
            });
    }

    public function isGroupAvailable(string $key): bool
    {
        $groups = $this->getConfig('groups');
        return isset($groups[$key]);
    }

    public function isGroupExplicitlySet(string $key): bool
    {
        if (! $this->isCookieSet()) {
            return false;
        }

        return $this->getCookieData()->has($key);
    }

    public function isGroupConsent(string $key): bool
    {
        if (! $this->isGroupExplicitlySet($key)) {
            return false;
        }

        return $this->getCookieData()->get($key);
    }

    public function setGroupConsent(string $key, bool $consent = true)
    {
        $this->consents[$key] = $consent;
    }

    public function saveConsents()
    {
        Cookie::queue(
            $this->getConfig('name'),
            collect($this->consents),
            $this->getConfig('expires_after')
        );
    }

    public function isCookieSet(): bool
    {
        return Cookie::has( $this->getConfig('name') );
    }

    protected function getCookie(): string
    {
        return Cookie::get( $this->getConfig('name') );
    }

    protected function getCookieData(): Collection
    {
        return collect(json_decode( $this->getCookie() ));
    }

    protected function getConfig(string $key)
    {
        $config = $this->config[$key];

        if (! isset($config)) {
            throw new Exception('Can not find configuration within `statamic.oreos` with key `' . $key . '`');
        }

        return $config;
    }

    protected function loadGroups(): Collection
    {
        return collect($this->getConfig('groups'));
    }

    protected function loadConsents(): array
    {
        return $this->groups->map(function($group, $key) {
            return $this->isGroupConsent($key);
        })->toArray();
    }

    protected function loadConfig(): array
    {
        return config('statamic.oreos');
    }

}