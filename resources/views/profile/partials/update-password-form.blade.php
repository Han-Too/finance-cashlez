<section>
    <div class="card card-flush mb-6 mb-xl-9">
        <div class="card-header mt-6">
            <div class="card-title flex-column">
                <h2 class="mb-1">
                    {{ __('Update Password') }}
                </h2>
                <div class="fs-6 fw-bold text-muted">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </div>
            </div>
        </div>
        <div class="card-body p-9 pt-4">
            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')

                <div class="mb-10 fv-row">
                    <x-input-label for="current_password" :value="__('Current Password')" class="required form-label" />
                    <x-text-input id="current_password" name="current_password" type="password"
                        class="form-control mb-2" autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="invalid-feedback" />
                </div>

                <div class="mb-10 fv-row">
                    <x-input-label for="password" :value="__('New Password')" class="required form-label" />
                    <x-text-input id="password" name="password" type="password" class="form-control mb-2"
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="invalid-feedback" />
                </div>

                <div class="mb-10 fv-row">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="required form-label" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                        class="form-control mb-2" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="invalid-feedback" />
                </div>

                <x-primary-button class="btn btn-primary">
                    <span class="indicator-label">Save Changes</span>
                </x-primary-button>
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                            class="fs-4 text-gray-600">{{ __('Saved.') }}</p>
                    </div>
                @endif
            </form>
        </div>
    </div>

</section>
