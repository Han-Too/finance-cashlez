<section>
    <div class="card card-flush mb-6 mb-xl-9">
        <!--begin::Card header-->
        <div class="card-header mt-6">
            <div class="card-title flex-column">
                <h2 class="mb-1">
                    {{ __('Profile Information') }}
                </h2>
                <div class="fs-6 fw-bold text-muted">
                    {{ __("Update your account's profile information and email address.") }}
                </div>
            </div>
        </div>
        <div class="card-body p-9 pt-4">
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('patch')
                <div class="mb-10 fv-row">
                    <!--begin::Label-->
                    <x-input-label for="name" :value="__('Name')" class="required form-label" />
                    <!--end::Label-->
                    <!--begin::Input-->
                    <x-text-input id="name" name="name" type="text" class="form-control mb-2"
                        :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" class="invalid-feedback"/>
                    <!--end::Input-->
                </div>
                <div class="mb-10 fv-row">
                    <!--begin::Label-->
                    <x-input-label for="email" :value="__('Email')" class="required form-label" />
                    <!--end::Label-->
                    <!--begin::Input-->
                    <x-text-input id="email" name="email" type="email" class="form-control mb-2"
                        :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" class="invalid-feedback"/>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-gray-800">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                    <!--end::Input-->
                </div>


                <x-primary-button class="btn btn-primary">
                    <span class="indicator-label">Save Changes</span>
                </x-primary-button>

                @if (session('status') === 'profile-updated')
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
