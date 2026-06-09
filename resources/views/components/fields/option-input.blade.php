@props([
    'label'       => '',
    'type'        => 'text',
    'placeholder' => '',
])

<div>
    @if($label)
        <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 bg-gray-50
            focus:outline-none focus:border-blue-400 focus:bg-white transition-colors duration-150']) }}
    />
</div>
