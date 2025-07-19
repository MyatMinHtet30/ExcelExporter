@props([
  'type' => 'text',
  'name',
  'id' => null,
  'value' => '',
  'placeholder' => '',
  'required' => false,
])

<div class="input-icon-wrapper">
  <input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    class="form-control"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    autocomplete="off"
    @if($required) required @endif
  />
  <img src="{{ asset('assets/icons/mic.svg') }}" alt="mic" class="speech-icon" />
</div>
