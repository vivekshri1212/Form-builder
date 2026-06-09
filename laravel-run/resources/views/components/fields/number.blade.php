@props([
    'field' => [],
])

<div class="{{ $field['cssClass'] ?? '' }}">
    @if(!empty($field['label']))
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $field['label'] }}
            @if(!empty($field['required']))
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input
        type="number"
        name="{{ $field['id'] ?? '' }}"
        placeholder="{{ $field['placeholder'] ?? '' }}"
        value="{{ $field['defaultValue'] ?? '' }}"
        @if(!empty($field['required'])) required @endif
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50
               focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
               focus:bg-white transition duration-150"
    />
</div>
