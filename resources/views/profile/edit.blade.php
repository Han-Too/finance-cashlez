<x-app-layout>
    <div class="container">

        <!--begin::Search-->
        {{-- <nav> --}}
        <div class="py-3 py-lg-0 py-xl-0"></div>
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8 row">
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4 active" id="nav-edit-tab" data-bs-toggle="tab"
                    href="#nav-edit">Edit Profile</a>
            </li>
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4" id="nav-password-tab" data-bs-toggle="tab"
                    href="#nav-password">Change Password</a>
            </li>
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4" id="nav-delete-tab" data-bs-toggle="tab"
                    href="#nav-delete">Delete Account</a>
            </li>
        </ul>
        {{-- </nav> --}}
        <!--end::Search-->
        {{-- <div class="card card-flush px-10 py-6 rounded-sm">
            <div class="card-title">
            </div> --}}
        {{-- <div class="d-flex flex-wrap justify-content-between">
                <!--begin::Stats-->
                <div class="d-flex flex-wrap"> --}}
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="nav-edit" role="tabpanel" aria-labelledby="nav-edit-tab"
                tabindex="0">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="tab-pane fade show" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab"
                tabindex="0">
                @include('profile.partials.update-password-form')
            </div>
            <div class="tab-pane fade show" id="nav-delete" role="tabpanel" aria-labelledby="nav-delete-tab"
                tabindex="0">
                @include('profile.partials.delete-user-form')
            </div>
            {{-- </div>
                </div> --}}
            {{-- <x-slot name="header">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Profile') }}
                    </h2>
                </x-slot>

                <div class="py-12">
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div> --}}
            {{-- </div>
        </div> --}}
        </div>
</x-app-layout>
