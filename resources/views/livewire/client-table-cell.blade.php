<div x-data="{
        editing: false,
        value: {{ json_encode($value) }},
        original: {{ json_encode($value) }},
        isChanged: false,
        save() {
            if(this.value !== this.original){
                this.isChanged = false;
                @this.updateInline({{ $row->id }}, {{ json_encode($field) }}, this.value)
                this.original = this.value;
            }
            this.editing = false;
        },
        cancel() {
            this.value = this.original;
            this.isChanged = false;
            this.editing = false;
        }
    }"
    x-init="$watch('value', v => isChanged = (v !== original))"
    class="relative transition-all duration-300"
>
    <template x-if="!editing">
        <div @click="editing=true" 
             x-bind:class="isChanged ? 'bg-yellow-100' : ''"
             class="px-2 py-1 cursor-pointer hover:bg-gray-100 rounded truncate-cell"
             :title="value">
            <span x-text="value"></span>
        </div>
    </template>

    <template x-if="editing">
        <input type="text"
            x-model="value"
            @keydown.enter="save()"
            @keydown.escape="cancel()"
            @blur="cancel()"
            x-bind:class="isChanged ? 'bg-yellow-100' : 'bg-white'"
            class="border border-blue-300 rounded px-2 py-1 w-full focus:outline-none focus:ring focus:ring-blue-200 transition-colors duration-300"
            autofocus
        />
    </template>
</div>
