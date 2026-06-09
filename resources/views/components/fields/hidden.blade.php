@props([
    'field' => [],
])

<input
    type="hidden"
    name="{{ $field['id'] ?? '' }}"
    value="{{ $field['defaultValue'] ?? '' }}"
    class="{{ $field['cssClass'] ?? '' }}"
/>
