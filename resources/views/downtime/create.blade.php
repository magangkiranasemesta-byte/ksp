@extends('layouts.app')

@section('title', 'Create Downtime Equipment')

@section('content')

<div class="downtime-page">

{{-- HEADER --}}
<div class="downtime-header">

    <div class="downtime-header-left">

        <a href="{{ route('downtime.index') }}" class="btn-back">
            <span class="back-icon">←</span>
            <span>Back</span>
        </a>

        <div>
            <h1>Create Downtime</h1>

            <p>
                Record a new equipment downtime.
            </p>
        </div>

    </div>

</div>


{{-- FORM CARD --}}
<div class="downtime-form-card">

    <div class="downtime-form-header">

        <div>
            <h2>Downtime Information</h2>

            <p>
                Fill in the information below to create
                a new downtime record.
            </p>
        </div>

    </div>


    <form
        action="{{ route('downtime.store') }}"
        method="POST"
        id="downtimeForm"
        class="downtime-form"
    >

        @csrf


        {{-- EQUIPMENT --}}
        <div class="form-group">

            <label for="equipment_id">
                Equipment
                <span class="required">*</span>
            </label>

            <select
                name="equipment_id"
                id="equipment_id"
                class="form-control"
                required
            >

                <option value="">
                    Select Equipment
                </option>

                @foreach($equipments as $equipment)

                    <option
                        value="{{ $equipment->id }}"
                        {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}
                    >
                        {{ $equipment->equipment_code }}
                        -
                        {{ $equipment->name }}
                    </option>

                @endforeach

            </select>

            @error('equipment_id')
                <span class="form-error">
                    {{ $message }}
                </span>
            @enderror

        </div>


        {{-- DATE & TIME --}}
        <div class="form-row">

            <div class="form-group">

                <label for="start_time">
                    Start Downtime
                    <span class="required">*</span>
                </label>

                <input
                    type="datetime-local"
                    name="start_time"
                    id="start_time"
                    class="form-control"
                    value="{{ old('start_time') }}"
                    required
                >

                @error('start_time')
                    <span class="form-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            <div class="form-group">

                <label for="end_time">
                    End Downtime
                </label>

                <input
                    type="datetime-local"
                    name="end_time"
                    id="end_time"
                    class="form-control"
                    value="{{ old('end_time') }}"
                >

                <small class="form-hint">
                    Leave empty if the equipment is still down.
                </small>

                @error('end_time')
                    <span class="form-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>

        </div>


        {{-- REASON --}}
        <div class="form-group">

            <label for="reason">
                Reason
                <span class="required">*</span>
            </label>

            <input
                type="text"
                name="reason"
                id="reason"
                class="form-control"
                value="{{ old('reason') }}"
                placeholder="Enter downtime reason"
                maxlength="255"
                required
            >

            @error('reason')
                <span class="form-error">
                    {{ $message }}
                </span>
            @enderror

        </div>


        {{-- DESCRIPTION --}}
        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                name="description"
                id="description"
                class="form-control form-textarea"
                rows="5"
                placeholder="Describe the downtime problem or additional information..."
            >{{ old('description') }}</textarea>

            @error('description')
                <span class="form-error">
                    {{ $message }}
                </span>
            @enderror

        </div>


        {{-- STATUS --}}
        <div class="form-group">

            <label for="status">
                Status
                <span class="required">*</span>
            </label>

            <select
                name="status"
                id="status"
                class="form-control"
                required
            >

                <option
                    value="ACTIVE"
                    {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="COMPLETED"
                    {{ old('status') === 'COMPLETED' ? 'selected' : '' }}
                >
                    Completed
                </option>

            </select>

            @error('status')
                <span class="form-error">
                    {{ $message }}
                </span>
            @enderror

        </div>


        {{-- FORM ACTION --}}
        <div class="downtime-form-actions">

            <a
                href="{{ route('downtime.index') }}"
                class="btn-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn-primary"
                id="submitDowntime"
            >
                Create Downtime
            </button>

        </div>

    </form>

</div>


</div>

@endsection

@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/downtime.css') }}"
>

@endpush

@push('scripts')

<script
    src="{{ asset('js/downtime.js') }}"
></script>

@endpush
