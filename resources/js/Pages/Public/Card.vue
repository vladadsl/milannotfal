<template>
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Notfall Emergency Profile</h1>
                <p class="mt-2 text-sm text-gray-600">Scan complete – review the patient's shared details below.</p>
            </div>

            <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Patient Overview</h2>
                    <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ fullName }}</dd>
                        </div>
                        <div v-if="profile?.blood_type">
                            <dt class="text-sm font-medium text-gray-500">Blood Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ profile?.blood_type }}</dd>
                        </div>
                        <div v-if="profile?.allergies">
                            <dt class="text-sm font-medium text-gray-500">Allergies</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ profile?.allergies }}</dd>
                        </div>
                        <div v-if="profile?.general_notes">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ profile?.general_notes }}</dd>
                        </div>
                    </dl>
                </div>

                <div v-if="profile?.contacts?.length" class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Emergency Contacts</h2>
                    <ul class="mt-4 space-y-4">
                        <li v-for="contact in profile.contacts" :key="contact.phone" class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ contact.name }}</p>
                                <p class="text-xs text-gray-600">{{ contact.relationship }}</p>
                            </div>
                            <a :href="`tel:${contact.phone}`" class="text-indigo-600 text-sm font-semibold">Call</a>
                        </li>
                    </ul>
                </div>

                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center justify-between">
                        <span>PIN-Protected Details</span>
                        <span v-if="pinVerified" class="text-xs font-medium text-green-600">Unlocked</span>
                    </h2>

                    <div v-if="!pinVerified" class="mt-4">
                        <p class="text-sm text-gray-600">Enter the 4-digit PIN from the physical card to view critical conditions and medications.</p>
                        <form class="mt-4 flex flex-col sm:flex-row gap-3" @submit.prevent="unlock">
                            <div class="sm:flex-1">
                                <label class="block text-sm font-medium text-gray-700" for="pin">PIN</label>
                                <input
                                    id="pin"
                                    v-model="pinForm.pin"
                                    type="password"
                                    inputmode="numeric"
                                    maxlength="4"
                                    pattern="[0-9]*"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono tracking-[0.5em] text-lg text-center"
                                    required
                                />
                                <p v-if="pinForm.errors.pin" class="mt-2 text-sm text-red-600">{{ pinForm.errors.pin }}</p>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                :disabled="pinForm.processing"
                            >
                                {{ pinForm.processing ? 'Checking…' : 'Unlock' }}
                            </button>
                        </form>
                    </div>

                    <div v-else class="mt-6 space-y-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Medical Conditions</h3>
                            <ul v-if="profile?.pin_protected?.conditions?.length" class="mt-3 space-y-3">
                                <li v-for="condition in profile.pin_protected.conditions" :key="condition.name" class="border border-gray-200 rounded p-3">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ condition.name }}</p>
                                        <span v-if="condition.is_critical" class="text-xs text-red-600 font-semibold uppercase">Critical</span>
                                    </div>
                                    <p v-if="condition.details" class="mt-2 text-sm text-gray-600">{{ condition.details }}</p>
                                </li>
                            </ul>
                            <p v-else class="mt-2 text-sm text-gray-500">No conditions on file.</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Medications</h3>
                            <ul v-if="profile?.pin_protected?.medications?.length" class="mt-3 space-y-3">
                                <li v-for="med in profile.pin_protected.medications" :key="med.name" class="border border-gray-200 rounded p-3">
                                    <p class="font-medium text-gray-900">{{ med.name }}</p>
                                    <p v-if="med.dosage" class="text-sm text-gray-600">Dosage: {{ med.dosage }}</p>
                                    <p v-if="med.frequency" class="text-sm text-gray-600">Frequency: {{ med.frequency }}</p>
                                    <p v-if="med.notes" class="text-sm text-gray-600 mt-2">{{ med.notes }}</p>
                                </li>
                            </ul>
                            <p v-else class="mt-2 text-sm text-gray-500">No medications on file.</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">If this is an emergency, call your local emergency number now.</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    card: Object,
    profile: Object,
    pinVerified: Boolean,
});

const fullName = computed(() => {
    if (!props.profile) {
        return 'Unknown Patient';
    }

    return `${props.profile.first_name ?? ''} ${props.profile.last_name ?? ''}`.trim();
});

const pinForm = useForm({
    pin: '',
});

const unlock = () => {
    pinForm.post(route('card.public.unlock', props.card.qr_token), {
        onSuccess: () => pinForm.reset(),
    });
};
</script>
