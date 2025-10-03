<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Emergency Profile</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div v-if="pinFlash" class="p-4 bg-green-50 border border-green-200 rounded">
                    <p class="text-sm text-green-700 font-medium">New PIN generated: <span class="font-mono text-lg">{{ pinFlash }}</span></p>
                    <p class="text-xs text-green-600 mt-1">Share this PIN with trusted responders only.</p>
                </div>

                <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Personal Details</h3>
                        <form class="mt-6 space-y-6" @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="first_name" value="First name" />
                                    <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" required autofocus />
                                    <InputError class="mt-2" :message="form.errors.first_name" />
                                </div>
                                <div>
                                    <InputLabel for="last_name" value="Last name" />
                                    <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.last_name" />
                                </div>
                                <div>
                                    <InputLabel for="date_of_birth" value="Date of birth" />
                                    <TextInput id="date_of_birth" v-model="form.date_of_birth" type="date" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.date_of_birth" />
                                </div>
                                <div>
                                    <InputLabel for="blood_type" value="Blood type" />
                                    <TextInput id="blood_type" v-model="form.blood_type" type="text" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.blood_type" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="allergies" value="Allergies" />
                                <textarea
                                    id="allergies"
                                    v-model="form.allergies"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.allergies" />
                            </div>

                            <div>
                                <InputLabel for="general_notes" value="Notes for responders" />
                                <textarea
                                    id="general_notes"
                                    v-model="form.general_notes"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="4"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.general_notes" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="primary_physician" value="Primary physician" />
                                    <TextInput id="primary_physician" v-model="form.primary_physician" type="text" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.primary_physician" />
                                </div>
                                <div>
                                    <InputLabel for="primary_physician_phone" value="Physician phone" />
                                    <TextInput id="primary_physician_phone" v-model="form.primary_physician_phone" type="text" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.primary_physician_phone" />
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <PrimaryButton :disabled="form.processing">Save changes</PrimaryButton>
                                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                    <p v-if="recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Emergency Card</h3>
                            <p class="mt-1 text-sm text-gray-600">This QR code links to your public emergency profile. Share or print it for your physical card.</p>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                            <div class="flex items-center gap-6">
                                <div v-if="qrDataUri" class="w-48 h-48 flex items-center justify-center bg-white border border-gray-200 rounded">
                                    <img :src="qrDataUri" alt="Emergency QR" class="w-full h-full object-contain" />
                                </div>
                                <div v-else class="w-48 h-48 flex items-center justify-center border border-dashed border-gray-300 text-gray-400">
                                    <span>No QR yet</span>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-sm text-gray-600">Card ID: <span class="font-medium text-gray-900">{{ card?.card_uuid ?? 'Pending' }}</span></p>
                                    <p class="text-sm text-gray-600">Status: <span class="font-medium capitalize">{{ card?.status ?? 'n/a' }}</span></p>
                                    <p v-if="pinSetAt" class="text-sm text-gray-600">PIN updated {{ formattedPinDate }}</p>
                                    <p v-if="publicUrl" class="text-xs text-gray-500 break-all">Shareable link: <a :href="publicUrl" class="text-indigo-600 hover:text-indigo-500">{{ publicUrl }}</a></p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <SecondaryButton type="button" @click="regenerateQr" :disabled="regeneratingQr">
                                    {{ regeneratingQr ? 'Updating…' : 'Rotate QR Token' }}
                                </SecondaryButton>
                                <DangerButton type="button" @click="regeneratePin" :disabled="regeneratingPin">
                                    {{ regeneratingPin ? 'Generating…' : 'Generate New PIN' }}
                                </DangerButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    profile: Object,
    card: Object,
    qrDataUri: String,
    pinSetAt: String,
    publicUrl: String,
});

const form = useForm({
    first_name: props.profile?.first_name ?? '',
    last_name: props.profile?.last_name ?? '',
    date_of_birth: props.profile?.date_of_birth ?? '',
    blood_type: props.profile?.blood_type ?? '',
    allergies: props.profile?.allergies ?? '',
    general_notes: props.profile?.general_notes ?? '',
    primary_physician: props.profile?.primary_physician ?? '',
    primary_physician_phone: props.profile?.primary_physician_phone ?? '',
});

watch(
    () => props.profile,
    (profile) => {
        form.reset({
            first_name: profile?.first_name ?? '',
            last_name: profile?.last_name ?? '',
            date_of_birth: profile?.date_of_birth ?? '',
            blood_type: profile?.blood_type ?? '',
            allergies: profile?.allergies ?? '',
            general_notes: profile?.general_notes ?? '',
            primary_physician: profile?.primary_physician ?? '',
            primary_physician_phone: profile?.primary_physician_phone ?? '',
        });
    }
);

const pinFlash = computed(() => usePage().props.flash?.pin ?? null);
const recentlySuccessful = computed(() => form.recentlySuccessful);

const pinSetAt = computed(() => props.pinSetAt);
const formattedPinDate = computed(() => {
    if (!pinSetAt.value) {
        return 'never';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(pinSetAt.value));
});

const regeneratingPin = ref(false);
const regeneratingQr = ref(false);

const submit = () => {
    form.patch(route('emergency.profile.update'));
};

const regeneratePin = () => {
    regeneratingPin.value = true;
    router.post(route('emergency.profile.pin'), {}, {
        preserveScroll: true,
        onFinish: () => {
            regeneratingPin.value = false;
        },
    });
};

const regenerateQr = () => {
    regeneratingQr.value = true;
    router.post(route('emergency.profile.qr'), {}, {
        preserveScroll: true,
        onFinish: () => {
            regeneratingQr.value = false;
        },
    });
};
</script>
