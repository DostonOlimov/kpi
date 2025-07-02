<!-- resources/views/components/form/input.blade.php -->
<div class="col-md-6">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name) }}"
           class="form-control @error($name) is-invalid @enderror" {{ $attributes }} autocomplete="off">

    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
