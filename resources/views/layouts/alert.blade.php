@if(session('text'))
    @push('scripts')
        <script>
            Swal.fire({
                title: '{{ session('title') }}',
                html: `{!! session('text') !!}`,
                icon: '{{ session('type') }}'
            })
        </script>
    @endpush
@endif
