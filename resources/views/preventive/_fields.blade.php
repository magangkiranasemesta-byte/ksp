{{-- Field form jadwal PM. Variabel: $equipments, $engineers, $preventive (opsional) --}}
@php
    $v = fn ($key, $default = null) => old($key, isset($preventive) ? ($preventive->{$key} instanceof \Carbon\CarbonInterface ? $preventive->{$key}->toDateString() : $preventive->{$key}) : $default);
    $input = 'w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500';
    $label = 'block text-xs font-semibold text-slate-600 mb-1.5';
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="{{ $label }}" for="pm_equipment_id">Equipment <span class="text-rose-500">*</span></label>
        <select id="pm_equipment_id" name="equipment_id" required class="{{ $input }}">
            <option value="">-- Pilih equipment --</option>
            @foreach($equipments as $eq)
                <option value="{{ $eq->id }}" @selected($v('equipment_id') == $eq->id)>{{ $eq->name }} ({{ $eq->equipment_code }})</option>
            @endforeach
        </select>
        @error('equipment_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $label }}" for="pm_title">Judul pekerjaan <span class="text-rose-500">*</span></label>
        <input id="pm_title" name="title" type="text" required maxlength="255" value="{{ $v('title') }}" class="{{ $input }}" placeholder="mis. Penggantian oli & pengecekan bearing">
        @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $label }}" for="pm_frequency">Frekuensi <span class="text-rose-500">*</span></label>
        <select id="pm_frequency" name="frequency" required class="{{ $input }}">
            @foreach(\App\Models\PreventiveMaintenance::FREQUENCIES as $key => $text)
                <option value="{{ $key }}" @selected($v('frequency', 'monthly') === $key)>{{ $text }}</option>
            @endforeach
        </select>
        @error('frequency')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $label }}" for="pm_assigned_to">Technician</label>
        <select id="pm_assigned_to" name="assigned_to" class="{{ $input }}">
            <option value="">-- Belum ditentukan --</option>
            @foreach($engineers as $eng)
                <option value="{{ $eng->id }}" @selected($v('assigned_to') == $eng->id)>{{ $eng->username }}</option>
            @endforeach
        </select>
        @error('assigned_to')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $label }}" for="pm_last">Maintenance terakhir</label>
        <input id="pm_last" name="last_maintenance_date" type="date" max="{{ now()->toDateString() }}" value="{{ $v('last_maintenance_date') }}" class="{{ $input }}">
        @error('last_maintenance_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $label }}" for="pm_next">Maintenance berikutnya <span class="text-rose-500">*</span></label>
        <input id="pm_next" name="next_maintenance_date" type="date" required value="{{ $v('next_maintenance_date') }}" class="{{ $input }}">
        @error('next_maintenance_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $label }}" for="pm_notes">Catatan / checklist</label>
        <textarea id="pm_notes" name="notes" rows="3" maxlength="2000" class="{{ $input }}">{{ $v('notes') }}</textarea>
        @error('notes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>
</div>
