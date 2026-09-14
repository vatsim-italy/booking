@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    @push('scripts')
        <script>
            $('.send-final-email').on('click', function(e) {
                e.preventDefault();
                if ($('#testmode1').prop('checked')) {
                    Swal.fire('Sending test Email...');
                    Swal.showLoading();
                    var url = '{{ route('admin.events.email.final', $event) }}';
                    axios.post(url, {
                            'testmode': 1,
                            '_method': 'PATCH',
                        })
                        .then(function(response) {
                            Swal.fire(response.data.success);
                        });
                } else {
                    Swal.fire({
                        title: 'Are you sure',
                        text: 'Are you sure you want to send the Final Information Email?',
                        icon: 'warning',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.value) {
                            Swal.fire('Sending Final Information Email...');
                            Swal.showLoading();
                            $(this).closest('form').submit();
                        }
                    });
                }
            });

            $('.send-email').on('click', function(e) {
                e.preventDefault();
                if ($('#testmode2').prop('checked')) {
                    Swal.fire('Sending test Email...');
                    Swal.showLoading();
                    var url = '{{ route('admin.events.email', $event) }}';
                    axios.post(url, {
                            'subject': $('#subject').val(),
                            'message': tinymce.activeEditor.getContent(),
                            'testmode': 1,
                            '_method': 'PATCH',
                        })
                        .then(function(response) {
                            console.log(response);
                            Swal.fire(response.data.success);
                        });
                } else {
                    Swal.fire({
                        title: 'Are you sure',
                        text: 'Are you sure you want to send a Email?',
                        icon: 'warning',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.value) {
                            Swal.fire('Sending Email...');
                            Swal.showLoading();
                            $(this).closest('form').submit();
                        }
                    });
                }

            });
        </script>
    @endpush
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | {{ __('Send Bulk E-mail') }}</div>

                <div class="card-body">

                    <form action="{{ route('admin.events.email.final', $event) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-checkbox mr-3">
                                    <input type="checkbox" name="testmode1" id="testmode1" value="1" class="custom-control-input @error('testmode1') is-invalid @enderror">
                                    <label class="custom-control-label" for="testmode1">{{ __('Test mode') }}</label>
                                    <small class="form-text text-muted">
                                        {{ __('Send a random Final Information E-mail to yourself') }}
                                    </small>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="forceSend" id="forceSend" value="1" class="custom-control-input @error('forceSend') is-invalid @enderror">
                                    <label class="custom-control-label" for="forceSend">{{ __('Send to everybody') }}</label>
                                    <small class="form-text text-muted">
                                        {{ __('Send to all particpants, even though they already received it (and no edit was made)') }}
                                    </small>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'testmode1'])
                            @include('partials.field-error', ['name' => 'forceSend'])
                        </div>
                        <button type="submit" class="btn btn-primary send-final-email">
                            <i class="fa fa-envelope"></i> {!! __('Send <strong>Final Information</strong> E-mail') !!}
                        </button>
                    </form>

                    <hr>

                    <form action="{{ route('admin.events.email', $event) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        {{-- Subject --}}
                        <div class="form-group">
                            <label for="subject">{{ __('Subject') }}</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" required>
                            @include('partials.field-error', ['name' => 'subject'])
                        </div>

                        {{-- Message --}}
                        <div class="form-group">
                            <label for="message">{{ __('Message') }}</label>
                            <textarea name="message" id="message" class="form-control tinymce @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('Salutation and closing are already included') }}
                            </small>
                            @include('partials.field-error', ['name' => 'message'])
                        </div>

                        {{-- Test mode --}}
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="testmode" id="testmode2" value="1" class="custom-control-input @error('testmode') is-invalid @enderror">
                                <label class="custom-control-label" for="testmode2">{{ __('Test mode') }}</label>
                                <small class="form-text text-muted">
                                    {{ __('Send a E-mail to yourself') }}
                                </small>
                            </div>
                            @include('partials.field-error', ['name' => 'testmode'])
                        </div>

                        <button type="submit" class="btn btn-primary send-email">
                            <i class="fa fa-envelope"></i> {{ __('Send E-mail') }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

