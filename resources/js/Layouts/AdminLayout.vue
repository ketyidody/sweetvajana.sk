<template>
  <div class="min-h-screen bg-muted/30">
    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border transform transition-transform duration-200 lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex items-center justify-between h-16 px-6 border-b border-border">
        <span class="text-lg font-medium">Sweet Vajana</span>
        <button class="lg:hidden" @click="sidebarOpen = false">
          <XIcon class="w-5 h-5" />
        </button>
      </div>

      <nav class="p-4 space-y-1">
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          :class="[
            'flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors',
            isActive(item.href)
              ? 'bg-primary text-primary-foreground'
              : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
          ]"
        >
          <component :is="item.icon" class="w-4 h-4" />
          {{ item.label }}
        </Link>

        <div class="pt-4 mt-4 border-t border-border space-y-1">
          <Link
            href="/"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors"
          >
            <ArrowLeftIcon class="w-4 h-4" />
            Back to Site
          </Link>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors w-full"
          >
            <LogOutIcon class="w-4 h-4" />
            Logout
          </Link>
        </div>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top bar -->
      <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-card border-b border-border sm:px-6">
        <button class="lg:hidden" @click="sidebarOpen = true">
          <MenuIcon class="w-5 h-5" />
        </button>
        <div class="flex-1" />
        <div class="flex items-center gap-3">
          <span class="text-sm text-muted-foreground">{{ $page.props.auth.user?.name }}</span>
        </div>
      </header>

      <!-- Flash messages -->
      <div v-if="$page.props.flash?.success" class="mx-4 mt-4 sm:mx-6">
        <div class="p-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm">
          {{ $page.props.flash.success }}
        </div>
      </div>
      <div v-if="$page.props.flash?.error" class="mx-4 mt-4 sm:mx-6">
        <div class="p-4 rounded-md bg-red-50 border border-red-200 text-red-800 text-sm">
          {{ $page.props.flash.error }}
        </div>
      </div>

      <!-- Page content -->
      <main class="p-4 sm:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  LayoutDashboard as DashboardIcon,
  FolderOpen as CategoriesIcon,
  Package as ProductsIcon,
  ShoppingCart as OrdersIcon,
  FileText as PagesIcon,
  Settings as SettingsIcon,
  Users as UsersIcon,
  ArrowLeft as ArrowLeftIcon,
  LogOut as LogOutIcon,
  Menu as MenuIcon,
  X as XIcon,
} from 'lucide-vue-next'

const page = usePage()
const sidebarOpen = ref(false)

const navItems = [
  { href: '/admin', label: 'Dashboard', icon: DashboardIcon },
  { href: '/admin/categories', label: 'Categories', icon: CategoriesIcon },
  { href: '/admin/products', label: 'Products', icon: ProductsIcon },
  { href: '/admin/orders', label: 'Orders', icon: OrdersIcon },
  { href: '/admin/pages', label: 'Pages', icon: PagesIcon },
  { href: '/admin/settings', label: 'Settings', icon: SettingsIcon },
  { href: '/admin/users', label: 'Users', icon: UsersIcon },
]

function isActive(href) {
  const url = page.url
  if (href === '/admin') return url === '/admin'
  return url.startsWith(href)
}
</script>
