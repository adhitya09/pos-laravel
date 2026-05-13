<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">

    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
        'xl:justify-center' :
        'justify-start'">
        <a href="/">
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="dark:hidden" src="/images/logo/logo.svg" alt="Logo" width="700" height="50" />
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="hidden dark:block" src="/images/logo/logo-dark.svg" alt="Logo" width="200"
                height="50" />
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                <!-- MENU -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>MENU</span>
                        </template>
                        <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill="currentColor"/>
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        @perm('dashboard.viewAny')
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('dashboard') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Dashboard</span>
                            </a>
                        </li>
                        @endperm
                        @perm('pos.viewAny')
                        <li>
                            <a href="{{ route('pos.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('pos.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="2"/>
                                    <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Kasir</span>
                            </a>
                        </li>
                        @endperm
                    </ul>
                </div>

                <!-- Manajemen Produk -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Produk</span>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        @perm('kategori.viewAny')
                        <li>
                            <a href="{{ route('kategori.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('kategori.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 7V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V7M3 7H21M3 7V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 19.5304 21 19 21 19V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Kategori</span>
                            </a>
                        </li>
                        @endperm
                        @perm('produk.viewAny')
                        <li>
                            <a href="{{ route('produk.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('produk.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 7L12 13L4 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Produk</span>
                            </a>
                        </li>
                        @endperm
                        @perm('inventory.viewAny')
                        <li>
                            <a href="{{ route('inventory.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('inventory.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Inventory</span>
                            </a>
                        </li>
                        @endperm
                    </ul>
                </div>

                <!-- Keuangan -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Keuangan</span>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        @perm('transaksi.viewAny')
                        <li>
                            <a href="{{ route('transaksi.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('transaksi.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 1V23M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Transaksi</span>
                            </a>
                        </li>
                        @endperm
                        @perm('cash-flow.viewAny')
                        <li>
                            <a href="{{ route('cash-flow.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('cash-flow.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H21M3 6V18C3 18.5304 3.21071 19.0391 3.58579 19.4142C3.96086 19.7893 4.46957 20 5 20H19C19.5304 20 20.0391 19.7893 20.4142 19.4142C20.7893 19.0391 21 18.5304 21 18V6M3 6V4C3 3.46957 3.21071 2.96086 3.58579 2.58579C3.96086 2.21071 4.46957 2 5 2H19C19.5304 2 20.0391 2.21071 20.4142 2.58579C20.7893 2.96086 21 3.46957 21 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Alur Kas</span>
                            </a>
                        </li>
                        @endperm
                        @perm('payment-method.viewAny')
                        <li>
                            <a href="{{ route('payment-method.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('payment-method.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                    <line x1="2" y1="10" x2="22" y2="10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Metode Pembayaran</span>
                            </a>
                        </li>
                        @endperm
                        @perm('report.viewAny')
                        <li>
                            <a href="{{ route('report.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('report.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 19V13C9 11.8954 9.89543 11 11 11H18C19.1046 11 20 11.8954 20 13V19M9 19H20M9 19H5C3.89543 19 3 18.1046 3 17V7C3 5.89543 3.89543 5 5 5H9M9 19V5M9 5H16C17.1046 5 18 5.89543 18 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Laporan</span>
                            </a>
                        </li>
                        @endperm
                    </ul>
                </div>

                <!-- Pengaturan -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Pengaturan</span>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        @perm('user.viewAny')
                        <li>
                            <a href="{{ route('user.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('user.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">User</span>
                            </a>
                        </li>
                        @endperm
                        @perm('role.viewAny')
                        <li>
                            <a href="{{ route('role.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('role.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Role</span>
                            </a>
                        </li>
                        @endperm
                        @perm('setting.viewAny')
                        <li>
                            <a href="{{ route('setting.index') }}"
                                class="menu-item text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{
                                    'menu-item-active': '{{ request()->routeIs('setting.*') }}' === '1'
                                }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.325 4.317C10.751 2.561 13.249 2.561 13.675 4.317C13.956 5.522 15.522 5.799 16.217 4.782C17.313 3.212 19.687 4.343 18.783 6.217C18.079 7.201 18.356 8.767 19.561 9.048C21.317 9.474 21.317 11.972 19.561 12.398C18.356 12.679 18.079 14.245 18.783 15.229C19.687 17.103 17.313 18.234 16.217 16.664C15.522 15.647 13.956 15.924 13.675 17.129C13.249 18.885 10.751 18.885 10.325 17.129C10.044 15.924 8.478 15.647 7.783 16.664C6.687 18.234 4.313 17.103 5.217 15.229C5.921 14.245 5.644 12.679 4.439 12.398C2.683 11.972 2.683 9.474 4.439 9.048C5.644 8.767 5.921 7.201 5.217 6.217C4.313 4.343 6.687 3.212 7.783 4.782C8.478 5.799 10.044 5.522 10.325 4.317Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Setting</span>
                            </a>
                        </li>
                        @endperm
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</aside>
