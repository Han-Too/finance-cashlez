<section class="space-y-6">
    <div class="card card-flush mb-6 mb-xl-9">
        <!--begin::Card header-->
        <div class="card-header mt-6">
            <div class="card-title flex-column">
                <h2 class="mb-1">
                    {{ __('Delete Account') }}
                </h2>
                <div class="fs-6 fw-bold text-muted">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </div>
            </div>
        </div>
        <div class="card-body p-9 pt-4">

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modaldelete">
                Delete Account
            </button>

            <!-- Modal -->
            <div class="modal fade" id="modaldelete" tabindex="-1" aria-labelledby="modaldeleteLabel"
                aria-hidden="true" name="confirm-user-deletion">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                {{ __('Are you sure you want to delete your account?') }}
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('profile.destroy') }}" class="p-1">
                                @csrf
                                @method('delete')

                                <p class="mt-1 fs-5 text-gray-600">
                                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted') }}
                                </p>
                                <p class="mt-1 fs-5 text-gray-600">
                                    {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
                                </p>
                                <div class="mb-10 fv-row">
                                    <x-input-label for="password" value="{{ __('Password') }}"
                                        class="required form-label" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="form-control mb-2" placeholder="{{ __('Password') }}" />
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="invalid-feedback" />
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Delete Account
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="mt-6">
                        <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4"
                            placeholder="{{ __('Password') }}" />

                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button class="ml-3">
                            {{ __('Delete Account') }}
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>


</section>
