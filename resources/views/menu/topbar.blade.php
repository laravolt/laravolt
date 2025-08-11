<header
  class="lg:ms-65 fixed top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-white border-b border-gray-200 dark:bg-neutral-800 dark:border-neutral-700"
>
  <div
    class="flex justify-between xl:grid xl:grid-cols-3 basis-full items-center w-full py-2.5 px-2 sm:px-5"
  >
    <div class="xl:col-span-1 flex items-center md:gap-x-3">
      <div class="lg:hidden space-x-4">
        <!-- Sidebar Toggle -->
        <button
          type="button"
          class="w-7 h-9.5 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
          aria-haspopup="dialog"
          aria-expanded="false"
          aria-controls="hs-pro-sidebar"
          aria-label="Toggle navigation"
          data-hs-overlay="#hs-pro-sidebar"
        >
          <svg
            class="shrink-0 size-4"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M17 8L21 12L17 16M3 12H13M3 6H13M3 18H13"></path>
          </svg>
        </button>
        <!-- End Sidebar Toggle -->

        @include('laravolt::menu.partials.breadcrumb')
      </div>

      <div class="hidden lg:block min-w-80 xl:w-full">
        <!-- Search Input -->

        <!-- TODO: End Search Input -->

        @include('laravolt::menu.partials.breadcrumb')
      </div>
    </div>

    <div class="xl:col-span-2 flex justify-end items-center gap-x-2">
      <div class="flex items-center">
        <div class="lg:hidden">
          <!-- Search Button Icon -->

          <!-- TODO: End Search Button Icon -->
        </div>

        <!-- Help Dropdown -->

        <!-- TODO: End Help Dropdown -->

        <!-- Notifications Button Icon -->

        <!-- TODO: End Notifications Dropdown -->

        <!-- Activity Button Icon -->

        <!-- TODO: End Activity Button Icon -->
      </div>

      @auth
        @php
          $user = auth()->user();
        @endphp
        <div class="h-9.5">
          <!-- Account Dropdown -->
          <div
            class="hs-dropdown inline-flex [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative text-start"
          >
            <button
              id="hs-dnad"
              type="button"
              class="inline-flex shrink-0 items-center gap-x-3 text-start rounded-full focus:outline-hidden"
              aria-haspopup="menu"
              aria-expanded="true"
              aria-label="Dropdown"
            >
              <img
                class="shrink-0 size-9.5 rounded-full"
                src="{{ $user->avatar }}"
                alt="Avatar"
              />
            </button>

            <!-- Account Dropdown -->
            <div
              class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 z-20 bg-white rounded-xl shadow-xl dark:bg-neutral-900 block"
              role="menu"
              aria-orientation="vertical"
              aria-labelledby="hs-dnad"
              data-placement="bottom-end"
              style="
                position: absolute;
                transform: translate3d(-202px, 48px, 0px);
                margin: 0px;
              "
            >
              <div class="border-gray-200 dark:border-neutral-800 p-1">
                <a
                  class="py-2 px-3 flex items-center gap-x-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                  href="#"
                >
                  <img
                    class="shrink-0 size-8 rounded-full"
                    src="{{ $user->avatar }}"
                    alt="Avatar"
                  />

                  <div class="grow">
                    <span
                      class="text-sm font-semibold text-gray-800 dark:text-neutral-300"
                    >
                      {{ $user->name }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-neutral-500">
                      {{ $user->email }}
                    </p>
                  </div>
                </a>
              </div>

              <div
                class="border-gray-200 border-y dark:border-neutral-800 px-4 py-3.5"
              >
                <!-- Switch/Toggle -->
                <div class="flex flex-wrap justify-between items-center gap-2">
                  <label
                    for="hs-pro-dnaddm"
                    class="flex-1 cursor-pointer text-sm text-gray-800 dark:text-neutral-300"
                  >
                    Dark mode
                  </label>
                  <label
                    for="hs-pro-dnaddm"
                    class="relative inline-block w-11 h-6 cursor-pointer"
                  >
                    <input
                      data-hs-theme-switch=""
                      type="checkbox"
                      id="hs-pro-dnaddm"
                      class="peer sr-only"
                    />
                    <span
                      class="absolute inset-0 bg-gray-200 rounded-full transition-colors duration-200 ease-in-out peer-checked:bg-blue-600 dark:bg-neutral-700 dark:peer-checked:bg-blue-500 peer-disabled:opacity-50 peer-disabled:pointer-events-none"
                    ></span>
                    <span
                      class="absolute top-1/2 start-0.5 -translate-y-1/2 size-5 bg-white rounded-full shadow-sm !transition-transform duration-200 ease-in-out peer-checked:translate-x-full dark:bg-neutral-400 dark:peer-checked:bg-white"
                    ></span>
                  </label>
                </div>
                <!-- End Switch/Toggle -->
              </div>
              <div class="p-1">
                <a
                  class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                  href="{{ route('my::profile.edit') }}"
                >
                  @lang('Edit Profil')
                </a>
                <a
                  class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                  href="{{ route('my::password.edit') }}"
                >
                  @lang('Edit Password')
                </a>
                <a
                  class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                  href="{{ route('auth::logout') }}"
                >
                  Logout
                </a>
              </div>
            </div>
            <!-- End Account Dropdown -->
          </div>
          <!-- End Account Dropdown -->
        </div>
      @endauth
    </div>
  </div>
</header>
