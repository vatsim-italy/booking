@php
    // Generalized field-level validation error.
    // Usage: @include('partials.field-error', ['name' => 'email'])
    // Optional: ['name' => 'email', 'bag' => 'myBag', 'block' => false, 'class' => 'extra-class']
    // - $name  (string, required, or $field alias): field name for @error.
    // - $bag   (string|null): named error bag, defaults to default bag.
    // - $block (bool): force `d-block` so the message is visible for
    //   custom controls, checkboxes/radios, input-groups and file inputs
    //   where Bootstrap 4 hides plain `.invalid-feedback`. Defaults to true
    //   so callers don't have to think about it; pass false to get strict
    //   Bootstrap markup (`is-invalid` sibling selector only).
    // - $class (string): extra classes appended to the feedback div.
    $fieldName = $name ?? $field ?? null;
    $errorBag = $bag ?? null;
    $forceBlock = $block ?? true;
    $extraClass = $class ?? '';
    $fieldMessage = null;
    if (!empty($fieldName) && isset($errors)) {
        try {
            $bagInstance = !empty($errorBag) ? $errors->getBag($errorBag) : $errors;
            if ($bagInstance && $bagInstance->has($fieldName)) {
                $fieldMessage = $bagInstance->first($fieldName);
            }
        } catch (\Throwable $e) {
            $fieldMessage = null;
        }
    }
@endphp

@if (!empty($fieldMessage))
    <div class="invalid-feedback{{ !empty($forceBlock) ? ' d-block' : '' }}{{ !empty($extraClass) ? ' '.$extraClass : '' }}" role="alert">{{ $fieldMessage }}</div>
@endif
