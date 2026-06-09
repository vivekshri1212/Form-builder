@props([
    'field' => [],
])

<div class="{{ $field['cssClass'] ?? '' }}">
    @if(!empty($field['label']))
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $field['label'] }}
            @if(!empty($field['required']))
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="space-y-2">
        @foreach($field['options'] ?? [] as $index => $option)
            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input
                    type="radio"
                    name="{{ $field['id'] ?? '' }}"
                    value="{{ $option }}"
                    @if(!empty($field['required'])) required @endif
                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-400 cursor-pointer"
                />
                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">{{ $option }}</span>
            </label>
        @endforeach
    </div>
</div>
