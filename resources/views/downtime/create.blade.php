<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mulai Downtime - Maintenance X</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            width: 95%;
            max-width: 900px;
            margin: 40px auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.07);
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .required {
            color: #dc2626;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: white;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 13px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error-list {
            margin: 0;
            padding-left: 20px;
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 14px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media (max-width: 600px) {

            .container {
                margin: 20px auto;
            }

            .card {
                padding: 20px;
            }

            .button-container {
                flex-direction: column;
            }

            .button-container .btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <div class="card">


        {{-- HEADER --}}

        <div class="header">

            <h1>
                Mulai Downtime Equipment
            </h1>

            <p>
                Catat ketika equipment mulai tidak dapat digunakan.
            </p>

        </div>


        {{-- ERROR GLOBAL --}}

        @if($errors->any())

            <div class="alert alert-error">

                <ul class="error-list">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- INFO --}}

        <div class="info-box">

            <strong>Informasi:</strong>

            <br>

            Setelah downtime dibuat, status akan menjadi
            <strong>ONGOING</strong>.

            <br>

            Setelah equipment selesai diperbaiki,
            gunakan tombol <strong>Selesaikan</strong>
            pada halaman Equipment Downtime.

        </div>


        {{-- FORM --}}

        <form
            action="{{ route('downtime.store') }}"
            method="POST"
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
                    required
                >

                    <option value="">
                        -- Pilih Equipment --
                    </option>


                    @foreach($equipment as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old('equipment_id') == $item->id ? 'selected' : '' }}
                        >

                            {{ $item->name }}

                        </option>

                    @endforeach

                </select>


                @error('equipment_id')

                    <div class="error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- START TIME --}}

            <div class="form-group">

                <label for="started_at">

                    Waktu Mulai Downtime

                    <span class="required">*</span>

                </label>


                <input
                    type="datetime-local"
                    name="started_at"
                    id="started_at"
                    value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}"
                    required
                >


                @error('started_at')

                    <div class="error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- REASON --}}

            <div class="form-group">

                <label for="reason">

                    Alasan Downtime

                    <span class="required">*</span>

                </label>


                <input
                    type="text"
                    name="reason"
                    id="reason"
                    value="{{ old('reason') }}"
                    placeholder="Contoh: Kerusakan motor"
                    maxlength="255"
                    required
                >


                @error('reason')

                    <div class="error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- DESCRIPTION --}}

            <div class="form-group">

                <label for="description">

                    Deskripsi

                </label>


                <textarea
                    name="description"
                    id="description"
                    placeholder="Jelaskan detail kerusakan atau penyebab downtime..."
                >{{ old('description') }}</textarea>


                @error('description')

                    <div class="error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- BUTTON --}}

            <div class="button-container">

                <a
                    href="{{ route('downtime.index') }}"
                    class="btn btn-secondary"
                >
                    Batal
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Mulai Downtime
                </button>

            </div>


        </form>


    </div>


</div>


</body>

</html>