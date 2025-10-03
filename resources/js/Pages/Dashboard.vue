<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    emergencyProfile: Object,
    emergencyCard: Object,
});

const cardStatus = computed(() => props.emergencyCard?.status ?? 'pending');

const pinUpdatedAt = computed(() => {
    const pinSetAt = usePage().props.auth?.user?.pin_set_at;

    if (!pinSetAt) {
        return null;
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(pinSetAt));
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Emergency readiness</h3>
                        <p class="mt-2 text-sm text-gray-600">Keep your emergency data current so responders get the right information when scanning your Notfall card.</p>

                        <dl class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="rounded border border-gray-200 p-4">
                                <dt class="text-sm font-medium text-gray-500">Card status</dt>
                                <dd class="mt-2 text-lg font-semibold text-gray-900 capitalize">{{ cardStatus }}</dd>
                                <p v-if="emergencyCard?.activated_at" class="mt-1 text-xs text-gray-500">Activated {{ new Date(emergencyCard.activated_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="rounded border border-gray-200 p-4">
                                <dt class="text-sm font-medium text-gray-500">PIN last updated</dt>
                                <dd class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ pinUpdatedAt ?? 'Not set yet' }}
                                </dd>
                            </div>
                            <div class="rounded border border-gray-200 p-4">
                                <dt class="text-sm font-medium text-gray-500">Profile completeness</dt>
                                <dd class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ emergencyProfile ? 'Ready' : 'Needs setup' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <Link
                                :href="route('emergency.profile.show')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Manage emergency profile
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
