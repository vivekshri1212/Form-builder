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

    <select
        name="{{ $field['id'] ?? '' }}"
        @if(!empty($field['required'])) required @endif
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50
               focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
               focus:bg-white appearance-none transition duration-150"
    >
        <option value="">Select an option...</option>
        @foreach($field['options'] ?? [] as $option)
            <option value="{{ $option }}"
                {{ isset($field['defaultValue']) && $field['defaultValue'] === $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
</div>
