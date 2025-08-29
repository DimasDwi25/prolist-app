@aware(['isTailwind', 'isBootstrap'])

<div {{
    $attributes
        ->merge($this->getToolsAttributes)
        ->class([
            $isTailwind && ($this->getToolsAttributes['default-styling'] ?? true) ? 'flex-col' : null,
            $isBootstrap && ($this->getToolsAttributes['default-styling'] ?? true) ? 'd-flex flex-column' : null,
        ])
        ->except(['default', 'default-styling', 'default-colors'])
}}>
    {{ $slot }}
</div>
