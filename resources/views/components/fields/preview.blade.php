{{--
    This component renders the correct field preview in preview mode.
    Since field data lives in Alpine.js state, we use x-html with Alpine bindings.
    The actual <input>, <select>, <textarea> are rendered via Alpine template rendering.
--}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        <span x-text="field.label"></span>
        <span x-show="field.required" class="text-red-500 ml-0.5">*</span>
    </label>

    {{-- Text / Number / Email / Phone / Date --}}
    <template x-if="['text','number','email','phone','date'].includes(field.type)">
        <input
            :type="field.type === 'phone' ? 'tel' : field.type"
            :placeholder="field.placeholder"
            :value="field.defaultValue"
            :required="field.required"
            :minlength="field.minChars || null"
            :maxlength="field.maxChars || null"
            :class="field.cssClass"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                   focus:bg-white transition duration-150"
        />
    </template>

    {{-- Textarea --}}
    <template x-if="field.type === 'textarea'">
        <textarea
            :placeholder="field.placeholder"
            :required="field.required"
            :minlength="field.minChars || null"
            :maxlength="field.maxChars || null"
            :class="field.cssClass"
            rows="3"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                   focus:bg-white resize-y transition duration-150"
            x-text="field.defaultValue"
        ></textarea>
    </template>

    {{-- Dropdown --}}
    <template x-if="field.type === 'dropdown'">
        <select
            :required="field.required"
            :class="field.cssClass"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                   focus:bg-white transition duration-150"
        >
            <option value="">Select an option...</option>
            <template x-for="opt in field.options" :key="opt">
                <option :value="opt" x-text="opt"></option>
            </template>
        </select>
    </template>

    {{-- Radio --}}
    <template x-if="field.type === 'radio'">
        <div class="space-y-2">
            <template x-for="opt in field.options" :key="opt">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="radio" :name="field.id" :value="opt" :required="field.required"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-400" />
                    <span class="text-sm text-gray-700" x-text="opt"></span>
                </label>
            </template>
        </div>
    </template>

    {{-- Checkboxes --}}
    <template x-if="field.type === 'checkbox'">
        <div class="space-y-2">
            <template x-for="opt in field.options" :key="opt">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" :name="field.id + '[]'" :value="opt"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-400" />
                    <span class="text-sm text-gray-700" x-text="opt"></span>
                </label>
            </template>
        </div>
    </template>

    {{-- Hidden --}}
    <template x-if="field.type === 'hidden'">
        <div class="px-3 py-2 text-sm border border-dashed border-gray-300 rounded-lg bg-gray-100 text-gray-400 italic">
            Hidden field — value: <span x-text="field.defaultValue || '(empty)'"></span>
        </div>
    </template>
</div>
