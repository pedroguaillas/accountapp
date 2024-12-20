<script setup>

// Imports
import { ArrowRightOnRectangleIcon, Bars3Icon } from "@heroicons/vue/24/outline";
import { HomeIcon } from "@heroicons/vue/24/solid";
import { router, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Emits
defineEmits(['toggle']);

// Props
defineProps({
    menu: Boolean
});

const logout = () => {
    router.post(route('logout'));
};

const page = usePage()

const user = computed(() => page.props.auth.user)

</script>

<template>
    <header class="ease-out duration-300 w-full h-[60px] bg-slate-800 z-50 flex justify-between items-center px-4">
        <button @click="$emit('toggle')" class="cursor-pointer rounded border p-1">
            <Bars3Icon class="size-6 text-white transition-transform duration-300" />
        </button>
        <div class="flex gap-4">
            <Link :href="route('business.setting.roles')" class="cursor-pointer">
            <HomeIcon class="size-6 text-white transition-transform duration-300" />
            </Link>
            <div class="text-white flex">
                <h3>{{ user.username }}</h3>
                <button @click="logout">
                    <ArrowRightOnRectangleIcon class="size-6 text-white transition-transform duration-300" />
                </button>
            </div>
        </div>
    </header>
</template>