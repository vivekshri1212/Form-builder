@extends('layouts.app')

@section('content')
<div
    x-data="formBuilder()"
    x-init="init()"
    class="flex h-screen overflow-hidden bg-transparent"
>

    {{-- ============================================================
         LEFT SIDEBAR — Field Palette
    ============================================================ --}}
    <aside class="w-56 bg-white/80 backdrop-blur-xl border-r border-gray-200/80 flex flex-col flex-shrink-0 shadow-sm">
        <div class="px-4 py-4 border-b border-gray-100">
            <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 p-3 text-white shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🧩</span>
                    <div>
                        <h1 class="font-semibold text-sm tracking-tight">Form Builder</h1>
                        <p class="text-[11px] text-blue-100">Create forms in style</p>
                    </div>
                </div>
            </div>
            <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50/70 p-2.5">
                <p class="text-[11px] font-semibold text-blue-700">Welcome!</p>
                <p class="text-[11px] text-gray-600">Drag fields, arrange them, and publish your form with confidence.</p>
            </div>
        </div>

        <div class="p-3 overflow-y-auto flex-1">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2 px-1">Field Types</p>
            <div id="field-palette" class="space-y-1.5">
                @foreach($fieldTypes as $type)
                    <div
                        draggable="true"
                        data-field-type="{{ $type['type'] }}"
                        @dragstart="onPaletteDragStart($event, '{{ $type['type'] }}')"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50
                               cursor-grab hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700
                               transition-all duration-150 select-none group"
                    >
                        <span class="text-base">{{ $type['icon'] }}</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-blue-700">{{ $type['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Undo / Redo --}}
        <div class="px-3 py-3 border-t border-gray-100 flex flex-col gap-2">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-2.5">
                <p class="text-[11px] font-semibold text-gray-700">Need help?</p>
                <p class="text-[11px] text-gray-500">Use the editor panel to build and preview your form.</p>
            </div>
            <div class="flex gap-2">
                <button @click="undo()" :disabled="historyIndex <= 0"
                    class="flex-1 flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-md border border-gray-200
                           text-xs text-gray-500 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition">
                    ↩ Undo
                </button>
                <button @click="redo()" :disabled="historyIndex >= history.length - 1"
                    class="flex-1 flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-md border border-gray-200
                           text-xs text-gray-500 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition">
                    Redo ↪
                </button>
            </div>
        </div>
    </aside>

    {{-- ============================================================
         CANVAS — Center drop zone
    ============================================================ --}}
    <main class="flex-1 flex flex-col overflow-hidden bg-gray-50">

        {{-- Top bar --}}
        <div class="flex items-center justify-between px-5 py-3 bg-white/80 backdrop-blur-xl border-b border-gray-200/80 shadow-sm">
            <div>
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
                    <h2 class="font-semibold text-gray-800">Canvas</h2>
                </div>
                <p class="text-xs text-gray-500" x-text="fields.length + ' field' + (fields.length !== 1 ? 's' : '') + ' ready for your form'"></p>
            </div>
            <div class="flex gap-2">
                <button
                    @click="previewMode = !previewMode"
                    :class="previewMode ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium hover:shadow-sm transition"
                >
                    <span x-text="previewMode ? '✏️ Edit' : '👁 Preview'"></span>
                </button>
                <button
                    @click="exportJSON()"
                    class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-medium hover:from-blue-700 hover:to-indigo-700 transition shadow-sm"
                >
                    Next →
                </button>
            </div>
        </div>

        {{-- Canvas scroll area --}}
        <div class="flex-1 overflow-y-auto p-5">

            {{-- PREVIEW MODE --}}
            <div x-show="previewMode" x-cloak class="max-w-xl mx-auto">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-5 text-base">Form Preview</h3>
                    <template x-if="fields.length === 0">
                        <p class="text-gray-400 text-sm text-center py-8">No fields to preview.</p>
                    </template>
                    <template x-for="field in fields" :key="field.id">
                        <div class="mb-4">
                            <x-fields.preview />
                        </div>
                    </template>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Submit
                    </button>
                </div>
            </div>

            {{-- EDIT MODE --}}
            <div x-show="!previewMode">
                <div
                    id="canvas-drop"
                    @dragover.prevent="onCanvasDragOver($event)"
                    @dragleave="onCanvasDragLeave($event)"
                    @drop.prevent="onCanvasDrop($event)"
                    :class="{ 'canvas-dragover': isDraggingOver }"
                    class="min-h-96 rounded-xl border-2 border-dashed border-gray-300 bg-white p-4 transition-all duration-200"
                >
                    {{-- Empty state --}}
                    <template x-if="fields.length === 0">
                        <div class="flex flex-col items-center justify-center h-80 text-gray-500 select-none pointer-events-none rounded-2xl border border-dashed border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 text-center shadow-inner">
                            <div class="text-5xl mb-3">✨</div>
                            <p class="text-sm font-semibold text-gray-700">Welcome to your form studio</p>
                            <p class="text-xs mt-1 text-gray-500">Pick a field from the left panel and drop it here to start building.</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2 text-[11px]">
                                <span class="rounded-full bg-white px-2.5 py-1 shadow-sm">Help & Contact</span>
                                <span class="rounded-full bg-white px-2.5 py-1 shadow-sm">Live Preview</span>
                                <span class="rounded-full bg-white px-2.5 py-1 shadow-sm">Export JSON</span>
                            </div>
                        </div>
                    </template>

                    {{-- Sortable list --}}
                    <div id="sortable-canvas" class="space-y-2">
                        <template x-for="(field, index) in fields" :key="field.id">
                            <div :data-id="field.id">

                                {{-- Delete confirmation inline --}}
                                <template x-if="pendingDeleteId === field.id">
                                    <div class="flex items-center justify-between px-4 py-3 bg-red-50 border border-red-200 rounded-xl">
                                        <span class="text-sm text-red-700">Delete "<span x-text="field.label"></span>"?</span>
                                        <div class="flex gap-2">
                                            <button @click="confirmDelete(field.id)"
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                            <button @click="pendingDeleteId = null"
                                                class="px-3 py-1 border border-gray-300 text-xs rounded-lg hover:bg-gray-100 transition">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- Field card --}}
                                <template x-if="pendingDeleteId !== field.id">
                                    <div
                                        :class="selectedFieldId === field.id
                                            ? 'border-blue-400 ring-2 ring-blue-100'
                                            : 'border-gray-200 hover:border-gray-300'"
                                        class="flex items-center gap-3 px-4 py-3 bg-white rounded-xl border transition-all duration-150 group cursor-default"
                                    >
                                        {{-- Drag handle --}}
                                        <span class="text-gray-300 hover:text-gray-500 cursor-grab text-lg drag-handle select-none" title="Drag to reorder">⠿</span>

                                        {{-- Field info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-800 text-sm truncate">
                                                <span x-text="field.label"></span>
                                                <span x-show="field.required" class="text-red-500 ml-0.5">*</span>
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                <span x-text="getTypeLabel(field.type)"></span>
                                                <span x-show="field.cssClass" x-text="' · .' + field.cssClass" class="text-blue-400"></span>
                                            </p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="selectField(field.id)" title="Edit"
                                                class="p-1.5 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-400 transition text-base">✏️</button>
                                            <button @click="duplicateField(field.id)" title="Duplicate"
                                                class="p-1.5 rounded-lg hover:bg-green-50 hover:text-green-600 text-gray-400 transition text-base">⧉</button>
                                            <button @click="askDelete(field.id)" title="Delete"
                                                class="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-gray-400 transition text-base">🗑</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ============================================================
         RIGHT PANEL — Field Options
    ============================================================ --}}
    <aside class="w-72 bg-white/80 backdrop-blur-xl border-l border-gray-200/80 flex flex-col flex-shrink-0 overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-sm tracking-tight">Field Options</h2>
            <div class="mt-2 rounded-xl border border-purple-100 bg-purple-50/70 p-2.5">
                <p class="text-[11px] font-semibold text-purple-700">Contact Support</p>
                <p class="text-[11px] text-gray-600">Need a quick hand? Adjust your field settings here.</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4">

            {{-- No selection state --}}
            <template x-if="!selectedField">
                <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <div class="text-3xl mb-2">☞</div>
                    <p class="text-xs text-center">Click the edit icon<br>on any field to configure it</p>
                </div>
            </template>

            {{-- Options form --}}
            <template x-if="selectedField">
                <div class="space-y-4">

                    {{-- Type badge --}}
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100"
                            x-text="getTypeLabel(selectedField.type)"></span>
                        <span x-show="selectedField.required"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-100">
                            Required
                        </span>
                    </div>

                    {{-- Label --}}
                    <x-fields.option-input label="Label" x-model="selectedField.label" @input="syncField()" />

                    {{-- Placeholder (text, number, email, phone, textarea) --}}
                    <template x-if="['text','number','email','phone','textarea'].includes(selectedField.type)">
                        <x-fields.option-input label="Placeholder" x-model="selectedField.placeholder" @input="syncField()" />
                    </template>

                    {{-- Min / Max chars (text, textarea) --}}
                    <template x-if="['text','textarea'].includes(selectedField.type)">
                        <div class="grid grid-cols-2 gap-2">
                            <x-fields.option-input label="Min chars" type="number" x-model="selectedField.minChars" @input="syncField()" />
                            <x-fields.option-input label="Max chars" type="number" x-model="selectedField.maxChars" @input="syncField()" />
                        </div>
                    </template>

                    {{-- Default value (text, number, email, hidden) --}}
                    <template x-if="['text','number','email','hidden'].includes(selectedField.type)">
                        <x-fields.option-input label="Default value" x-model="selectedField.defaultValue" @input="syncField()" />
                    </template>

                    {{-- CSS class --}}
                    <x-fields.option-input label="CSS Class" x-model="selectedField.cssClass" @input="syncField()" placeholder="e.g. full-width" />

                    {{-- Required toggle --}}
                    <div class="flex items-center justify-between py-1">
                        <label class="text-xs font-medium text-gray-600">Required</label>
                        <button
                            @click="selectedField.required = !selectedField.required; syncField()"
                            :class="selectedField.required ? 'bg-blue-600' : 'bg-gray-200'"
                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200"
                        >
                            <span
                                :class="selectedField.required ? 'translate-x-4' : 'translate-x-0.5'"
                                class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    {{-- Options list (dropdown, radio, checkbox) --}}
                    <template x-if="['dropdown','radio','checkbox'].includes(selectedField.type)">
                        <div>
                            <div class="border-t border-gray-100 pt-3 mt-1">
                                <p class="text-xs font-medium text-gray-500 mb-2">Options</p>
                                <div class="space-y-1.5">
                                    <template x-for="(opt, idx) in selectedField.options" :key="idx">
                                        <div class="flex gap-1.5 items-center">
                                            <input
                                                type="text"
                                                :value="opt"
                                                @input="updateOption(idx, $event.target.value)"
                                                class="flex-1 px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:border-blue-400 focus:bg-white transition"
                                            />
                                            <button @click="removeOption(idx)"
                                                class="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-gray-300 transition text-xs font-bold">✕</button>
                                        </div>
                                    </template>
                                </div>
                                <button @click="addOption()"
                                    class="mt-2 w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-dashed border-gray-300 text-xs text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                    + Add option
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Remove element --}}
                    <div class="border-t border-gray-100 pt-3 mt-2">
                        <button @click="askDelete(selectedField.id)"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-medium hover:bg-red-50 transition">
                            🗑 Remove field
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </aside>

    {{-- Toast --}}
    <div x-show="toast" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-4 py-2.5 rounded-lg shadow-lg z-50"
        x-text="toast">
    </div>

</div>
@endsection

@push('scripts')
<script>
function formBuilder() {
    return {
        fields: [],
        selectedFieldId: null,
        isDraggingOver: false,
        dragPaletteType: null,
        pendingDeleteId: null,
        previewMode: false,
        toast: null,
        toastTimer: null,
        history: [[]],
        historyIndex: 0,
        sortable: null,

        fieldTypes: @json($fieldTypes),

        get selectedField() {
            return this.fields.find(f => f.id === this.selectedFieldId) || null;
        },

        init() {
            this.$nextTick(() => {
                this.initSortable();
            });
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); this.undo(); }
                if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); this.redo(); }
            });
            // LocalStorage restore
            const saved = localStorage.getItem('form_builder_state');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    this.fields = parsed;
                    this.history = [JSON.parse(JSON.stringify(parsed))];
                    this.historyIndex = 0;
                    this.$nextTick(() => this.initSortable());
                } catch(e) {}
            }
        },

        initSortable() {
            const el = document.getElementById('sortable-canvas');
            if (!el) return;
            if (this.sortable) this.sortable.destroy();
            this.sortable = Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: (evt) => {
                    const moved = this.fields.splice(evt.oldIndex, 1)[0];
                    this.fields.splice(evt.newIndex, 0, moved);
                    this.saveHistory();
                    this.$nextTick(() => this.initSortable());
                }
            });
        },

        // ── Palette drag ───────────────────────────────────────────
        onPaletteDragStart(e, type) {
            this.dragPaletteType = type;
            e.dataTransfer.effectAllowed = 'copy';
        },

        onCanvasDragOver(e) {
            if (this.dragPaletteType) this.isDraggingOver = true;
        },

        onCanvasDragLeave(e) {
            if (!e.currentTarget.contains(e.relatedTarget)) {
                this.isDraggingOver = false;
            }
        },

        onCanvasDrop(e) {
            this.isDraggingOver = false;
            if (this.dragPaletteType) {
                const newField = this.createField(this.dragPaletteType);
                this.fields.push(newField);
                this.selectedFieldId = newField.id;
                this.dragPaletteType = null;
                this.saveHistory();
                this.$nextTick(() => this.initSortable());
            }
        },

        // ── Field CRUD ─────────────────────────────────────────────
        createField(type) {
            return {
                id: Math.random().toString(36).slice(2, 9),
                type,
                label: this.getTypeLabel(type),
                placeholder: '',
                required: false,
                cssClass: '',
                defaultValue: '',
                minChars: '',
                maxChars: '',
                options: ['dropdown','radio','checkbox'].includes(type) ? ['Option 1','Option 2','Option 3'] : [],
            };
        },

        selectField(id) {
            this.selectedFieldId = id;
        },

        syncField() {
            this.saveHistory();
            this.$nextTick(() => this.initSortable());
        },

        duplicateField(id) {
            const idx = this.fields.findIndex(f => f.id === id);
            if (idx < 0) return;
            const copy = JSON.parse(JSON.stringify(this.fields[idx]));
            copy.id = Math.random().toString(36).slice(2, 9);
            copy.label += ' (copy)';
            this.fields.splice(idx + 1, 0, copy);
            this.selectedFieldId = copy.id;
            this.saveHistory();
            this.$nextTick(() => this.initSortable());
            this.showToast('Field duplicated');
        },

        askDelete(id) {
            this.pendingDeleteId = id;
        },

        confirmDelete(id) {
            const f = this.fields.find(x => x.id === id);
            const label = f ? f.label : 'Field';
            this.fields = this.fields.filter(x => x.id !== id);
            if (this.selectedFieldId === id) this.selectedFieldId = null;
            this.pendingDeleteId = null;
            this.saveHistory();
            this.$nextTick(() => this.initSortable());
            this.showToast(`"${label}" removed`);
        },

        // ── Options list ────────────────────────────────────────────
        addOption() {
            if (this.selectedField) {
                this.selectedField.options.push('New option');
                this.saveHistory();
            }
        },

        removeOption(idx) {
            if (this.selectedField) {
                this.selectedField.options.splice(idx, 1);
                this.saveHistory();
            }
        },

        updateOption(idx, val) {
            if (this.selectedField) {
                this.selectedField.options[idx] = val;
                this.saveHistory();
            }
        },

        // ── History ─────────────────────────────────────────────────
        saveHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(JSON.parse(JSON.stringify(this.fields)));
            this.historyIndex = this.history.length - 1;
            localStorage.setItem('form_builder_state', JSON.stringify(this.fields));
        },

        undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.fields = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
                if (this.selectedFieldId && !this.fields.find(f => f.id === this.selectedFieldId)) {
                    this.selectedFieldId = null;
                }
                this.$nextTick(() => this.initSortable());
            }
        },

        redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                this.fields = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
                this.$nextTick(() => this.initSortable());
            }
        },

        // ── Export ──────────────────────────────────────────────────
        exportJSON() {
            const output = {
                form: {
                    fields: this.fields.map(f => {
                        const out = { id: f.id, type: f.type, label: f.label, required: f.required };
                        if (f.placeholder) out.placeholder = f.placeholder;
                        if (f.cssClass)    out.css_class   = f.cssClass;
                        if (f.defaultValue) out.default    = f.defaultValue;
                        if (f.minChars)    out.min_chars   = parseInt(f.minChars);
                        if (f.maxChars)    out.max_chars   = parseInt(f.maxChars);
                        if (['dropdown','radio','checkbox'].includes(f.type)) out.options = f.options;
                        return out;
                    })
                }
            };
            const blob = new Blob([JSON.stringify(output, null, 2)], { type: 'application/json' });
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
            a.download = 'form-schema.json'; a.click();
            this.showToast('JSON exported!');
        },

        // ── Helpers ─────────────────────────────────────────────────
        getTypeLabel(type) {
            const map = {
                text: 'Text Input', number: 'Number', email: 'Email', phone: 'Phone',
                textarea: 'Text Area', dropdown: 'Dropdown', radio: 'Radio Buttons',
                checkbox: 'Checkboxes', date: 'Date', hidden: 'Hidden Field',
            };
            return map[type] || type;
        },

        showToast(msg) {
            this.toast = msg;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => { this.toast = null; }, 2500);
        },
    };
}
</script>
@endpush
